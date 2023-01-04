<?php
    session_start();

    $realm = "Restricted area";

    $login = parse_ini_file("./env.ini");

    $session_dur_sec = 64800; // 18 hours

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
            // Return bad request if the username or password is not set
            if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW'])) {
                header('HTTP/1.0 400 Bad Request');
                die();
            }
            
            // Check if the username and password are correct otherwise return unauthorized
            if($_SERVER['PHP_AUTH_USER'] == $login['USER'] && $_SERVER['PHP_AUTH_PW'] == $login['PASS']) {
                session_destroy();
                session_start();
                $_SESSION['expires'] = time() + $session_dur_sec;
                echo "Welcome to $realm, {$_SERVER['PHP_AUTH_USER']}.";
            } else {
                unauthorized();
            }
        }
        
        die();
    } else {
        header('HTTP/1.0 400 Bad Request');
        die();
    }
?>

