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
        try {
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
                require_once ROOT_PATH . "/app/Views/errors/404.php";
                exit;
            }

            foreach ($this->routes[$method] as $route => $action) {

                $pattern = preg_replace('/\{[a-zA-Z_][a-zA-Z0-9_]*\}/', '([a-zA-Z0-9_-]+)', $route);

                $pattern = "#^" . $pattern . "$#";

                if (preg_match($pattern, $uri, $matches)) {

                    array_shift($matches);

                    [$controllerName, $methodName] = explode('@', $action);

                    require_once ROOT_PATH . "/app/Controllers/" . $controllerName . ".php";

                    $controller = new $controllerName();

                    call_user_func_array([$controller, $methodName], $matches);

                    return;
                }
            }

            http_response_code(404);
            require_once ROOT_PATH . "/app/Views/errors/404.php";
            exit;

        } catch (Throwable $e) {
            error_log("Unhandled system exception in Router dispatch: " . $e->getMessage() . "\n" . $e->getTraceAsString());

            if (session_status() === PHP_SESSION_NONE) {
                @session_start();
            }

            if (empty($_SESSION['error'])) {
                $_SESSION['error'] = "An unexpected error occurred: " . $e->getMessage();
            }

            $referer = $_SERVER['HTTP_REFERER'] ?? '';
            $scheme = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? "https" : "http";
            $host = $_SERVER['HTTP_HOST'] ?? '';
            $reqUri = $_SERVER['REQUEST_URI'] ?? '';
            $currentUrl = $host ? ($scheme . "://" . $host . $reqUri) : '';

            if (!empty($referer) && $referer !== $currentUrl) {
                header("Location: " . $referer);
                exit;
            }

            $fallback = defined('BASE_URL') ? BASE_URL : '/';
            if (!empty($_SESSION['auth_user_role'])) {
                if (in_array($_SESSION['auth_user_role'], ['admin', 'agent'])) {
                    $fallback = BASE_URL . '/agent/tickets';
                } else {
                    $fallback = BASE_URL . '/tickets';
                }
            }

            header("Location: " . $fallback);
            exit;
        }
    }
}