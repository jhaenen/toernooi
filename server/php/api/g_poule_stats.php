<?php
    // Allow from any origin
    if (isset($_SERVER['HTTP_ORIGIN'])) {
        // Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
        // you want to allow, and if so:
        header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400');    // cache for 1 day
    }

    // Return method not allowed if not GET
    if ($_SERVER['REQUEST_METHOD'] != 'GET') {
        header('HTTP/1.0 405 Method Not Allowed');
        die();
    }

    $ini = parse_ini_file("../env.ini");

    $host = $ini["DB_HOST"];
    $user = $ini["DB_USER"];
    $pass = $ini["DB_PASS"];
    $db = $ini["DB_NAME"];

    $conn = new mysqli($host, $user, $pass, $db);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    } 

    $poule_id = null;

    if(isset($_GET['p'])) { 
        $poule_id = $_GET['p'];
    }    

    $sql = "SELECT * FROM toernooi_stats WHERE poule_id = " . $poule_id . " ORDER BY points DESC, score_diff DESC, score_for DESC, id ASC;";
    $stat_result = $conn->query($sql);

    $stats = array();

    if ($stat_result->num_rows > 0) {
        // output data of each row
        while($row = $stat_result->fetch_assoc()) {
            $stats[] = new \stdClass();
            $index = count($stats) - 1;

            $stats[$index]->id = intval($row["id"]);
            $stats[$index]->name = base64_decode($row["name"]);
            $stats[$index]->poule_id = intval($row["poule_id"]);
            $stats[$index]->played = intval($row["played"]);
            $stats[$index]->won = intval($row["won"]);
            $stats[$index]->lost = intval($row["lost"]);
            $stats[$index]->points = intval($row["points"]);
            $stats[$index]->score_for = intval($row["score_for"]);
            $stats[$index]->score_against = intval($row["score_against"]);
            $stats[$index]->score_diff = intval($row["score_diff"]);
        }
    }

    $sql = "SELECT * FROM toernooi_wed_name WHERE poule_id = " . $poule_id . " ORDER BY time ASC; ";
    $game_result = $conn->query($sql);

    $games = array();

    if ($game_result->num_rows > 0) {
        // output data of each row
        while($row = $game_result->fetch_assoc()) {
            $games[] = new \stdClass();
            $index = count($games) - 1;

            $games[$index]->id = intval($row["id"]);

            $games[$index]->poule = new \stdClass();
            $games[$index]->poule->id = intval($row["poule_id"]);
            $games[$index]->poule->name = base64_decode($row["poule_name"]);
            $games[$index]->poule->color = $row["poule_color"];

            $games[$index]->team1 = new \stdClass();
            $games[$index]->team1->id = intval($row["team1_id"]);
            $games[$index]->team1->name = base64_decode($row["team1_name"]);

            $games[$index]->team2 = new \stdClass();
            $games[$index]->team2->id = intval($row["team2_id"]);
            $games[$index]->team2->name = base64_decode($row["team2_name"]);

            $games[$index]->team1->score = intval($row["score1"]);
            $games[$index]->team2->score = intval($row["score2"]);

            $games[$index]->time = $row["time"];

            $games[$index]->court_num = $row["court_num"];

            if($row["ref_id"] == null) {
                $games[$index]->ref = null;
            } else {
                $games[$index]->ref = new \stdClass();
                $games[$index]->ref->id = $row["ref_id"];
                $games[$index]->ref->name = base64_decode($row["ref_name"]);
            }
            
        }
    }

    $sql = "SELECT * FROM toernooi_poules WHERE id = " . $poule_id;
    $poule_result = $conn->query($sql);

    $poule = new \stdClass();

    if ($poule_result->num_rows == 1) {
        $row = $poule_result->fetch_assoc();

        $poule->id = intval($row["id"]);
        $poule->name = base64_decode($row["name"]);
        $poule->color = $row["color"];
    }

    $conn->close();

    // Set content type to json
    header('Content-Type: application/json');

    $resp = new \stdClass();
    $resp->poule = $poule;
    $resp->stats = $stats;
    $resp->games = $games;

    die(json_encode($resp));
?>