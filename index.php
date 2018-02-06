<?php
/**
 * Laravel - A PHP Framework For Web Artisans
 *
 * @package  Laravel
 * @author   Taylor Otwell <taylorotwell@gmail.com>
 */

$islive=true;
$server = $_SERVER["SERVER_NAME"];
if($server == "localhost" || $server == "127.0.0.1"){$islive = false;}
if(strpos($server, ".") !== false){
    if(is_numeric(str_replace(".", "", $server))) {
        $server = explode(".", $server);
        if ($server[0] == "10") {$islive = false;}
        if ($server[0] == "172" && $server[1] > "15" && $server[1] < 32) {$islive = false;}
        if ($server[0] == "192" && $server[1] == "168") {$islive = false;}
    }
}

//if($_SERVER["SERVER_NAME"] == "londonpizza.ca") {
if($islive) {
    if (!isset($_SERVER['HTTPS']) or $_SERVER['HTTPS'] == 'off') {
        $redirect_url = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        header("Location: $redirect_url");
        exit();
    }
}

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
);

// This file allows us to emulate Apache's "mod_rewrite" functionality from the
// built-in PHP web server. This provides a convenient way to test a Laravel
// application without having installed a "real" web server software here.
if ($uri !== '/' && file_exists(__DIR__.'/public'.$uri)) {
    return false;
}

require_once __DIR__.'/public/index.php';
