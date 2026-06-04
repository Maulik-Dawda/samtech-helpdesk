<?php

class Router
{
    private $routes = [];

    public function get($uri, $action)
    {
        $this->routes['GET'][$uri] = $action;
    }

    public function post($uri, $action)
    {
        $this->routes['POST'][$uri] = $action;
    }

    public function dispatch()
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        $basePath = '/samtech-helpdesk/public';

        $uri = str_replace($basePath, '', $uri);

        $uri = rtrim($uri, '/');

        if ($uri === '') {
            $uri = '/';
        }

        $method = $_SERVER['REQUEST_METHOD'];

        if (!isset($this->routes[$method])) {
            http_response_code(404);
            echo "404 Page Not Found";
            return;
        }

        foreach ($this->routes[$method] as $route => $action) {

            $pattern = preg_replace('/\{[a-zA-Z_][a-zA-Z0-9_]*\}/', '([a-zA-Z0-9_-]+)', $route);

            $pattern = "#^" . $pattern . "$#";

            if (preg_match($pattern, $uri, $matches)) {

                array_shift($matches);

                [$controllerName, $methodName] = explode('@', $action);

                require_once "../app/Controllers/" . $controllerName . ".php";

                $controller = new $controllerName();

                call_user_func_array([$controller, $methodName], $matches);

                return;
            }
        }

        http_response_code(404);
        echo "404 Page Not Found";
    }
}