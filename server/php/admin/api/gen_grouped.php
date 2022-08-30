<?php 
    $ini = parse_ini_file("../../env.ini");

    if ($ini["ENV_MODE"] != "DEV") {
        include "guard.php";
    }

    Class Game {
        public $team1 = -1;
        public $team2 = -1;
        public $time = "00:00";
        public $ref = -1;

        public function __construct($team1, $team2) {
            $this->team1 = $team1;
            $this->team2 = $team2;
            $this->time = "00:00";
            $this->ref = -1;
        }
    }

    Class Ref {
        public $team_id = -1;
        public $games = 0;
        public $rest = 0;

        public function __construct($team_id) {
            $this->team_id = $team_id;
            $this->games = 0;
            $this->rest = 0;
        }
    }

    // Return method POST allowed on OPTIONS request
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');
        header('Content-Type: application/json');
        header('Access-Control-Max-Age: 1728000');
        header('Content-Length: 0');
        header('Connection: close');
        die();
    }

    // Return method not allowed if not POST
    if ($_SERVER['REQUEST_METHOD'] != 'POST') {
        header('HTTP/1.0 405 Method Not Allowed');
        die();
    }

    // Decode body of request
    $request = json_decode(file_get_contents('php://input'));

    // Set content type to json
    header('Content-Type: application/json');

    // Check if poule1 and poule2 are set and are integers
    if (!isset($request->poule1) || !is_int($request->poule1) || !isset($request->poule2) || !is_int($request->poule2)) {
        header('HTTP/1.0 400 Bad Request');
        die("{\"msg\":\"Poule 1 of poule 2 niet gezet of niet integer\"}");
    }

    // Check if court is set and is integer
    if (!isset($request->court1) || !is_int($request->court1) || !isset($request->court2) || !is_int($request->court2)) {
        header('HTTP/1.0 400 Bad Request');
        die("{\"msg\":\"Veld 1 of veld 2 niet gezet of niet integer\"}");
    }

    // Check if time is set and is string
    if (!isset($request->time) || !is_string($request->time)) {
        header('HTTP/1.0 400 Bad Request');
        die("{\"msg\":\"Tijd niet gezet of niet string\"}");
    }

    if (!preg_match("/^[0-9]{2}:[0-9]{2}$/", $request->time)) {
        header('HTTP/1.0 400 Bad Request');
        die("{\"msg\":\"Tijd niet in juiste formaat HH:MM\"}");
    }

    // Check if interval is set and is integer
    if (!isset($request->interval) || !is_int($request->interval)) {
        header('HTTP/1.0 400 Bad Request');
        die("{\"msg\":\"Interval niet gezet of niet integer\"}");
    }

    $host = $ini["DB_HOST"];
    $user = $ini["DB_USER"];
    $pass = $ini["DB_PASS"];
    $db = $ini["DB_NAME"];

    $conn = new mysqli($host, $user, $pass, $db);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $poule1 = $request->poule1;
    $poule2 = $request->poule2;

    // Check if poule1 and poule2 are different
    if ($poule1 == $poule2) {
        header('HTTP/1.0 400 Bad Request');
        die("{\"msg\":\"Poule 1 en poule 2 zijn hetzelfde\"}");
    }

    // Check if poule1 and poule2 are valid
    $sql = "SELECT * FROM toernooi_poules WHERE id = $poule1 OR id = $poule2";
    $result = $conn->query($sql);

    if ($result->num_rows != 2) {
        header('HTTP/1.0 400 Bad Request');
        die("{\"msg\":\"Poule 1 en poule 2 is niet geldig\"}");
    }

    // Delete all games from this poule
    $sql = "DELETE FROM toernooi_wedstrijden WHERE (poule_id = " . $poule1 . " OR poule_id = " . $poule2 . ");";
    $conn->query($sql);

    // Get all teams from this poule
    $sql = "SELECT id, poule_id FROM toernooi_teams WHERE (poule_id = " . $poule1 . " OR poule_id = " . $poule2 . ")";
    $result = $conn->query($sql);

    $team1_ids = array();
    $team2_ids = array();
    if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
            if($row["poule_id"] == $poule1) {
                $team1_ids[] = $row["id"];
            } else {
                $team2_ids[] = $row["id"];
            }
        }
    }

    // Bad request if there are less 3 teams
    if (count($team1_ids) < 3 || count($team2_ids) < 3) {
        header("HTTP/1.0 400 Bad Request");
        die("Poules moeten minstens 3 teams bevatten");
    }

    if (max(count($team1_ids), count($team2_ids)) < 6) {
        header("HTTP/1.0 400 Bad Request");
        die("Grootste poule moet minstens 6 teams bevatten");
    }

    // Create key value pair for each team and rest
    $teams_rest = [];
    foreach ($team1_ids as $team_id) {
        // Push new key value pair with team id as key and 0 as value
        $teams_rest[$team_id] = 0;
    }
    foreach ($team2_ids as $team_id) {
        // Push new key value pair with team id as key and 0 as value
        $teams_rest[$team_id] = 0;
    }

    // Create an array with Refs for each team
    $teams1_refs = [];
    $teams2_refs = [];
    foreach ($team1_ids as $team_id) {
        // Push new Ref with team id as key and 0 as value
        $teams1_refs[$team_id] = new Ref($team_id);
    }
    foreach ($team2_ids as $team_id) {
        // Push new Ref with team id as key and 0 as value
        $teams2_refs[$team_id] = new Ref($team_id);
    }

    // Create array of all possible games for teams1
    $games1 = [];
    $spice = 0;
    for ($i = 0; $i < count($team1_ids); $i++) {
        for ($j = $i + 1; $j < count($team1_ids); $j++) {
            if (($spice % 2) == 0) { // Spice up the game team order
                $games1[] = new Game($team1_ids[$i], $team1_ids[$j]);
            } else {
                $games1[] = new Game($team1_ids[$j], $team1_ids[$i]);
            }

            $spice++;
        }
    }

    // Create array of all possible games for teams2
    $games2 = [];
    $spice = 0;
    for ($i = 0; $i < count($team2_ids); $i++) {
        for ($j = $i + 1; $j < count($team2_ids); $j++) {
            if (($spice % 2) == 0) { // Spice up the game team order
                $games2[] = new Game($team2_ids[$i], $team2_ids[$j]);
            } else {
                $games2[] = new Game($team2_ids[$j], $team2_ids[$i]);
            }

            $spice++;
        }
    }

    $num_games1 = count($games1);
    $num_games2 = count($games2);

    $court1_games = [];
    $court2_games = [];

    if ($num_games1 > $num_games2) {
        $total = count($games1) + count($games2);

        $court2_num = floor($total / 2);
        $court1_num = $total - $court2_num;

        $carry_over = $court2_num - $num_games2;
        $insert_int = floor($num_games2 / $carry_over);

        // echo "Court 1 num: " . $court1_num . " Court 2 num: " . $court2_num . " Carry over: " . $carry_over . " Insert int: " . $insert_int . "<br>";

        for ($i = 0; $i < $court1_num; $i++) {
            $court1_games[] = $poule1;
            if ($i < $court2_games) {
                $insert_index = floor($insert_int / 2);

                if ($i % ($insert_int + 1) == $insert_index) {
                    $court2_games[] = $poule1;
                } else {
                    $court2_games[] = $poule2;
                }
            }
        }
    } else if ($num_games1 < $num_games2) {
        $total = count($games1) + count($games2);

        $court1_num = floor($total / 2);
        $court2_num = $total - $court1_num;

        $carry_over = $court1_num - $num_games1;
        $insert_int = floor($num_games1 / $carry_over);

        // echo "Court 1 num: " . $court1_num . " Court 2 num: " . $court2_num . " Carry over: " . $carry_over . " Insert int: " . $insert_int . "<br>";

        for ($i = 0; $i < $court2_num; $i++) {
            $court2_games[] = $poule2;
            if ($i < $court1_games) {
                $insert_index = floor($insert_int / 2);

                if ($i % ($insert_int + 1) == $insert_index) {
                    $court1_games[] = $poule2;
                } else {
                    $court1_games[] = $poule1;
                }
            }
        }
    } else {
        $court1_num = $num_games1;
        $court2_num = $num_games2;

        // echo "Court 1 num: " . $court1_num . " Court 2 num: " . $court2_num . "<br>";

        for ($i = 0; $i < $num_games1; $i++) {
            $court1_games[] = $poule1;
            $court2_games[] = $poule2;
        }
    }

    // Create a priority queue of games with rest being the key
    $pq_games1 = new SplPriorityQueue();
    foreach ($games1 as $game) {
        $pq_games1->insert($game, 0);
    }

    // Create a priority queue of games with rest being the key
    $pq_games2 = new SplPriorityQueue();
    foreach ($games2 as $game) {
        $pq_games2->insert($game, 0);
    }

    $sql = "INSERT INTO toernooi_wedstrijden (poule_id, team1_id, team2_id, time, court_num, ref_id) VALUES ";
    
    $interval = $request->interval;
    $time = date_create($request->time);
  
    $max_games = max($court1_num, $court2_num);
    for ($round = 0; $round < $max_games; $round++) {
        // echo "Round: " . ($round+1) . "<br>";

        $game1 = null;
        $game2 = null;

        if ($round < $court1_num) {   
            if ($court1_games[$round] == $poule1) {
                $game1 = $pq_games1->extract();
            } else {
                $game1 = $pq_games2->extract();
            }
        }

        {
            $rejectStack = new SplStack();

            if ($round < $court2_num) {   
                do {
                    if ($court2_games[$round] == $poule1 && !$pq_games1->valid()) {
                        // Internal server error if no games left in queue
                        header("HTTP/1.0 500 Internal Server Error");
                        die("Error in scheduling");
                    } else if ($court2_games[$round] == $poule2 && !$pq_games2->valid()) {
                        header("HTTP/1.0 500 Internal Server Error");
                        die("Error in scheduling");
                    }

                    if ($court2_games[$round] == $poule1) {
                        $rejectStack->push($pq_games1->extract());
                    } else {
                        $rejectStack->push($pq_games2->extract());
                    }
                } while($rejectStack->top()->team1 == $game1?->team1 || 
                        $rejectStack->top()->team2 == $game1?->team1 || 
                        $rejectStack->top()->team1 == $game1?->team2 || 
                        $rejectStack->top()->team2 == $game1?->team2);

                $game2 = $rejectStack->pop();

                while($rejectStack->count() > 0) {
                    $tmp = $rejectStack->pop();
                    $key = $teams_rest[$tmp->team1] + $teams_rest[$tmp->team2];

                    if ($court2_games[$round] == $poule1) {
                        $pq_games1->insert($tmp, $key);
                    } else {
                        $pq_games2->insert($tmp, $key);
                    }
                }
            }
        }

        if($game1 != null) {
            // Loop through teams_ref to select a ref for the game
            $referee1 = null;
            {
                if ($court1_games[$round] == $poule1) {
                    $teams_refs = $teams1_refs;
                } else {
                    $teams_refs = $teams2_refs;
                }

                $team_rest = -1;
                foreach ($teams_refs as $team_ref) {
                    // Ref must not be in game already
                    if ($team_ref->team_id != $game1->team1 && $team_ref->team_id != $game1->team2 && $team_ref->team_id != $game2->team1 && $team_ref->team_id != $game2->team2) {
                        if ($referee1 != null) {

                            $game_rest = $teams_rest[$team_ref->team_id];

                            // If games are equal, select the one with the lowest rest
                            if ($team_ref->games == $referee1->games) {
                                if ($team_ref->rest > $referee1->rest) {
                                    $referee1 = $team_ref;
                                    $team_rest = $teams_rest[$team_ref->team_id];
                                } else if ($team_ref->rest == $referee1->rest) {
                                    if ($team_rest < $game_rest) {
                                        $referee1 = $team_ref;
                                        $team_rest = $teams_rest[$team_ref->team_id];
                                    }
                                }
                            // If current team has less games
                            } else if ($team_ref->games < $referee1->games) {
                                $referee1 = $team_ref;
                                $team_rest = $teams_rest[$team_ref->team_id];
                            }
                        } else {
                            $referee1 = $team_ref;
                            $team_rest = $teams_rest[$team_ref->team_id];
                        }
                    }
                }

                // Loop through teams_ref and update the rest of the teams that are not referee and update the games of the referee
                foreach ($teams_refs as $team_ref) {
                    if ($team_ref->team_id != $referee1->team_id) {
                        $team_ref->rest += 1;
                    } else {
                        $team_ref->rest = 0;
                        $team_ref->games += 1;
                    }
                }
            }
        }

        if ($game2 != null) {
            // Loop through teams_ref to select a ref for the game
            $referee2 = null;
            {
                if ($court2_games[$round] == $poule1) {
                    $teams_refs = $teams1_refs;
                } else {
                    $teams_refs = $teams2_refs;
                }

                $team_rest = -1;
                foreach ($teams_refs as $team_ref) {
                    // Ref must not be in game already
                    if ($team_ref->team_id != $game1?->team1 && $team_ref->team_id != $game1?->team2 && $team_ref->team_id != $game2?->team1 && $team_ref->team_id != $game2?->team2) {
                        // Referee must not be the same as the first one
                        if ($referee1->team_id != $team_ref->team_id) {
                            if ($referee2 != null) {

                                $game_rest = $teams_rest[$team_ref->team_id];

                                // If games are equal, select the one with the lowest rest
                                if ($team_ref->games == $referee2->games) {
                                    if ($team_ref->rest > $referee2->rest) {
                                        $referee2 = $team_ref;
                                        $team_rest = $teams_rest[$team_ref->team_id];
                                    } else if ($team_ref->rest == $referee2->rest) {
                                        if ($team_rest < $game_rest) {
                                            $referee2 = $team_ref;
                                            $team_rest = $teams_rest[$team_ref->team_id];
                                        }
                                    }
                                // If current team has less games
                                } else if ($team_ref->games < $referee2->games) {
                                    $referee2 = $team_ref;
                                    $team_rest = $teams_rest[$team_ref->team_id];
                                }
                            } else {
                                $referee2 = $team_ref;
                                $team_rest = $teams_rest[$team_ref->team_id];
                            }
                        }
                    }
                }

                // Loop through teams_ref and update the rest of the teams that are not referee and update the games of the referee
                foreach ($teams_refs as $team_ref) {
                    if ($team_ref->team_id != $referee2->team_id) {
                        $team_ref->rest += 1;
                    } else {
                        $team_ref->rest = 0;
                        $team_ref->games += 1;
                    }
                }
            }
        }

        // Set game times
        if ($game1 != null) {
            $game1->time = $time->format("H:i");
            $sql .= "(" . $court1_games[$round] . ", " . $game1->team1 . ", " . $game1->team2 . ", '" . $game1->time . "', " . $request->court1 . ", " . $referee1->team_id . "),";

            // echo games
            // echo "Court 1: " . $game1->team1 . " - " . $game1->team2 . " @ " . $game1->time . " Ref: " . $referee1->team_id . "<br>";
        }
        if ($game2 != null) {
            $game2->time = $time->format("H:i");
            $sql .= "(" . $court2_games[$round] . ", " . $game2->team1 . ", " . $game2->team2 . ", '" . $game2->time . "', " . $request->court2 . ", " . $referee2->team_id . "),";

            // echo games
            // echo "Court 2: " . $game2->team1 . " - " . $game2->team2 . " @ " . $game2->time . " Ref: " . $referee2->team_id . "<br>";
        }

        $time->add(date_interval_create_from_date_string($interval . " minutes"));        

        // iterate through teams and update rest
        foreach ($team1_ids as $team) {
            if($team == $game1?->team1 || $team == $game1?->team2 || $team == $game2?->team1 || $team == $game2?->team2) {
                $teams_rest[$team] = 0;
            } else {
                $teams_rest[$team]++;
            }
        }

        // iterate through teams and update rest
        foreach ($team2_ids as $team) {
            if($team == $game1?->team1 || $team == $game1?->team2 || $team == $game2?->team1 || $team == $game2?->team2) {
                $teams_rest[$team] = 0;
            } else {
                $teams_rest[$team]++;
            }
        }
        
        // Create temporary priority queue for pq_game and update rest
        $t_pq_game = new SplPriorityQueue();
        while ($pq_games1->valid()) {
            $t_game = $pq_games1->extract();

            $key = $teams_rest[$t_game->team1] + $teams_rest[$t_game->team2];

            $t_pq_game->insert($t_game, $key);
        }
        // Set pq_game to temporary priority queue
        $pq_games1 = $t_pq_game;

        // Create temporary priority queue for pq_game and update rest
        $t_pq_game = new SplPriorityQueue();
        while ($pq_games2->valid()) {
            $t_game = $pq_games2->extract();

            $key = $teams_rest[$t_game->team1] + $teams_rest[$t_game->team2];

            $t_pq_game->insert($t_game, $key);
        }
        // Set pq_game to temporary priority queue
        $pq_games2 = $t_pq_game;
        
        // echo "<br>";
    }

    // Perform the insert query
    $sql = substr($sql, 0, -1);
    $conn->query($sql);

    // Print query error
    if ($conn->error) {
        echo "Error: " . $conn->error;
    }

    // echo "<br>";

    $conn->close();
?>