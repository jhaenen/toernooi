<?php 
    Class Game {
        public $team1 = -1;
        public $team2 = -1;
        public $time = "00:00";

        public function __construct($team1, $team2, $time) {
            $this->team1 = $team1;
            $this->team2 = $team2;
            $this->time = $time;
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

        $ini = parse_ini_file("../env.ini");

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

        // Bad request if there are no teams
        if (count($team_ids) == 0) {
            header("HTTP/1.0 400 Bad Request");
            die();
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

        // Create array of all possible games
        $games = [];
        $spice = 0;
        for ($i = 0; $i < count($teams); $i++) {
            for ($j = $i + 1; $j < count($teams); $j++) {
                if (($spice % 2) == 0) { // Spice up the game team order
                    $games[] = new Game($teams[$i], $teams[$j], "00:00");
                } else {
                    $games[] = new Game($teams[$j], $teams[$i], "00:00");
                }

                $spice++;
            }
        }

        // Var dump each individual game
        // echo "Games: <br>";
        // foreach ($games as $game) {
        //     echo "Game: " . $game->team1 . " vs " . $game->team2 . " @ " . $game->time . "<br>";
        // }
        // echo "<br>";

        // Create a priority queue of games with rest being the key
        $pq = new SplPriorityQueue();
        foreach ($games as $game) {
            $pq->insert($game, 0);
        }

        $sql = "";
        if (!isset($request->court) || !is_int($request->court)) {
            $sql = "INSERT INTO toernooi_wedstrijden (poule_id, team1_id, team2_id, time) VALUES ";
        } else {
            $sql = "INSERT INTO toernooi_wedstrijden (poule_id, team1_id, team2_id, time, court_num) VALUES ";
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
                    die();
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
        while ($pq->valid()) {
            $game = $pq->extract();
            // echo "Game: " . $game->team1 . " vs " . $game->team2 . " @ " . date_format($time, "H:i") . "<br>";
            // echo rest of game
            // echo "Rest: " . $teams_rest[$game->team1] . " vs " . $teams_rest[$game->team2] . "<br>";

            // Insert game into database
            if (!isset($request->court) || !is_int($request->court)) {
                $sql .= "(" . $poule . ", " . $game->team1 . ", " . $game->team2 . ", '" . date_format($time, "H:i") . "'),";
            } else {
                $sql .= "(" . $poule . ", " . $game->team1 . ", " . $game->team2 . ", '" . date_format($time, "H:i") . "', " . $request->court . "),";
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
            $t_pq = new SplPriorityQueue();
            while ($pq->valid()) {
                $t_game = $pq->extract();

                $key = $teams_rest[$t_game->team1] + $teams_rest[$t_game->team2];

                // echo "Game: " . $t_game->team1 . " vs " . $t_game->team2  . " - key: " . $key . "<br>";

                $t_pq->insert($t_game, $key);
            }
            // echo "<br>";
            
            $pq = $t_pq;
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
        die();
    }
?>