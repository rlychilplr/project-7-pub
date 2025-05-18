<?php
/**
 * credits: fase 4 example and Claude 3.5 sonnet (zed) for regex support
 * html (elements) use this, php uses relative/absolute paths and doesn't care about routing
 */

class Router
{
    private $routes = [];
    private $notfoundRouteFunc;

    /**
     * @param mixed $notfoundRouteFunc
     */
    function __construct($notfoundRouteFunc)
    {
        $this->notfoundRouteFunc = $notfoundRouteFunc;
    }

    /**
     * @param string $pattern
     * @param string $handler
     */
    public function addRoute($pattern, $handler): void
    {
        $this->routes[$pattern] = [
            "pattern" => $pattern,
            "handler" => $handler,
        ];
    }

    /**
     * @param string $requestedPath
     */
    public function dispatch($requestedPath): void
    {
        try {
            foreach ($this->routes as $route) {
                $pattern = str_replace("/", "\/", $route["pattern"]);
                $pattern =
                    "/^" . str_replace("(.*)", "([^?]*)", $pattern) . '$/';

                if (preg_match($pattern, $requestedPath, $matches)) {
                    array_shift($matches);
                    call_user_func_array($route["handler"], $matches);
                    return;
                }
            }

            call_user_func($this->notfoundRouteFunc);
        } catch (Throwable $e) {
            http_response_code(500);
            echo "Internal Server Error";
        }
    }
}
