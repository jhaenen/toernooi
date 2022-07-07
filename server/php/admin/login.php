<?php
    session_start();

    $realm = "Restricted area";

    $user = "admin";
    $pass = "root";

    $session_dur_sec = 60;

    function unauthorized() {
        header('HTTP/1.0 401 Unauthorized');
        die(file_get_contents("html/login.html"));
    }

    if (isset($_SESSION['expires']) && time() <= $_SESSION['expires']) {
        header("Location: /admin/");
        die();
    }

    if ($_SERVER['REQUEST_METHOD'] == "GET") {
        if (!isset($_SERVER['PHP_AUTH_USER'])) {
            unauthorized();
        } else {
            if($_SERVER['PHP_AUTH_USER'] == $user && $_SERVER['PHP_AUTH_PW'] == $pass) {
                session_destroy();
                session_start();
                $_SESSION['expires'] = time() + $session_dur_sec;
                echo "Welcome to $realm, {$_SERVER['PHP_AUTH_USER']}.";
            } else {
                unauthorized();
            }
        }
        die();
    }
?>

