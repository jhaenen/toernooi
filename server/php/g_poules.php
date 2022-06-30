<?php
    $host = 'wolleserver.local:4001';
    $user = 'root';
    $pass = 'root';
    $db = 'toernooi_das';

    $conn = new mysqli($host, $user, $pass, $db);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    } 
    
    $sql = "SELECT * FROM toernooi_poules";
    $result = $conn->query($sql);

    $poules = array();

    if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
            $poules[] = $row;
        }
    } else {
        echo "0 results";
    }

    echo(json_encode($poules));

    $conn->close();
?>