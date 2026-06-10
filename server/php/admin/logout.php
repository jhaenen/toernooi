<?php
    session_start();
    session_destroy();
    $content = @file_get_contents(__DIR__ . "/html/login.html");
    if ($content === false) {
        http_response_code(500);
        die("Failed to read login.html");
    }
    $content = preg_replace('#(?<!/das)/admin/#', '/das/admin/', $content);
    die($content);
?>