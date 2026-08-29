<?php

namespace Core\Helpers;

use Core\Libraries\Layout\Layout;
use Core\Libraries\Session\Session;
use Core\Libraries\Request\Request;
use Core\Libraries\Security\Security;

class Helper
{
    /**
     * @var array
     */
    protected static $configCache = [];

    /**
     * Prevent direct instantiation.
     */
    private function __construct()
    {
    }

    /**
     * Load or retrieve config values with dot notation (e.g. 'app.name').
     *
     * @param string|null $key
     * @param mixed $default
     * @return mixed
     */
    public static function config(?string $key = null, $default = null)
    {
        if ($key === null) {
            return self::$configCache;
        }

        $parts = explode('.', $key);
        $configFile = array_shift($parts);

        if (!isset(self::$configCache[$configFile])) {
            $configDir = defined('CONFIG_PATH') ? CONFIG_PATH : dirname(__DIR__, 2) . '/app/Config';
            $configPath = $configDir . '/' . $configFile . '.php';
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

    /**
     * Generate full application URL.
     *
     * @param string $path
     * @return string
     */
    public static function url(string $path = ''): string
    {
        $baseUrl = rtrim(self::config('app.url', 'http://localhost'), '/');
        $cleanPath = ltrim($path, '/');
        return $cleanPath !== '' ? "{$baseUrl}/{$cleanPath}" : $baseUrl;
    }

    /**
     * Generate asset URL.
     *
     * @param string $path
     * @return string
     */
    public static function asset(string $path): string
    {
        $cleanPath = ltrim($path, '/');
        return '/assets/' . $cleanPath;
    }

    /**
     * Redirect to another URL or route.
     *
     * @param string $url
     * @param int $status
     * @return void
     */
    public static function redirect(string $url, int $status = 302): void
    {
        if (strpos($url, 'http://') !== 0 && strpos($url, 'https://') !== 0) {
            $url = self::url($url);
        }

        http_response_code($status);
        header("Location: {$url}");
        exit;
    }

    /**
     * Redirect back to previous page.
     *
     * @return void
     */
    public static function back(): void
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        self::redirect($referer);
    }
}
