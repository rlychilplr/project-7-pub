<?php

require_once __DIR__ . "/../config/config.php";

abstract class Controller
{
    /**
     * @param string $view
     * @param array $data
     */
    protected function render($view, $data = []): void
    {
        try {
            extract($data);
            // Get the absolute path to the views directory
            $viewsPath = dirname(__DIR__) . "/views/" . $view . ".php";

            if (!file_exists($viewsPath)) {
                throw new Exception("View file not found: " . $viewsPath);
            }

            require $viewsPath;
        } catch (Throwable $e) {
            error_log("Error in render method: " . $e->getMessage());
            // Display a simple error message
            echo "<h1>Error</h1>";
            echo "<p>An error occurred while rendering the view.</p>";
            echo "<p>Please try again later or contact support.</p>";
        }
    }

    /**
     * @param string $path
     */
    protected function redirect($path): void
    {
        $basePath = Config::getBasePath();
        header("Location: $basePath/$path");
        exit();
    }

    /**
     * @param mixed $data
     */
    protected function json($data): void
    {
        try {
            header("Content-Type: application/json");
            $jsonString = json_encode($data);

            if ($jsonString === false) {
                error_log("JSON encode error: " . json_last_error_msg());
                echo '{"success":false,"message":"Error encoding JSON response"}';
            } else {
                echo $jsonString;
            }
            exit();
        } catch (Throwable $e) {
            error_log("Error in Controller::json: " . $e->getMessage());
            // Try a simpler approach as fallback
            header("Content-Type: application/json");
            echo '{"success":false,"message":"Internal server error"}';
            exit();
        }
    }
}
