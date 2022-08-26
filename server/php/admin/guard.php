<?php 

    session_start();
    $realm = "Restricted area";

    function setContentType($page) {
        $ext = pathinfo($page, PATHINFO_EXTENSION);
        switch ($ext) {
            case "html":
                header("Content-Type: text/html");
                break;
            case "css":
                header("Content-Type: text/css");
                break;
            case "js":
                header("Content-Type: text/javascript");
                break;
            case "png":
                header("Content-Type: image/png");
                break;
            case "jpg":
                header("Content-Type: image/jpeg");
                break;
            case "webp":
                header("Content-Type: image/webp");
                break;
            case "svg":
                header("Content-Type: image/svg+xml");
                break;
            case "ico":
                header("Content-Type: image/x-icon");
                break;
            default:
                break;
        }
    }

    function checkSafePages($page) {
        // Create variable js pattern that stores a regex pattern for 'assets/login.html.[hash].js' with hash being 8 characters long
        $jsPattern = '/assets\/login\.html\.[a-z0-9]{8}\.js/';   

        // Create variable index pattern that stores a regex pattern for 'assets/index.[hash].js' with hash being 8 characters long
        $indexPattern = '/assets\/index\.[a-z0-9]{8}\.js/';

        // Create variable css pattern that stores a regex pattern for 'assets/index.[hash].css' with hash being 8 characters long
        $cssPattern = '/assets\/index\.[a-z0-9]{8}\.css/';

        // Create variable logo pattern that stores a regex pattern for 'assets/logo_lq.[hash].webp' with hash being 8 characters long
        $logoPattern = '/assets\/logo_lq\.[a-z0-9]{8}\.webp/';

        if(preg_match($jsPattern, $page) || preg_match($cssPattern, $page) || preg_match($indexPattern, $page) || preg_match($logoPattern, $page)) die(file_get_contents("html/" . $_GET['page']));
    }

    function unauthorized() {
        $page = $_GET['page'];

        checkSafePages($page);

        header('HTTP/1.0 401 Unauthorized');
        
        if ($page == "" || $page == "index.html") {
            die(file_get_contents("html/login.html"));
        }
    }

    setContentType($_GET['page']);

    if (!isset($_SESSION['expires'])) {
        unauthorized();
    } elseif (time() > $_SESSION['expires']) {
        session_destroy();
        unauthorized();
    } else {
        die(file_get_contents("html/" . $_GET['page']));
    }
?>