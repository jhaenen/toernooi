<?php 
    session_start();

    function unauthorized() {
        header('HTTP/1.0 401 Unauthorized');
        die();
    }

    // Set content type to JSON
    header("Content-Type: application/json");

    if (!isset($_SESSION['expires'])) {
        unauthorized();
    } elseif (time() > $_SESSION['expires']) {
        session_destroy();
        unauthorized();
    }
?>