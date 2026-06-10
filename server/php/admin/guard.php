<?php 

    session_start();
    $realm = "Restricted area";

    function setContentType($page) {
        $ext = pathinfo($page, PATHINFO_EXTENSION);
        switch ($ext) {
            case "html":
                header("Content-Type: text/html; charset=UTF-8");
                break;
            case "css":
                header("Content-Type: text/css; charset=UTF-8");
                break;
            case "js":
                header("Content-Type: text/javascript; charset=UTF-8");
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

    function readFileOrDie($filepath) {
        if (!file_exists($filepath)) {
            http_response_code(404);
            die("File not found: " . htmlspecialchars($filepath));
        }
        $content = @file_get_contents($filepath);
        if ($content === false) {
            http_response_code(500);
            die("Failed to read file: " . htmlspecialchars($filepath) . " (error: " . error_get_last()['message'] . ")");
        }
        return $content;
    }

    function serveFile($filepath) {
        if (!file_exists($filepath)) {
            http_response_code(404);
            header('X-Guard-Debug: not-found');
            die("File not found: " . htmlspecialchars($filepath));
        }

        $size = filesize($filepath);
        if ($size === false) {
            http_response_code(500);
            header('X-Guard-Debug: filesize-failed');
            die("Failed to stat file: " . htmlspecialchars($filepath));
        }

        // Send helpful debug headers (remove in production if desired)
        header('X-Guard-Path: ' . $filepath);
        header('X-Guard-Size: ' . $size);

        $ext = strtolower(pathinfo($filepath, PATHINFO_EXTENSION));

        // If serving HTML, load and rewrite admin paths to include the /das prefix
        if ($ext === 'html') {
            $content = @file_get_contents($filepath);
            if ($content === false) {
                http_response_code(500);
                header('X-Guard-Debug: readfile-failed');
                die("Failed to read file: " . htmlspecialchars($filepath));
            }

            // Rewrite absolute admin paths to include /das prefix, but avoid double-prefixing
            $content = preg_replace('#(?<!/das)/admin/#', '/das/admin/', $content);

            // Ensure any output buffers are cleared
            while (ob_get_level()) {
                ob_end_clean();
            }

            header('Content-Length: ' . strlen($content));
            echo $content;
            flush();
            exit();
        }

        // For non-HTML files stream directly
        while (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Length: ' . $size);
        $sent = @readfile($filepath);
        if ($sent === false) {
            http_response_code(500);
            header('X-Guard-Debug: readfile-failed');
            die("Failed to read file: " . htmlspecialchars($filepath));
        }

        flush();
        exit();
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

        if(preg_match($jsPattern, $page) || preg_match($cssPattern, $page) || preg_match($indexPattern, $page) || preg_match($logoPattern, $page)) {
            serveFile(__DIR__ . "/html/" . $_GET['page']);
        }
    }

    function unauthorized() {
        $page = $_GET['page'];

        checkSafePages($page);

        header('HTTP/1.0 401 Unauthorized');
        
        if ($page == "" || $page == "index.html" || $page == "login.html") {
            serveFile(__DIR__ . "/html/login.html");
        }
    }

    setContentType($_GET['page']);

    if (!isset($_SESSION['expires'])) {
        unauthorized();
    } elseif (time() > $_SESSION['expires']) {
        session_destroy();
        unauthorized();
    } else {
        serveFile(__DIR__ . "/html/" . $_GET['page']);
    }
?>