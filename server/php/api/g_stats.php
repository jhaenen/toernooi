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

    $sql = "";
    $stats = array();
    if ($poule_id == null) {
        $sql = "SELECT * FROM toernooi_poules";

        // Get all poules
        $result = $conn->query($sql);

        $index_map = array();

        // Create an new object for each poule with id and an new standings array
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $stats[] = new \stdClass();
                $index = count($stats) - 1;

                $stats[$index]->id = $row["id"];
                $stats[$index]->name = base64_decode($row["name"]);
                $stats[$index]->color = $row["color"];
                $stats[$index]->standings = array();

                $index_map[$row["id"]] = $index;
            }
        }

        $sql = "SELECT * FROM toernooi_stats ORDER BY poule_id ASC, points DESC, score_diff DESC;";

        $result = $conn->query($sql);

        // Place each team's stats in the correct poule
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $poule_index = $index_map[$row["poule_id"]];

                $stats[$poule_index]->standings[] = new \stdClass();
                $index = count($stats[$poule_index]->standings) - 1;

                $stats[$poule_index]->standings[$index]->id = intval($row["id"]);
                $stats[$poule_index]->standings[$index]->name = base64_decode($row["name"]);
                $stats[$poule_index]->standings[$index]->poule_id = intval($row["poule_id"]);
                $stats[$poule_index]->standings[$index]->played = intval($row["played"]);
                $stats[$poule_index]->standings[$index]->won = intval($row["won"]);
                $stats[$poule_index]->standings[$index]->lost = intval($row["lost"]);
                $stats[$poule_index]->standings[$index]->points = intval($row["points"]);
                $stats[$poule_index]->standings[$index]->score_for = intval($row["score_for"]);
                $stats[$poule_index]->standings[$index]->score_against = intval($row["score_against"]);
                $stats[$poule_index]->standings[$index]->score_diff = intval($row["score_diff"]);
            }
        }
    } else {
        $sql = "SELECT * FROM toernooi_stats WHERE poule_id = " . $poule_id . " ORDER BY points DESC, score_diff DESC, score_for DESC;";

        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            // output data of each row
            while($row = $result->fetch_assoc()) {
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
    }

    $conn->close();

    // Set content type to json
    header('Content-Type: application/json');

    die(json_encode($stats));
?>