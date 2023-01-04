<?php
    session_start();
    session_destroy();
    die(file_get_contents("html/login.html"));
?>