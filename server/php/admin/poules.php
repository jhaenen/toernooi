<?php
    // Allow methods GET, PUT, PATCH, DELETE on OPTIONS request
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: PUT, PATCH, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');
        header('Content-Type: application/json');
        header('Access-Control-Max-Age: 1728000');
        header('Content-Length: 0');
        header('Connection: close');
        die();
    }

    // Return method not allowed if not PUT, PATCH, DELETE
    if ($_SERVER['REQUEST_METHOD'] != 'PUT' && $_SERVER['REQUEST_METHOD'] != 'PATCH' && $_SERVER['REQUEST_METHOD'] != 'DELETE') {
        header('HTTP/1.0 405 Method Not Allowed');
        die();
    }

    // Connect to database
    $ini = parse_ini_file("../env.ini");

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

    // If request is PUT
    if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
        // If field name is not set return bad request
        if (!isset($request->name) && !is_string($request->name)) {
            header('HTTP/1.0 400 Bad Request');
            die();
        } else {
            // Insert new poule with name in database
            $sql = "INSERT INTO toernooi_poules (name) VALUES ('" . $request->name . "')";
            $conn->query($sql);
            die();
        }
    }

    // If request is PATCH
    if ($_SERVER['REQUEST_METHOD'] == 'PATCH') {
        // If field name is not set return bad request
        if (!isset($request->id) || !is_int($request->id) || !isset($request->name) || !is_string($request->name)) {
            header('HTTP/1.0 400 Bad Request');
            die();
        } else {
            // Update poule with name in database
            $sql = "UPDATE toernooi_poules SET name = '" . $request->name . "' WHERE id = " . $request->id;
            $conn->query($sql);
            die();
        }
    }

    // If request is DELETE
    if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
        // If field name is not set return bad request
        if (!isset($request->id) || !is_int($request->id)) {
            header('HTTP/1.0 400 Bad Request');
            die();
        } else {
            // Delete games from this poule
            $sql = "DELETE FROM toernooi_wedstrijden WHERE poule_id = " . $request->id;
            $conn->query($sql);

            // Delete teams from this poule
            $sql = "DELETE FROM toernooi_teams WHERE poule_id = " . $request->id;
            $conn->query($sql);

            // Delete poule from database
            $sql = "DELETE FROM toernooi_poules WHERE id = " . $request->id;
            $conn->query($sql);
            die();
        }
    }
?>