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

    $conn->close();
?>