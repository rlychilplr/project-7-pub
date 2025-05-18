<?php
include_once __DIR__ . "/index.php";
global $router;

function removeCommonStart($str1, $str2)
{
    $scriptDir = dirname($str2);
    return str_replace($scriptDir, "", $str1);
}

$scriptPath = $_SERVER["SCRIPT_NAME"];
$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
$route = removeCommonStart($path, $scriptPath);

if ($route == "") {
    $route = "/";
}

if ($route[0] != "/") {
    $route = "/" . $route;
}

$router->dispatch($route);
