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


    $poules = null;

    if ($poule_id == null) {
        $sql = "SELECT * FROM toernooi_poules";
        $result = $conn->query($sql);

        $poules = array();

        if ($result->num_rows > 0) {
            // output data of each row
            while($row = $result->fetch_assoc()) {
                $poules[] = row_to_obj($row);
            }
        }
    } else {
        $sql = "SELECT * FROM toernooi_poules WHERE id = " . $poule_id;

        $result = $conn->query($sql);

        $poules = new \stdClass();

        if ($result->num_rows > 0) {
            $poules = row_to_obj($result->fetch_assoc());
        }
    }

    $conn->close();

    // Set content type to json
    header('Content-Type: application/json');

    die(json_encode($poules));

    function row_to_obj($row) {
        $obj = new \stdClass();
        $obj->id = intval($row["id"]);
        $obj->name = base64_decode($row["name"]);
        $obj->color = $row["color"];
        return $obj;
    }
?>