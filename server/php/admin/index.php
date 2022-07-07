<?php
    session_start();
    $realm = "Restricted area";

    function unauthorized() {
        header('HTTP/1.0 401 Unauthorized');
        die(file_get_contents("html/login.html"));
    }

    if (!isset($_SESSION['expires'])) {
        unauthorized();
    } elseif (time() > $_SESSION['expires']) {
        session_destroy();
        unauthorized();
    } else {
        die(file_get_contents("html/index.html"));
    }
?>