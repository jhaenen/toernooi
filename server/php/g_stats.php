<?php
    $ini = parse_ini_file("./env.ini");

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
    if ($poule_id == null) {
        $sql = "SELECT * FROM toernooi_stats ORDER BY poule_id ASC, points DESC; ";
    } else {
        $sql = "SELECT * FROM toernooi_stats WHERE poule_id = " . $poule_id . " ORDER BY points DESC; ";
    }
    
    $result = $conn->query($sql);

    $stats = array();

    if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
            $stats[] = new \stdClass();
            $index = count($stats) - 1;

            $stats[$index]->id = intval($row["id"]);
            $stats[$index]->name = $row["name"];
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

    echo(json_encode($stats));

    $conn->close();
?>