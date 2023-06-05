<?php 
    $ini = parse_ini_file("../../env.ini");

    if ($ini["ENV_MODE"] != "DEV") {
        include "guard.php";
    }

    // Allow methods PATCH and OPTIONS request
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, PATCH, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');
        header('Content-Type: application/json');
        header('Access-Control-Max-Age: 1728000');
        header('Content-Length: 0');
        header('Connection: close');
        die();
    }

    // Return method not allowed if not GET, PATCH or OPTIONS
    if ($_SERVER['REQUEST_METHOD'] != 'GET' && $_SERVER['REQUEST_METHOD'] != 'PATCH' && $_SERVER['REQUEST_METHOD'] != 'OPTIONS') {
        header('HTTP/1.0 405 Method Not Allowed');
        die();
    }

    // $ini = parse_ini_file("../env.ini");

    $host = $ini["DB_HOST"];
    $user = $ini["DB_USER"];
    $pass = $ini["DB_PASS"];
    $db = $ini["DB_NAME"];

    $conn = new mysqli($host, $user, $pass, $db);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    } 


    // If GET request, get the params from the database
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        $sql = "";

        $param_name = null;
        if (isset($_GET['name'])) {
            $param_name = $_GET['name'];
        }

        if ($param_name != null) {
            $sql = "SELECT * FROM toernooi_params WHERE name = '" . $param_name . "';";
        } else {
            $sql = "SELECT * FROM toernooi_params;";
        }

        $result = $conn->query($sql);

        $params = array();

        if ($result->num_rows > 0) {
            // output data of each row
            while($row = $result->fetch_assoc()) {                
                $params[$row["name"]] = $row["value"];
            }
        }

        header('Access-Control-Allow-Origin: *');
        header('Content-Type: application/json');
        echo json_encode($params);
    }

    // If PATCH request, update the params in the database
    if ($_SERVER['REQUEST_METHOD'] == 'PATCH') {
        $allowed_names = ["date"];

        // Decode body of request
        $request = json_decode(file_get_contents('php://input'));
        
        // If no param name is given, return bad request
        if (!isset($request->name) || !isset($request->value)) {
            header('HTTP/1.0 400 Bad Request');
            die("No param given");
        }

        $param_name = $request->name;
        $param_value = $request->value;

        // Check if the param exists
        $sql = "SELECT * FROM toernooi_params WHERE name = '" . $param_name . "';";
        $result = $conn->query($sql);

        if ($result->num_rows == 0) {
            header('HTTP/1.0 404 Not Found');
            die("Param not found");
        }

        // Update the param
        $sql = "UPDATE toernooi_params SET value = '" . $param_value . "' WHERE name = '" . $param_name . "';";
        $conn->query($sql);
    }

    $conn->close();
?>