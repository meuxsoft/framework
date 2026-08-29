<?php

namespace Core\Libraries\Request;

use Core\Libraries\Session\Session;

class Request
{
    /**
     * Prevent direct instantiation.
     */
    private function __construct()
    {
    }

    /**
     * Get HTTP request method (handles method spoofing).
     *
     * @return string
     */
    public static function method(): string
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        if ($method === 'POST' && isset($_POST['_method'])) {
            $spoofed = strtoupper((string)$_POST['_method']);
            if (in_array($spoofed, ['PUT', 'DELETE', 'PATCH', 'GET', 'HEAD', 'OPTIONS'], true)) {
                return $spoofed;
            }
        }
        return strtoupper($method);
    }

    /**
     * Check if request method is GET.
     *
     * @return bool
     */
    public static function isGet(): bool
    {
        return self::method() === 'GET';
    }

    /**
     * Check if request method is POST.
     *
     * @return bool
     */
    public static function isPost(): bool
    {
        return self::method() === 'POST';
    }

    /**
     * Check if request method is PUT.
     *
     * @return bool
     */
    public static function isPut(): bool
    {
        return self::method() === 'PUT';
    }

    /**
     * Check if request method is DELETE.
     *
     * @return bool
     */
    public static function isDelete(): bool
    {
        return self::method() === 'DELETE';
    }

    /**
     * Check if request is AJAX.
     *
     * @return bool
     */
    public static function isAjax(): bool
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * Get GET query parameter or all GET parameters.
     *
     * @param string|null $key
     * @param mixed $default
     * @return mixed
     */
    public static function get(?string $key = null, $default = null)
    {
        if ($key === null) {
            return $_GET;
        }
        return $_GET[$key] ?? $default;
    }

    /**
     * Get POST parameter or all POST parameters.
     *
     * @param string|null $key
     * @param mixed $default
     * @return mixed
     */
    public static function post(?string $key = null, $default = null)
    {
        if ($key === null) {
            return $_POST;
        }
        return $_POST[$key] ?? $default;
    }

    /**
     * Get parameter from POST, GET, or JSON body.
     *
     * @param string|null $key
     * @param mixed $default
     * @return mixed
     */
    public static function input(?string $key = null, $default = null)
    {
        $all = self::all();
        if ($key === null) {
            return $all;
        }
        return $all[$key] ?? $default;
    }

    /**
     * Get all incoming request data merged (JSON body + POST + GET).
     *
     * @return array
     */
    public static function all(): array
    {
        $json = [];
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (strpos($contentType, 'application/json') !== false) {
            $raw = file_get_contents('php://input');
            $json = json_decode($raw, true) ?: [];
        }

        return array_merge($_GET, $_POST, $json);
    }

    /**
     * Get uploaded files array or specific file.
     *
     * @param string|null $key
     * @return mixed
     */
    public static function files(?string $key = null)
    {
        if ($key === null) {
            return $_FILES;
        }
        return $_FILES[$key] ?? null;
    }

    /**
     * Check if a file was uploaded.
     *
     * @param string $key
     * @return bool
     */
    public static function hasFile(string $key): bool
    {
        return isset($_FILES[$key]) && $_FILES[$key]['error'] !== UPLOAD_ERR_NO_FILE;
    }

    /**
     * Get request URI.
     *
     * @return string
     */
    public static function uri(): string
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        return parse_url($uri, PHP_URL_PATH) ?? '/';
    }

    /**
     * Get client IP address.
     *
     * @return string
     */
    public static function ip(): string
    {
        $headers = [
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        ];

        foreach ($headers as $header) {
            if (!empty($_SERVER[$header])) {
                $ips = explode(',', $_SERVER[$header]);
                foreach ($ips as $rawIp) {
                    $ip = trim($rawIp);
                    if (filter_var($ip, FILTER_VALIDATE_IP)) {
                        return $ip;
                    }
                }
            }
        }

        return '127.0.0.1';
    }

    /**
     * Get request header.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function header(string $key, $default = null)
    {
        $headerKey = 'HTTP_' . strtoupper(str_replace('-', '_', $key));
        if (isset($_SERVER[$headerKey])) {
            return $_SERVER[$headerKey];
        }
        if (isset($_SERVER[$key])) {
            return $_SERVER[$key];
        }
        return $default;
    }

    /**
     * Flash current input into session for old() helper.
     *
     * @return void
     */
    public static function flashInput(): void
    {
        $data = self::all();
        // Do not flash passwords
        unset($data['password'], $data['password_confirmation'], $data['_csrf_token']);
        Session::flash('_old_input', $data);
    }
}
