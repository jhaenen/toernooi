<?php
    $host = 'wolleserver.local:4001';
    $user = 'root';
    $pass = 'root';
    $conn = new mysqli($host, $user, $pass);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    } 
    echo "Connected to MySQL successfully!";
?>