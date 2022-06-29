<?php
    $host = 'wolleserver.local:4001';
    $user = 'root';
    $pass = 'root';
    $db = 'toernooi_das';

    $conn = new mysqli($host, $user, $pass, $db);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    } 
    
    $sql = "SELECT * FROM toernooi_wed_name";
    $result = $conn->query($sql);

    $games = array();

    if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
            $games[] = new \stdClass();
            $index = count($games) - 1;

            $games[$index]->id = intval($row["ID"]);

            $games[$index]->poule = new \stdClass();
            $games[$index]->poule->id = intval($row["poule_ID"]);
            $games[$index]->poule->name = $row["poule_name"];

            $games[$index]->team1 = new \stdClass();
            $games[$index]->team1->id = intval($row["team1_ID"]);
            $games[$index]->team1->name = $row["team1_name"];

            $games[$index]->team2 = new \stdClass();
            $games[$index]->team2->id = intval($row["team2_ID"]);
            $games[$index]->team2->name = $row["team2_name"];

            $games[$index]->team1->score = intval($row["score1"]);
            $games[$index]->team2->score = intval($row["score2"]);

            $games[$index]->time = $row["time"];
        }
    } else {
        echo "0 results";
    }

    echo(json_encode($games));

    $conn->close();
?>