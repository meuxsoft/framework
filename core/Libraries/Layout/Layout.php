<?php

namespace Core\Libraries\Layout;

use RuntimeException;

class Layout
{
    protected $viewContent = '';
    protected $viewData = [];

    protected function __construct()
    {
    }

    public function content()
    {
        return $this->viewContent;
    }

    public static function render($layout, $view, $data = [])
    {
        $content = self::view($view, $data);

        if ($layout === null || $layout === '') {
            echo $content;
            return $content;
        }

        $layoutFile = VIEW_PATH . '/layouts/' . str_replace('.', '/', $layout) . '.php';
        if (!file_exists($layoutFile)) {
            throw new RuntimeException("Layout file not found: [{$layoutFile}]");
        }

        $instance = new self();
        $instance->viewContent = $content;
        $instance->viewData = $data;

        return $instance->renderLayoutFile($layoutFile, $data);
    }

    protected function renderLayoutFile($layoutFile, $data)
    {
        extract($data, EXTR_SKIP);

        ob_start();
        include $layoutFile;
        $output = ob_get_clean();

        echo $output;
        return $output;
    }

    public static function view($view, $data = [])
    {
        $viewFile = self::resolveViewPath($view);

        if (!file_exists($viewFile)) {
            throw new RuntimeException("View file not found: [{$viewFile}]");
        }

        $realViewFile = realpath($viewFile);
        $realViewsPath = realpath(VIEW_PATH);
        $realModulesPath = is_dir(MODULES_PATH) ? realpath(MODULES_PATH) : false;

        $isValidPath = false;
        if ($realViewFile && $realViewsPath && strpos($realViewFile, $realViewsPath) === 0) {
            $isValidPath = true;
        } elseif ($realViewFile && $realModulesPath && strpos($realViewFile, $realModulesPath) === 0) {
            $isValidPath = true;
        }

        if (!$isValidPath) {
            throw new RuntimeException("Security Exception: Unauthorized view path access [{$view}]");
        }

        extract($data, EXTR_SKIP);

        ob_start();
        include $viewFile;
        return ob_get_clean();
    }

    public static function resolveViewPath($view)
    {
        if (strpos($view, '..') !== false || strpos($view, "\0") !== false) {
            throw new RuntimeException("Security Exception: Directory traversal attempt detected in view name [{$view}]");
        }

        if (strpos($view, '::') !== false) {
            [$module, $moduleView] = explode('::', $view, 2);
            $module = preg_replace('/[^a-zA-Z0-9_-]/', '', $module);
            $normalizedView = str_replace('.', '/', $moduleView);
            return MODULES_PATH . '/' . $module . '/Views/' . $normalizedView . '.php';
        }

        $normalized = str_replace('.', '/', $view);

        $standardPath = VIEW_PATH . '/' . $normalized . '.php';
        if (file_exists($standardPath)) {
            return $standardPath;
        }

        $parts = explode('/', $normalized);
        if (count($parts) > 0) {
            $firstSegment = ucfirst(rtrim($parts[0], 's'));
            $subPath = implode('/', array_slice($parts, 1));
            $moduleViewPath = MODULES_PATH . '/' . $firstSegment . '/Views/' . $subPath . '.php';
            if (file_exists($moduleViewPath)) {
                return $moduleViewPath;
            }

            $directModulePath = MODULES_PATH . '/' . $parts[0] . '/Views/' . $subPath . '.php';
            if (file_exists($directModulePath)) {
                return $directModulePath;
            }
        }

        return $standardPath;
    }

    public static function json($data, $status = 200, $headers = [])
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
