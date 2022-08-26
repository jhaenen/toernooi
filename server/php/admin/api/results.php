<?php
    include "guard.php";

    // Allow PATCH method on OPTIONS request
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        header('Access-Control-Allow-Methods: PATCH, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');
        header('Content-Type: application/json');
        header('Access-Control-Max-Age: 1728000');
        header('Content-Length: 0');
        header('Connection: close');
        die();
    }

    // Return method not allowed if not PATCH
    if ($_SERVER['REQUEST_METHOD'] != 'PATCH') {
        header('HTTP/1.0 405 Method Not Allowed');
        die();
    }

    // Connect to database
    $ini = parse_ini_file("../../env.ini");

    $host = $ini["DB_HOST"];
    $user = $ini["DB_USER"];
    $pass = $ini["DB_PASS"];
    $db = $ini["DB_NAME"];

    $conn = new mysqli($host, $user, $pass, $db);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Decode body of request
    $request = json_decode(file_get_contents('php://input'));

    // if field id is not set and not int return bad request
    if (!isset($request->id) || !is_int($request->id) || !isset($request->score1) || !is_int($request->score1) || !isset($request->score2) || !is_int($request->score2)) {
        header('HTTP/1.0 400 Bad Request');
        die();
    } else {
        // Update game with score in database
        $sql = "UPDATE toernooi_wedstrijden SET score1 = " . $request->score1 . ", score2 = " . $request->score2 . " WHERE id = " . $request->id;
        $conn->query($sql);

        // Internal server error if query failed
        if ($conn->error) {
            header('HTTP/1.0 500 Internal Server Error');
            die();
        }

        die();
    }
?>