<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once "../app/config/config.php";
require_once "../app/core/Router.php";
require_once "../app/controllers/HomePageController.php";
require_once "../app/controllers/AuthController.php";
require_once "../app/controllers/GameController.php";
require_once "../app/controllers/MapController.php";

// For debugging
// echo "Requested URI: " . $_SERVER["REQUEST_URI"] . "<br>";
// echo "Request Method: " . $_SERVER["REQUEST_METHOD"] . "<br>";

// Autoload controllers
spl_autoload_register(function ($class) {
    $file = "../app/controllers/" . $class . ".php";
    if (file_exists($file)) {
        require_once $file;
    }
});

$router = new Router(function () {
    http_response_code(404);
    echo "404 Page not found.";
});

// Add routes
$router->addRoute("/", function () {
    $controller = new HomePageController();
    $controller->index();
});

$router->addRoute("/home", function () {
    $controller = new HomePageController();
    $controller->index();
});

$router->addRoute("/login", function () {
    $controller = new AuthController();
    $controller->login();
});

$router->addRoute("/register", function () {
    $controller = new AuthController();
    $controller->register();
});

$router->addRoute("/logout", function () {
    $controller = new AuthController();
    $controller->logout();
});

$router->addRoute("/game", function () {
    $controller = new GameController();
    $controller->index();
});

$router->addRoute("/map", function () {
    $controller = new MapController();
    $controller->index();
});

$router->addRoute("/game/playCard", function () {
    $controller = new GameController();
    $controller->playCard();
});

$router->addRoute("/game/endTurn", function () {
    $controller = new GameController();
    $controller->endTurn();
});

$router->addRoute("/reward", function () {
    $controller = new GameController();
    $controller->reward();
});

$router->addRoute("/game/over", function () {
    $controller = new GameController();
    $controller->gameOver();
});

$router->addRoute("/restart", function () {
    $controller = new GameController();
    $controller->restart();
});

$router->addRoute("/treasure", function () {
    $controller = new TreasureController();
    $controller->index();
});

$router->addRoute("/rest", function () {
    $restController = new RestController();
    $restController->index();
});

$router->addRoute("/elite", function () {
    $controller = new GameController();
    $controller->eliteEncounter();
});

$router->addRoute("/boss", function () {
    $controller = new GameController();
    $controller->bossEncounter();
});

$router->addRoute("/victory", function () {
    $gameController = new GameController();
    $gameController->victory();
});

// Get the URI path
$uri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
// Remove the base path from the URI if needed
$basePath = Config::getBasePath();
if ($basePath !== "/") {
    $uri = str_replace($basePath, "", $uri);
}

// Dispatch
$router->dispatch($uri);
