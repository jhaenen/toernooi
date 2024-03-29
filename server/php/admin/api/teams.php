<?php 
    $ini = parse_ini_file("../../env.ini");

    if ($ini["ENV_MODE"] != "DEV") {
        include "guard.php";
    }

    // Allow methods PUT, PATCH, DELETE on OPTIONS request
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, PUT, PATCH, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');
        header('Content-Type: application/json');
        header('Access-Control-Max-Age: 1728000');
        header('Content-Length: 0');
        header('Connection: close');
        die();
    }

    // Return method not allowed if not GET, PUT, PATCH, DELETE
    if ($_SERVER['REQUEST_METHOD'] != 'GET' && $_SERVER['REQUEST_METHOD'] != 'PUT' && $_SERVER['REQUEST_METHOD'] != 'PATCH' && $_SERVER['REQUEST_METHOD'] != 'DELETE') {
        header('HTTP/1.0 405 Method Not Allowed');
        die();
    }


    $host = $ini["DB_HOST"];
    $user = $ini["DB_USER"];
    $pass = $ini["DB_PASS"];
    $db = $ini["DB_NAME"];

    $conn = new mysqli($host, $user, $pass, $db);

    // Internal server error if connection failed
    if ($conn->connect_error) {
        header('HTTP/1.0 500 Internal Server Error');
        die();
    }

    // Decode body of request
    $request = json_decode(file_get_contents('php://input'));

    // if method is GET
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        // If $_GET['poule'] is not set return all teams
        if (!isset($_GET['poule'])) {
            $sql = "SELECT * FROM toernooi_teams";
            $result = $conn->query($sql);
            $teams = array();

            while ($row = $result->fetch_assoc()) {
                $teams[] = new \stdClass();
                $index = count($teams) - 1;

                $teams[$index]->id = intval($row["id"]);
                $teams[$index]->name = base64_decode($row["name"]);
                $teams[$index]->poule_id = intval($row["poule_id"]);
            }

            echo json_encode($teams);
        } else {
            // Check if string $_GET['poule'] is an numeric value
            if (!is_numeric($_GET['poule'])) {
                header('HTTP/1.0 400 Bad Request');
                die();
            }

            $poule = intval($_GET['poule']);


            // Check if poule exists otherwise return bad request
            $sql = "SELECT * FROM toernooi_poules WHERE id = " . $poule;
            $result = $conn->query($sql);


            // echo error of query

            if ($result->num_rows == 0) {
                header('HTTP/1.0 400 Bad Request');
                die();
            }

            // Return teams of poule
            $sql = "SELECT * FROM toernooi_teams WHERE poule_id = " . $poule;
            $result = $conn->query($sql);
            $teams = array();

            while ($row = $result->fetch_assoc()) {
                $teams[] = new \stdClass();
                $index = count($teams) - 1;

                $teams[$index]->id = intval($row["id"]);
                $teams[$index]->name = base64_decode($row["name"]);
                $teams[$index]->poule_id = intval($row["poule_id"]);
            }

            echo json_encode($teams);
        }
    }

    // if method is PUT
    if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
        // if field poule_id is not set and not int and field name is not set and not string return bad request
        if (!isset($request->poule_id) || !is_int($request->poule_id) || !isset($request->name) || !is_string($request->name)) {
            header('HTTP/1.0 400 Bad Request');
            die();
        } else {
            // Check if poule_id exists in database
            $sql = "SELECT * FROM toernooi_poules WHERE id = " . $request->poule_id;
            $result = $conn->query($sql);

            // If poule_id does not exist return bad request
            if ($result->num_rows == 0) {
                header('HTTP/1.0 400 Bad Request');
                die();
            }

            // insert new team with name in database
            $sql = "INSERT INTO toernooi_teams (poule_id, name) VALUES (" . $request->poule_id . ", '" . base64_encode($request->name) . "')";
            $conn->query($sql);

            // Internal server error if query failed
            if ($conn->error) {
                header('HTTP/1.0 500 Internal Server Error');
                die();
            }

            die();
        }
    }

    // if method is PATCH
    if ($_SERVER['REQUEST_METHOD'] == 'PATCH') {
        // if field id is not set and not int and field name is not set or not string return bad request
        if (!isset($request->id) || !is_int($request->id) || !isset($request->name) || !is_string($request->name)) {
            header('HTTP/1.0 400 Bad Request');
            die();
        } else {
            // Update team in database
            $sql = "UPDATE toernooi_teams SET name = '" . base64_encode($request->name) . "' WHERE id = " . $request->id;
            $conn->query($sql);

            // Internal server error if query failed
            if ($conn->error) {
                header('HTTP/1.0 500 Internal Server Error');
                die();
            }

            die();
        }
    }

    // if method is DELETE
    if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
        // if field id is not set and not int return bad request
        if (!isset($request->id) || !is_int($request->id)) {
            header('HTTP/1.0 400 Bad Request');
            die();
        } else {
            // Delete games of team from database
            $sql = "DELETE FROM toernooi_wedstrijden WHERE team1_id = " . $request->id . " OR team2_id = " . $request->id;
            $conn->query($sql);

            // Delete team from database
            $sql = "DELETE FROM toernooi_teams WHERE id = " . $request->id;
            $conn->query($sql);

            // Internal server error if query failed
            if ($conn->error) {
                header('HTTP/1.0 500 Internal Server Error');
                die();
            }

            die();
        }
    }
?>