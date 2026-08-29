<?php

namespace Core\Libraries\Router;

use Core\Libraries\Layout\Layout;
use RuntimeException;

class Router
{
    protected static $routes = [];
    protected static $notFoundHandler = null;
    protected static $methodNotAllowedHandler = null;

    private function __construct()
    {
    }

    public static function addRoute($methods, $uri, $action)
    {
        $methods = array_map('strtoupper', (array)$methods);
        $uri = '/' . trim($uri, '/');

        self::$routes[] = [
            'methods' => $methods,
            'uri'     => $uri,
            'action'  => $action,
            'pattern' => self::compilePattern($uri),
        ];
    }

    public static function get($uri, $action)
    {
        self::addRoute(['GET', 'HEAD'], $uri, $action);
    }

    public static function post($uri, $action)
    {
        self::addRoute('POST', $uri, $action);
    }

    public static function put($uri, $action)
    {
        self::addRoute('PUT', $uri, $action);
    }

    public static function delete($uri, $action)
    {
        self::addRoute('DELETE', $uri, $action);
    }

    public static function match($methods, $uri, $action)
    {
        self::addRoute($methods, $uri, $action);
    }

    public static function any($uri, $action)
    {
        self::addRoute(['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'HEAD', 'OPTIONS'], $uri, $action);
    }

    protected static function compilePattern($uri)
    {
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $uri);
        return '#^' . $pattern . '$#';
    }

    public static function setNotFoundHandler($handler)
    {
        self::$notFoundHandler = $handler;
    }

    public static function setMethodNotAllowedHandler($handler)
    {
        self::$methodNotAllowedHandler = $handler;
    }

    public static function loadRoutes($filePath)
    {
        if (file_exists($filePath)) {
            require_once $filePath;
        }
    }

    public static function getRoutes()
    {
        return self::$routes;
    }

    public static function clear()
    {
        self::$routes = [];
    }

    public static function dispatch($requestMethod = null, $requestUri = null)
    {
        if ($requestMethod === null) {
            $requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
            if ($requestMethod === 'POST' && isset($_POST['_method'])) {
                $spoofedMethod = strtoupper($_POST['_method']);
                if (in_array($spoofedMethod, ['PUT', 'DELETE', 'PATCH', 'GET', 'HEAD', 'OPTIONS'], true)) {
                    $requestMethod = $spoofedMethod;
                }
            }
        }

        if ($requestUri === null) {
            $requestUri = $_SERVER['REQUEST_URI'] ?? '/';
        }

        $path = parse_url($requestUri, PHP_URL_PATH) ?? '/';
        $path = '/' . trim($path, '/');

        $methodMatchFound = false;

        foreach (self::$routes as $route) {
            if (preg_match($route['pattern'], $path, $matches)) {
                if (in_array(strtoupper($requestMethod), $route['methods'], true)) {
                    $params = [];
                    foreach ($matches as $key => $val) {
                        if (!is_int($key)) {
                            $params[$key] = $val;
                        }
                    }

                    return self::executeAction($route['action'], $params);
                }
                $methodMatchFound = true;
            }
        }

        if ($methodMatchFound) {
            return self::handleMethodNotAllowed();
        }

        return self::handleNotFound();
    }

    protected static function executeAction($action, $params = [])
    {
        if (is_callable($action)) {
            return call_user_func_array($action, array_values($params));
        }

        if (is_string($action)) {
            if (strpos($action, '@') !== false) {
                [$controllerName, $method] = explode('@', $action, 2);
            } else {
                $controllerName = $action;
                $method = 'index';
            }

            $resolvedClass = self::resolveControllerClass($controllerName);
            if (!class_exists($resolvedClass)) {
                throw new RuntimeException("Controller class [{$resolvedClass}] not found.");
            }

            $controllerInstance = new $resolvedClass();
            if (!method_exists($controllerInstance, $method)) {
                throw new RuntimeException("Method [{$method}] does not exist in controller [{$resolvedClass}].");
            }

            return call_user_func_array([$controllerInstance, $method], array_values($params));
        }

        if (is_array($action) && count($action) === 2) {
            [$controller, $method] = $action;
            if (is_string($controller)) {
                $resolvedClass = self::resolveControllerClass($controller);
                $controller = new $resolvedClass();
            }
            return call_user_func_array([$controller, $method], array_values($params));
        }

        throw new RuntimeException('Invalid route action definition.');
    }

    protected static function resolveControllerClass($controllerName)
    {
        if (strpos($controllerName, '\\') === 0) {
            return ltrim($controllerName, '\\');
        }

        if (class_exists($controllerName)) {
            return $controllerName;
        }

        $appController = "App\\Controllers\\{$controllerName}";
        if (class_exists($appController)) {
            return $appController;
        }

        if (preg_match('/^([A-Z][a-zA-Z0-9_]*)Controller$/', $controllerName, $m)) {
            $moduleName = $m[1];
            $moduleController = "App\\Modules\\{$moduleName}\\Controllers\\{$controllerName}";
            if (class_exists($moduleController)) {
                return $moduleController;
            }
        }

        return $appController;
    }

    public static function handleNotFound()
    {
        if (!headers_sent()) {
            http_response_code(404);
        }

        if (self::$notFoundHandler !== null) {
            return call_user_func(self::$notFoundHandler);
        }

        if (file_exists(VIEW_PATH . '/errors/404.php')) {
            return Layout::render('main', 'errors.404', ['title' => '404 - Sayfa Bulunamadı']);
        }

        echo '<!DOCTYPE html><html><head><meta charset="utf-8"><title>404 Not Found</title><style>body{font-family:sans-serif;text-align:center;padding:50px;background:#f8fafc;color:#334155;}h1{font-size:48px;margin-bottom:10px;}p{font-size:18px;}</style></head><body><h1>404</h1><p>Aradığınız sayfa bulunamadı.</p><a href="/" style="color:#3b82f6;">Ana Sayfaya Dön</a></body></html>';
        return null;
    }

    public static function handleMethodNotAllowed()
    {
        if (!headers_sent()) {
            http_response_code(405);
        }

        if (self::$methodNotAllowedHandler !== null) {
            return call_user_func(self::$methodNotAllowedHandler);
        }

        echo '<!DOCTYPE html><html><head><meta charset="utf-8"><title>405 Method Not Allowed</title><style>body{font-family:sans-serif;text-align:center;padding:50px;background:#f8fafc;color:#334155;}h1{font-size:48px;margin-bottom:10px;}p{font-size:18px;}</style></head><body><h1>405</h1><p>Bu istek metodu bu rota için geçerli değildir.</p><a href="/" style="color:#3b82f6;">Ana Sayfaya Dön</a></body></html>';
        return null;
    }
}
