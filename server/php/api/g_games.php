<?php
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
    if ($poule_id == null) {
        $sql = "SELECT * FROM toernooi_wed_name ORDER BY time ASC, court_num ASC; ";
    } else {
        $sql = "SELECT * FROM toernooi_wed_name WHERE poule_id = " . $poule_id . " ORDER BY time ASC; ";
    }

    $result = $conn->query($sql);

    $games = array();

    if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
            $games[] = new \stdClass();
            $index = count($games) - 1;

            $games[$index]->id = intval($row["id"]);

            $games[$index]->poule = new \stdClass();
            $games[$index]->poule->id = intval($row["poule_id"]);
            $games[$index]->poule->name = $row["poule_name"];
            $games[$index]->poule->color = $row["poule_color"];

            $games[$index]->team1 = new \stdClass();
            $games[$index]->team1->id = intval($row["team1_id"]);
            $games[$index]->team1->name = $row["team1_name"];

            $games[$index]->team2 = new \stdClass();
            $games[$index]->team2->id = intval($row["team2_id"]);
            $games[$index]->team2->name = $row["team2_name"];

            $games[$index]->team1->score = intval($row["score1"]);
            $games[$index]->team2->score = intval($row["score2"]);

            $games[$index]->time = $row["time"];

            $games[$index]->court_num = $row["court_num"];

            $games[$index]->ref = new \stdClass();
            $games[$index]->ref->id = $row["ref_id"];
            $games[$index]->ref->name = $row["ref_name"];
        }
    }
    // } else {
    //     echo "0 results";
    // }

    echo(json_encode($games));

    $conn->close();
?>