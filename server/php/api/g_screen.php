<?php
    // Allow from any origin
    if (isset($_SERVER['HTTP_ORIGIN'])) {
        header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400');
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

    $response = new \stdClass();

    // Latest results — most recent first, one per court
    $sql = "SELECT * FROM toernooi_latest ORDER BY id DESC LIMIT 4";
    $result = $conn->query($sql);

    $response->latest = array();

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $response->latest[] = new \stdClass();
            $index = count($response->latest) - 1;

            $response->latest[$index]->id = intval($row["id"]);

            $response->latest[$index]->poule = new \stdClass();
            $response->latest[$index]->poule->id = intval($row["poule_id"]);
            $response->latest[$index]->poule->name = base64_decode($row["poule_name"]);
            $response->latest[$index]->poule->color = $row["poule_color"];

            $response->latest[$index]->team1 = new \stdClass();
            $response->latest[$index]->team1->id = intval($row["team1_id"]);
            $response->latest[$index]->team1->name = base64_decode($row["team1_name"]);

            $response->latest[$index]->team2 = new \stdClass();
            $response->latest[$index]->team2->id = intval($row["team2_id"]);
            $response->latest[$index]->team2->name = base64_decode($row["team2_name"]);

            $response->latest[$index]->team1->score = intval($row["score1"]);
            $response->latest[$index]->team2->score = intval($row["score2"]);

            $response->latest[$index]->time = $row["time"];
            $response->latest[$index]->court_num = $row["court_num"];

            if ($row["ref_id"] == null) {
                $response->latest[$index]->ref = null;
            } else {
                $response->latest[$index]->ref = new \stdClass();
                $response->latest[$index]->ref->id = $row["ref_id"];
                $response->latest[$index]->ref->name = base64_decode($row["ref_name"]);
            }
        }
    }

    // Next games — earliest unplayed games
    $sql = "SELECT * FROM toernooi_wed_name
            WHERE score1 = 0 AND score2 = 0
            ORDER BY time ASC, court_num ASC
            LIMIT 4";

    $response->next = array();

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $response->next[] = new \stdClass();
            $index = count($response->next) - 1;

            $response->next[$index]->id = intval($row["id"]);

            $response->next[$index]->poule = new \stdClass();
            $response->next[$index]->poule->id = intval($row["poule_id"]);
            $response->next[$index]->poule->name = base64_decode($row["poule_name"]);
            $response->next[$index]->poule->color = $row["poule_color"];

            $response->next[$index]->team1 = new \stdClass();
            $response->next[$index]->team1->id = intval($row["team1_id"]);
            $response->next[$index]->team1->name = base64_decode($row["team1_name"]);

            $response->next[$index]->team2 = new \stdClass();
            $response->next[$index]->team2->id = intval($row["team2_id"]);
            $response->next[$index]->team2->name = base64_decode($row["team2_name"]);

            $response->next[$index]->team1->score = intval($row["score1"]);
            $response->next[$index]->team2->score = intval($row["score2"]);

            $response->next[$index]->time = $row["time"];
            $response->next[$index]->court_num = $row["court_num"];

            if ($row["ref_id"] == null) {
                $response->next[$index]->ref = null;
            } else {
                $response->next[$index]->ref = new \stdClass();
                $response->next[$index]->ref->id = $row["ref_id"];
                $response->next[$index]->ref->name = base64_decode($row["ref_name"]);
            }
        }
    }

    // Standings per poule
    $response->stats = array();

    $sql = "SELECT * FROM toernooi_poules";
    $result = $conn->query($sql);

    $index_map = array();

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $response->stats[] = new \stdClass();
            $index = count($response->stats) - 1;

            $response->stats[$index]->id = $row["id"];
            $response->stats[$index]->name = base64_decode($row["name"]);
            $response->stats[$index]->color = $row["color"];
            $response->stats[$index]->standings = array();

            $index_map[$row["id"]] = $index;
        }
    }

    $sql = "SELECT * FROM toernooi_stats ORDER BY poule_id ASC, points DESC, score_diff DESC, score_for DESC, id ASC;";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $poule_index = $index_map[$row["poule_id"]];

            $response->stats[$poule_index]->standings[] = new \stdClass();
            $index = count($response->stats[$poule_index]->standings) - 1;

            $response->stats[$poule_index]->standings[$index]->id = intval($row["id"]);
            $response->stats[$poule_index]->standings[$index]->name = base64_decode($row["name"]);
            $response->stats[$poule_index]->standings[$index]->poule_id = intval($row["poule_id"]);
            $response->stats[$poule_index]->standings[$index]->played = intval($row["played"]);
            $response->stats[$poule_index]->standings[$index]->won = intval($row["won"]);
            $response->stats[$poule_index]->standings[$index]->lost = intval($row["lost"]);
            $response->stats[$poule_index]->standings[$index]->points = intval($row["points"]);
            $response->stats[$poule_index]->standings[$index]->score_for = intval($row["score_for"]);
            $response->stats[$poule_index]->standings[$index]->score_against = intval($row["score_against"]);
            $response->stats[$poule_index]->standings[$index]->score_diff = intval($row["score_diff"]);
        }
    }

    $conn->close();

    header('Content-Type: application/json');
    die(json_encode($response));
?>