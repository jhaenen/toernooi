<?php
    // Allow from any origin
    if (isset($_SERVER['HTTP_ORIGIN'])) {
        header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400');
    }

    if ($_SERVER['REQUEST_METHOD'] != 'GET') {
        header('HTTP/1.0 405 Method Not Allowed');
        die();
    }

    $ini = parse_ini_file("../env.ini");

    $host = $ini["DB_HOST"];
    $user = $ini["DB_USER"];
    $pass = $ini["DB_PASS"];
    $db   = $ini["DB_NAME"];

    $conn = new mysqli($host, $user, $pass, $db);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        header('HTTP/1.0 400 Bad Request');
        die();
    }

    $team_id = intval($_GET['id']);

    // Team info
    $sql = "SELECT t.id, t.name, p.id as poule_id, p.name as poule_name, p.color as poule_color
            FROM toernooi_teams t
            JOIN toernooi_poules p ON t.poule_id = p.id
            WHERE t.id = $team_id";
    $result = $conn->query($sql);

    if ($result->num_rows == 0) {
        header('HTTP/1.0 404 Not Found');
        die();
    }

    $row  = $result->fetch_assoc();
    $team = new \stdClass();
    $team->id   = intval($row['id']);
    $team->name = base64_decode($row['name']);
    $team->poule = new \stdClass();
    $team->poule->id    = intval($row['poule_id']);
    $team->poule->name  = base64_decode($row['poule_name']);
    $team->poule->color = $row['poule_color'];

    // Played games (team1 or team2)
    $sql = "SELECT * FROM toernooi_wed_name
            WHERE team1_id = $team_id OR team2_id = $team_id
            ORDER BY time ASC";
    $result = $conn->query($sql);

    $played_games = array();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $g = new \stdClass();
            $g->id = intval($row['id']);

            $g->poule = new \stdClass();
            $g->poule->id    = intval($row['poule_id']);
            $g->poule->name  = base64_decode($row['poule_name']);
            $g->poule->color = $row['poule_color'];

            $g->team1 = new \stdClass();
            $g->team1->id    = intval($row['team1_id']);
            $g->team1->name  = base64_decode($row['team1_name']);
            $g->team1->score = intval($row['score1']);

            $g->team2 = new \stdClass();
            $g->team2->id    = intval($row['team2_id']);
            $g->team2->name  = base64_decode($row['team2_name']);
            $g->team2->score = intval($row['score2']);

            $g->time      = $row['time'];
            $g->court_num = $row['court_num'];

            if ($row['ref_id'] == null) {
                $g->ref = null;
            } else {
                $g->ref = new \stdClass();
                $g->ref->id   = intval($row['ref_id']);
                $g->ref->name = base64_decode($row['ref_name']);
            }

            $played_games[] = $g;
        }
    }

    // Ref games
    $sql = "SELECT * FROM toernooi_wed_name
            WHERE ref_id = $team_id
            ORDER BY time ASC";
    $result = $conn->query($sql);

    $ref_games = array();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $g = new \stdClass();
            $g->id = intval($row['id']);

            $g->poule = new \stdClass();
            $g->poule->id    = intval($row['poule_id']);
            $g->poule->name  = base64_decode($row['poule_name']);
            $g->poule->color = $row['poule_color'];

            $g->team1 = new \stdClass();
            $g->team1->id    = intval($row['team1_id']);
            $g->team1->name  = base64_decode($row['team1_name']);
            $g->team1->score = intval($row['score1']);

            $g->team2 = new \stdClass();
            $g->team2->id    = intval($row['team2_id']);
            $g->team2->name  = base64_decode($row['team2_name']);
            $g->team2->score = intval($row['score2']);

            $g->time      = $row['time'];
            $g->court_num = $row['court_num'];

            $g->ref = new \stdClass();
            $g->ref->id   = intval($row['ref_id']);
            $g->ref->name = base64_decode($row['ref_name']);

            $ref_games[] = $g;
        }
    }

    $conn->close();

    $resp = new \stdClass();
    $resp->team         = $team;
    $resp->played_games = $played_games;
    $resp->ref_games    = $ref_games;

    header('Content-Type: application/json');
    die(json_encode($resp));
?>