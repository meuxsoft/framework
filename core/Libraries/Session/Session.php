<?php

namespace Core\Libraries\Session;

class Session
{
    protected static $flashKey = '_flash_data';
    protected static $csrfKey = '_csrf_token';

    private function __construct()
    {
    }

    public static function start()
    {
        if (session_status() === PHP_SESSION_NONE) {
            $isSecure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);

            if (!headers_sent()) {
                if (PHP_VERSION_ID >= 70300) {
                    session_set_cookie_params([
                        'lifetime' => 0,
                        'path'     => '/',
                        'domain'   => '',
                        'secure'   => $isSecure,
                        'httponly' => true,
                        'samesite' => 'Lax'
                    ]);
                } else {
                    session_set_cookie_params(0, '/; samesite=Lax', '', $isSecure, true);
                }
            }

            @session_start();
        }

        if (!isset($_SESSION[self::$flashKey])) {
            $_SESSION[self::$flashKey] = [];
        }

        if (empty($_SESSION[self::$csrfKey])) {
            $_SESSION[self::$csrfKey] = bin2hex(random_bytes(32));
        }
    }

    public static function set($key, $value)
    {
        self::start();
        $_SESSION[$key] = $value;
    }

    public static function get($key, $default = null)
    {
        self::start();
        return $_SESSION[$key] ?? $default;
    }

    public static function has($key)
    {
        self::start();
        return isset($_SESSION[$key]);
    }

    public static function remove($key)
    {
        self::start();
        unset($_SESSION[$key]);
    }

    public static function all()
    {
        self::start();
        return $_SESSION;
    }

    public static function flash($key, $value)
    {
        self::start();
        $_SESSION[self::$flashKey][$key] = $value;
    }

    public static function getFlash($key, $default = null)
    {
        self::start();
        if (isset($_SESSION[self::$flashKey][$key])) {
            $value = $_SESSION[self::$flashKey][$key];
            unset($_SESSION[self::$flashKey][$key]);
            return $value;
        }
        return $default;
    }

    public static function hasFlash($key)
    {
        self::start();
        return isset($_SESSION[self::$flashKey][$key]);
    }

    public static function generateCsrfToken()
    {
        self::start();
        $token = bin2hex(random_bytes(32));
        $_SESSION[self::$csrfKey] = $token;
        return $token;
    }

    public static function csrfToken()
    {
        self::start();
        if (empty($_SESSION[self::$csrfKey])) {
            return self::generateCsrfToken();
        }
        return $_SESSION[self::$csrfKey];
    }

    public static function verifyCsrfToken($token)
    {
        if (empty($token)) {
            return false;
        }
        return hash_equals(self::csrfToken(), $token);
    }

    public static function regenerate($deleteOldSession = true)
    {
        self::start();
        return session_regenerate_id($deleteOldSession);
    }

    public static function destroy()
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION = [];
            if (ini_get('session.use_cookies')) {
                $params = session_get_cookie_params();
                setcookie(
                    session_name(),
                    '',
                    time() - 42000,
                    $params['path'],
                    $params['domain'],
                    $params['secure'],
                    $params['httponly']
                );
            }
            session_destroy();
        }
    }
}
