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
    
    if (isset($request->poule) && is_int($request->poule)) {
        $poule = $request->poule;

        $host = $ini["DB_HOST"];
        $user = $ini["DB_USER"];
        $pass = $ini["DB_PASS"];
        $db = $ini["DB_NAME"];

        $conn = new mysqli($host, $user, $pass, $db);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Delete all games from this poule
        $sql = "DELETE FROM toernooi_wedstrijden WHERE poule_id = " . $poule;
        $conn->query($sql);

        // Get all teams from this poule
        $sql = "SELECT id FROM toernooi_teams WHERE poule_id = " . $poule;
        $result = $conn->query($sql);

        $team_ids = array();
        if ($result->num_rows > 0) {
            // output data of each row
            while($row = $result->fetch_assoc()) {
                $team_ids[] = $row["id"];
            }
        }

        // Bad request if there are less 3 teams
        if (count($team_ids) < 3) {
            header("HTTP/1.0 400 Bad Request");
            die("Poule does not exist or is not big enough");
        }
        
        // Create object for each team
        $teams = [];
        foreach ($team_ids as $team_id) {
            $teams[] = $team_id;
        }

        // Create key value pair for each team and rest
        $teams_rest = [];
        foreach ($team_ids as $team_id) {
            // Push new key value pair with team id as key and 0 as value
            $teams_rest[$team_id] = 0;
        }

        // Create an array with Refs for each team
        $teams_refs = [];
        foreach ($team_ids as $team_id) {
            // Push new Ref with team id as key and 0 as value
            $teams_refs[$team_id] = new Ref($team_id);
        }

        // Check if full is set in request and is boolean otherwise return bad request
        if (!isset($request->full) || !is_bool($request->full)) {
            header("HTTP/1.0 400 Bad Request");
            die("Field full is not set or is not boolean");
        }

        // Create array of all possible games
        $games = [];
        $spice = 0;
        if($request->full) {
            for ($i = 0; $i < count($teams); $i++) {
                for ($j = 0; $j < count($teams); $j++) {
                    if ($i != $j) {
                        $games[] = new Game($teams[$i], $teams[$j]);
                    }
                }
            }
        } else {
            for ($i = 0; $i < count($teams); $i++) {
                for ($j = $i + 1; $j < count($teams); $j++) {
                    if (($spice % 2) == 0) { // Spice up the game team order
                        $games[] = new Game($teams[$i], $teams[$j]);
                    } else {
                        $games[] = new Game($teams[$j], $teams[$i]);
                    }

                    $spice++;
                }
            }
        }

        // Var dump each individual game
        // echo "Games: <br>";
        // foreach ($games as $game) {
        //     echo "Game: " . $game->team1 . " vs " . $game->team2 . " @ " . $game->time . "<br>";
        // }
        // echo "<br>";

        // Create a priority queue of games with rest being the key
        $pq_game = new SplPriorityQueue();
        foreach ($games as $game) {
            $pq_game->insert($game, 0);
        }

        $sql = "";
        if (!isset($request->use_ref) || $request->use_ref == false) {
            $sql = "INSERT INTO toernooi_wedstrijden (poule_id, team1_id, team2_id, time, court_num) VALUES ";
        } else {
            $sql = "INSERT INTO toernooi_wedstrijden (poule_id, team1_id, team2_id, time, court_num, ref_id) VALUES ";
        }

        $time = Array();
        $interval = 0;
        if(isset($request->time) && is_string($request->time)) {
            if (preg_match("/^[0-9]{2}:[0-9]{2}$/", $request->time)) {
                if (isset($request->interval) && is_int($request->interval)) {
                    $interval = $request->interval;
                    $time = date_create($request->time);
                } else {
                    // Bad request if interval is not set
                    header("HTTP/1.0 400 Bad Request");
                    die("Interval is not set or is not integer"); 
                }
            } else {
                header("HTTP/1.0 400 Bad Request");
                die("Time is not in correct format: 'HH:MM'");
            }
        } else {
            $time = date_create("00:00");
        }      

        // echo "Prio: <br>";
        // Iterate through the priority queue without extracting and print out the games
        while ($pq_game->valid()) {
            $game = $pq_game->extract();

            // Loop through teams_ref to select a ref for the game
            $referee = null;
            {
                $team_rest = -1;
                foreach ($teams_refs as $team_ref) {
                    // Ref must not be in game already
                    if ($team_ref->team_id != $game->team1 && $team_ref->team_id != $game->team2) {
                        if ($referee != null) {

                            $game_rest = $teams_rest[$team_ref->team_id];

                            // If games are equal, select the one with the lowest rest
                            if ($team_ref->games == $referee->games) {
                                if ($team_ref->rest > $referee->rest) {
                                    $referee = $team_ref;
                                    $team_rest = $teams_rest[$team_ref->team_id];
                                } else if ($team_ref->rest == $referee->rest) {
                                    if ($team_rest < $game_rest) {
                                        $referee = $team_ref;
                                        $team_rest = $teams_rest[$team_ref->team_id];
                                    }
                                }
                            // If current team has less games
                            } else if ($team_ref->games < $referee->games) {
                                $referee = $team_ref;
                                $team_rest = $teams_rest[$team_ref->team_id];
                            }
                        } else {
                            $referee = $team_ref;
                            $team_rest = $teams_rest[$team_ref->team_id];
                        }
                    }
                }
            }
            
            // Loop through teams_ref and update the rest of the teams that are not referee and update the games of the referee
            foreach ($teams_refs as $team_ref) {
                if ($team_ref->team_id != $referee->team_id) {
                    $team_ref->rest += 1;
                } else {
                    $team_ref->rest = 0;
                    $team_ref->games += 1;
                }
            }

            // echo "Game: " . $game->team1 . " vs " . $game->team2 . " @ " . date_format($time, "H:i") . " Ref: " . $referee->team_id . "<br>";
            // echo rest of game
            // echo "Rest: " . $teams_rest[$game->team1] . " vs " . $teams_rest[$game->team2] . "<br>";

            // Insert game into database
            if (!isset($request->use_ref) || $request->use_ref == false) {
                $sql .= "(" . $poule . ", " . $game->team1 . ", " . $game->team2 . ", '" . date_format($time, "H:i") . "', " . $request->court . "),";
            } else {
                $sql .= "(" . $poule . ", " . $game->team1 . ", " . $game->team2 . ", '" . date_format($time, "H:i") . "', " . $request->court . ", " . $referee->team_id . "),";
            }

            // Increment time by interval
            $time->add(date_interval_create_from_date_string($interval . " minutes"));
            

            // iterate through teams and update rest
            foreach ($teams as $team) {
                if($team == $game->team1 || $team == $game->team2) {
                    $teams_rest[$team] = 0;
                } else {
                    $teams_rest[$team]++;
                }
            }

            // echo all teams and rest
            // echo "Teams: <br>";
            // foreach ($teams as $team) {
            //     echo $team . ": " . $teams_rest[$team] . "<br>";
            // }
            // echo "<br>";
            
            // echo all teams and rest
            // echo "Games: <br>";

            // Create temporary priority queue for pq_game and update rest
            $t_pq_game = new SplPriorityQueue();
            while ($pq_game->valid()) {
                $t_game = $pq_game->extract();

                $key = $teams_rest[$t_game->team1] + $teams_rest[$t_game->team2];

                // echo "Game: " . $t_game->team1 . " vs " . $t_game->team2  . " - key: " . $key . "<br>";

                $t_pq_game->insert($t_game, $key);
            }
            // echo "<br>";
            
            // Set pq_game to temporary priority queue
            $pq_game = $t_pq_game;
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
    } else {
        header("HTTP/1.0 400 Bad Request");
        die("Poule is not set or is not integer");
    }
?>