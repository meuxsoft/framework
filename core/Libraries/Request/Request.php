<?php

namespace Core\Libraries\Request;

use Core\Libraries\Session\Session;

class Request
{
    private function __construct()
    {
    }

    public static function method()
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        if ($method === 'POST' && isset($_POST['_method'])) {
            $spoofed = strtoupper($_POST['_method']);
            if (in_array($spoofed, ['PUT', 'DELETE', 'PATCH', 'GET', 'HEAD', 'OPTIONS'], true)) {
                return $spoofed;
            }
        }
        return strtoupper($method);
    }

    public static function isGet()
    {
        return self::method() === 'GET';
    }

    public static function isPost()
    {
        return self::method() === 'POST';
    }

    public static function isPut()
    {
        return self::method() === 'PUT';
    }

    public static function isDelete()
    {
        return self::method() === 'DELETE';
    }

    public static function isAjax()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    public static function get($key = null, $default = null)
    {
        if ($key === null) {
            return $_GET;
        }
        return $_GET[$key] ?? $default;
    }

    public static function post($key = null, $default = null)
    {
        if ($key === null) {
            return $_POST;
        }
        return $_POST[$key] ?? $default;
    }

    public static function input($key = null, $default = null)
    {
        $all = self::all();
        if ($key === null) {
            return $all;
        }
        return $all[$key] ?? $default;
    }

    public static function all()
    {
        $json = [];
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (strpos($contentType, 'application/json') !== false) {
            $raw = file_get_contents('php://input');
            $json = json_decode($raw, true) ?: [];
        }

        return array_merge($_GET, $_POST, $json);
    }

    public static function files($key = null)
    {
        if ($key === null) {
            return $_FILES;
        }
        return $_FILES[$key] ?? null;
    }

    public static function hasFile($key)
    {
        return isset($_FILES[$key]) && $_FILES[$key]['error'] !== UPLOAD_ERR_NO_FILE;
    }

    public static function uri()
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        return parse_url($uri, PHP_URL_PATH) ?? '/';
    }

    public static function ip()
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

    public static function header($key, $default = null)
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

    public static function flashInput()
    {
        $data = self::all();
        unset($data['password'], $data['password_confirmation'], $data['_csrf_token']);
        Session::flash('_old_input', $data);
    }
}
