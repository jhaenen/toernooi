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

    $sql = "SELECT * FROM toernooi_wed_name ORDER BY time ASC, court_num ASC; ";

    $result = $conn->query($sql);

    $groups = array();

    $curTime = null;
    $groupIndex = 0;

    if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
            
            if ($row["time"] != $curTime) {
                $curTime = $row["time"];

                $groups[] = new \stdClass();
                $groupIndex = count($groups) - 1;

                $groups[$groupIndex]->time = $row["time"];
                $groups[$groupIndex]->games = array();
            }
            
            $groups[$groupIndex]->games[] = new \stdClass();
            $gameIndex = count($groups[$groupIndex]->games) - 1;

            $groups[$groupIndex]->games[$gameIndex]->id = intval($row["id"]);

            $groups[$groupIndex]->games[$gameIndex]->poule = new \stdClass();
            $groups[$groupIndex]->games[$gameIndex]->poule->id = intval($row["poule_id"]);
            $groups[$groupIndex]->games[$gameIndex]->poule->name = base64_decode($row["poule_name"]);
            $groups[$groupIndex]->games[$gameIndex]->poule->color = $row["poule_color"];

            $groups[$groupIndex]->games[$gameIndex]->team1 = new \stdClass();
            $groups[$groupIndex]->games[$gameIndex]->team1->id = intval($row["team1_id"]);
            $groups[$groupIndex]->games[$gameIndex]->team1->name = base64_decode($row["team1_name"]);

            $groups[$groupIndex]->games[$gameIndex]->team2 = new \stdClass();
            $groups[$groupIndex]->games[$gameIndex]->team2->id = intval($row["team2_id"]);
            $groups[$groupIndex]->games[$gameIndex]->team2->name = base64_decode($row["team2_name"]);

            $groups[$groupIndex]->games[$gameIndex]->team1->score = intval($row["score1"]);
            $groups[$groupIndex]->games[$gameIndex]->team2->score = intval($row["score2"]);

            $groups[$groupIndex]->games[$gameIndex]->time = $row["time"];

            $groups[$groupIndex]->games[$gameIndex]->court_num = $row["court_num"];

            $groups[$groupIndex]->games[$gameIndex]->ref = new \stdClass();
            $groups[$groupIndex]->games[$gameIndex]->ref->id = intval($row["ref_id"]);
            $groups[$groupIndex]->games[$gameIndex]->ref->name = base64_decode($row["ref_name"]);

        }
    }

    $conn->close();

    // Set content type to json
    header('Content-Type: application/json');

    die(json_encode($groups));
?>