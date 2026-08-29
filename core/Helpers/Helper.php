<?php

namespace Core\Helpers;

class Helper
{
    protected static $configCache = [];

    private function __construct()
    {
    }

    public static function config($key = null, $default = null)
    {
        if ($key === null) {
            return self::$configCache;
        }

        $parts = explode('.', $key);
        $configFile = array_shift($parts);

        if (!isset(self::$configCache[$configFile])) {
            $configPath = CONFIG_PATH . '/' . $configFile . '.php';
            if (file_exists($configPath)) {
                self::$configCache[$configFile] = require $configPath;
            } else {
                self::$configCache[$configFile] = [];
            }
        }

        $current = self::$configCache[$configFile];

        foreach ($parts as $part) {
            if (is_array($current) && array_key_exists($part, $current)) {
                $current = $current[$part];
            } else {
                return $default;
            }
        }

        return $current;
    }

    public static function url($path = '')
    {
        $baseUrl = rtrim(self::config('app.url', 'http://localhost'), '/');
        $cleanPath = ltrim($path, '/');
        return $cleanPath !== '' ? "{$baseUrl}/{$cleanPath}" : $baseUrl;
    }

    public static function asset($path)
    {
        $cleanPath = ltrim($path, '/');
        return '/assets/' . $cleanPath;
    }

    public static function redirect($url, $status = 302)
    {
        if (strpos($url, 'http://') !== 0 && strpos($url, 'https://') !== 0) {
            $url = self::url($url);
        }

        http_response_code($status);
        header("Location: {$url}");
        exit;
    }

    public static function back()
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        self::redirect($referer);
    }
}
