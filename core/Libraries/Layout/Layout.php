<?php

namespace Core\Libraries\Layout;

use RuntimeException;

class Layout
{
    /**
     * Get base views directory path.
     *
     * @return string
     */
    public static function getViewsPath(): string
    {
        return defined('VIEW_PATH') ? VIEW_PATH : dirname(__DIR__, 3) . '/app/Views';
    }

    /**
     * Get modules directory path.
     *
     * @return string
     */
    public static function getModulesPath(): string
    {
        return defined('MODULES_PATH') ? MODULES_PATH : dirname(__DIR__, 3) . '/app/Modules';
    }

    /**
     * Prevent instantiation.
     */
    private function __construct()
    {
    }

    /**
     * Render a view wrapped in a layout.
     *
     * @param string|null $layout Layout name (e.g. 'main', 'admin') or null for no layout
     * @param string $view View path (e.g. 'home.index' or 'Product::index' or 'products.index')
     * @param array $data Data array passed to view & layout
     * @return string
     */
    public static function render(?string $layout, string $view, array $data = []): string
    {
        $content = self::view($view, $data);

        if ($layout === null || $layout === '') {
            echo $content;
            return $content;
        }

        $layoutFile = self::getViewsPath() . '/layouts/' . str_replace('.', '/', $layout) . '.php';
        if (!file_exists($layoutFile)) {
            throw new RuntimeException("Layout file not found: [{$layoutFile}]");
        }

        $mergedData = array_merge($data, ['content' => $content]);
        extract($mergedData, EXTR_SKIP);

        ob_start();
        include $layoutFile;
        $output = ob_get_clean();

        echo $output;
        return $output;
    }

    /**
     * Render only the view and return output string.
     *
     * @param string $view
     * @param array $data
     * @return string
     */
    public static function view(string $view, array $data = []): string
    {
        $viewFile = self::resolveViewPath($view);

        if (!file_exists($viewFile)) {
            throw new RuntimeException("View file not found: [{$viewFile}]");
        }

        extract($data, EXTR_SKIP);

        ob_start();
        include $viewFile;
        return ob_get_clean();
    }

    /**
     * Resolve view path from string name (handles dot notation & module syntax).
     *
     * @param string $view
     * @return string
     */
    public static function resolveViewPath(string $view): string
    {
        $viewsPath = self::getViewsPath();
        $modulesPath = self::getModulesPath();

        // Check Module syntax: "Module::view.name"
        if (strpos($view, '::') !== false) {
            [$module, $moduleView] = explode('::', $view, 2);
            $normalizedView = str_replace('.', '/', $moduleView);
            return $modulesPath . '/' . $module . '/Views/' . $normalizedView . '.php';
        }

        $normalized = str_replace('.', '/', $view);

        // Check if direct path under app/Views
        $standardPath = $viewsPath . '/' . $normalized . '.php';
        if (file_exists($standardPath)) {
            return $standardPath;
        }

        // Check if first segment corresponds to an active module (e.g. 'products.index' -> 'Product/Views/index.php')
        $parts = explode('/', $normalized);
        if (count($parts) > 0) {
            $firstSegment = ucfirst(rtrim($parts[0], 's')); // e.g. 'products' -> 'Product'
            $subPath = implode('/', array_slice($parts, 1));
            $moduleViewPath = $modulesPath . '/' . $firstSegment . '/Views/' . $subPath . '.php';
            if (file_exists($moduleViewPath)) {
                return $moduleViewPath;
            }

            // Direct module match e.g. 'Product/Views/index.php'
            $directModulePath = $modulesPath . '/' . $parts[0] . '/Views/' . $subPath . '.php';
            if (file_exists($directModulePath)) {
                return $directModulePath;
            }
        }

        return $standardPath;
    }

    /**
     * Send a JSON response.
     *
     * @param mixed $data
     * @param int $status
     * @param array $headers
     * @return void
     */
    public static function json($data, int $status = 200, array $headers = []): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');

        foreach ($headers as $key => $value) {
            header("{$key}: {$value}");
        }

        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }
}
