<?php

namespace Core\Libraries\Router;

use Core\Libraries\Layout\Layout;
use Core\Libraries\Request\Request;
use Exception;
use RuntimeException;

class Router
{
    /**
     * @var array
     */
    protected static $routes = [];

    /**
     * @var callable|null
     */
    protected static $notFoundHandler = null;

    /**
     * @var callable|null
     */
    protected static $methodNotAllowedHandler = null;

    /**
     * Prevent instantiation.
     */
    private function __construct()
    {
    }

    /**
     * Register a route.
     *
     * @param string|array $methods
     * @param string $uri
     * @param mixed $action
     * @return void
     */
    public static function addRoute($methods, string $uri, $action): void
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

    /**
     * Register GET route.
     *
     * @param string $uri
     * @param mixed $action
     * @return void
     */
    public static function get(string $uri, $action): void
    {
        self::addRoute(['GET', 'HEAD'], $uri, $action);
    }

    /**
     * Register POST route.
     *
     * @param string $uri
     * @param mixed $action
     * @return void
     */
    public static function post(string $uri, $action): void
    {
        self::addRoute('POST', $uri, $action);
    }

    /**
     * Register PUT route.
     *
     * @param string $uri
     * @param mixed $action
     * @return void
     */
    public static function put(string $uri, $action): void
    {
        self::addRoute('PUT', $uri, $action);
    }

    /**
     * Register DELETE route.
     *
     * @param string $uri
     * @param mixed $action
     * @return void
     */
    public static function delete(string $uri, $action): void
    {
        self::addRoute('DELETE', $uri, $action);
    }

    /**
     * Register route for multiple methods.
     *
     * @param array $methods
     * @param string $uri
     * @param mixed $action
     * @return void
     */
    public static function match(array $methods, string $uri, $action): void
    {
        self::addRoute($methods, $uri, $action);
    }

    /**
     * Register route for all HTTP methods.
     *
     * @param string $uri
     * @param mixed $action
     * @return void
     */
    public static function any(string $uri, $action): void
    {
        self::addRoute(['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'HEAD', 'OPTIONS'], $uri, $action);
    }

    /**
     * Convert URI with placeholders (e.g. {id}, {slug}) to regular expression.
     *
     * @param string $uri
     * @return string
     */
    protected static function compilePattern(string $uri): string
    {
        // Replace {param} with regex capture group
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $uri);
        return '#^' . $pattern . '$#';
    }

    /**
     * Set custom 404 handler.
     *
     * @param callable $handler
     * @return void
     */
    public static function setNotFoundHandler(callable $handler): void
    {
        self::$notFoundHandler = $handler;
    }

    /**
     * Set custom 405 handler.
     *
     * @param callable $handler
     * @return void
     */
    public static function setMethodNotAllowedHandler(callable $handler): void
    {
        self::$methodNotAllowedHandler = $handler;
    }

    /**
     * Load route definitions from a file.
     *
     * @param string $filePath
     * @return void
     */
    public static function loadRoutes(string $filePath): void
    {
        if (file_exists($filePath)) {
            require_once $filePath;
        }
    }

    /**
     * Get all registered routes.
     *
     * @return array
     */
    public static function getRoutes(): array
    {
        return self::$routes;
    }

    /**
     * Clear all registered routes.
     *
     * @return void
     */
    public static function clear(): void
    {
        self::$routes = [];
    }

    /**
     * Dispatch the current HTTP request.
     *
     * @param string|null $requestMethod
     * @param string|null $requestUri
     * @return mixed
     */
    public static function dispatch(?string $requestMethod = null, ?string $requestUri = null)
    {
        if ($requestMethod === null) {
            $requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
            // Method spoofing for HTML forms (_method: PUT/DELETE/PATCH)
            if ($requestMethod === 'POST' && isset($_POST['_method'])) {
                $spoofedMethod = strtoupper((string)$_POST['_method']);
                if (in_array($spoofedMethod, ['PUT', 'DELETE', 'PATCH', 'GET', 'HEAD', 'OPTIONS'], true)) {
                    $requestMethod = $spoofedMethod;
                }
            }
        }

        if ($requestUri === null) {
            $requestUri = $_SERVER['REQUEST_URI'] ?? '/';
        }

        // Strip query string and sanitize URI
        $path = parse_url($requestUri, PHP_URL_PATH) ?? '/';
        $path = '/' . trim($path, '/');

        $methodMatchFound = false;

        foreach (self::$routes as $route) {
            if (preg_match($route['pattern'], $path, $matches)) {
                if (in_array(strtoupper($requestMethod), $route['methods'], true)) {
                    // Extract named parameters
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

    /**
     * Execute route action (Closure, string 'Controller@method', or [Controller, method]).
     *
     * @param mixed $action
     * @param array $params
     * @return mixed
     */
    protected static function executeAction($action, array $params = [])
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

    /**
     * Resolve fully qualified controller class name.
     *
     * @param string $controllerName
     * @return string
     */
    protected static function resolveControllerClass(string $controllerName): string
    {
        if (strpos($controllerName, '\\') === 0) {
            return ltrim($controllerName, '\\');
        }

        // Already namespaced?
        if (class_exists($controllerName)) {
            return $controllerName;
        }

        // Check standard App\Controllers
        $appController = "App\\Controllers\\{$controllerName}";
        if (class_exists($appController)) {
            return $appController;
        }

        // Check Modules (e.g. App\Modules\Product\Controllers\ProductController)
        if (preg_match('/^([A-Z][a-zA-Z0-9_]*)Controller$/', $controllerName, $m)) {
            $moduleName = $m[1];
            $moduleController = "App\\Modules\\{$moduleName}\\Controllers\\{$controllerName}";
            if (class_exists($moduleController)) {
                return $moduleController;
            }
        }

        return $appController;
    }

    /**
     * Handle 404 Not Found response.
     *
     * @return mixed
     */
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

    /**
     * Handle 405 Method Not Allowed response.
     *
     * @return mixed
     */
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
