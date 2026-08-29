<?php

namespace Core\Libraries\Session;

class Session
{
    /**
     * @var string
     */
    protected static $flashKey = '_flash_data';

    /**
     * @var string
     */
    protected static $csrfKey = '_csrf_token';

    /**
     * Prevent instantiation.
     */
    private function __construct()
    {
    }

    /**
     * Start the session with secure settings.
     *
     * @return void
     */
    public static function start(): void
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

        // Initialize flash session storage
        if (!isset($_SESSION[self::$flashKey])) {
            $_SESSION[self::$flashKey] = [];
        }

        // Automatically create CSRF token if not set
        if (empty($_SESSION[self::$csrfKey])) {
            $_SESSION[self::$csrfKey] = bin2hex(random_bytes(32));
        }
    }

    /**
     * Set a session key-value pair.
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public static function set(string $key, $value): void
    {
        self::start();
        $_SESSION[$key] = $value;
    }

    /**
     * Get a session value by key.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        self::start();
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Check if a session key exists.
     *
     * @param string $key
     * @return bool
     */
    public static function has(string $key): bool
    {
        self::start();
        return isset($_SESSION[$key]);
    }

    /**
     * Remove a key from session.
     *
     * @param string $key
     * @return void
     */
    public static function remove(string $key): void
    {
        self::start();
        unset($_SESSION[$key]);
    }

    /**
     * Get all session data.
     *
     * @return array
     */
    public static function all(): array
    {
        self::start();
        return $_SESSION;
    }

    /**
     * Store flash data for the next request.
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public static function flash(string $key, $value): void
    {
        self::start();
        $_SESSION[self::$flashKey][$key] = $value;
    }

    /**
     * Get flash data and remove it.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function getFlash(string $key, $default = null)
    {
        self::start();
        if (isset($_SESSION[self::$flashKey][$key])) {
            $value = $_SESSION[self::$flashKey][$key];
            unset($_SESSION[self::$flashKey][$key]);
            return $value;
        }
        return $default;
    }

    /**
     * Check if flash key exists.
     *
     * @param string $key
     * @return bool
     */
    public static function hasFlash(string $key): bool
    {
        self::start();
        return isset($_SESSION[self::$flashKey][$key]);
    }

    /**
     * Generate or regenerate CSRF token.
     *
     * @return string
     */
    public static function generateCsrfToken(): string
    {
        self::start();
        $token = bin2hex(random_bytes(32));
        $_SESSION[self::$csrfKey] = $token;
        return $token;
    }

    /**
     * Get current CSRF token.
     *
     * @return string
     */
    public static function csrfToken(): string
    {
        self::start();
        if (empty($_SESSION[self::$csrfKey])) {
            return self::generateCsrfToken();
        }
        return $_SESSION[self::$csrfKey];
    }

    /**
     * Verify CSRF token.
     *
     * @param string|null $token
     * @return bool
     */
    public static function verifyCsrfToken(?string $token): bool
    {
        if (empty($token)) {
            return false;
        }
        return hash_equals(self::csrfToken(), $token);
    }

    /**
     * Regenerate session ID.
     *
     * @param bool $deleteOldSession
     * @return bool
     */
    public static function regenerate(bool $deleteOldSession = true): bool
    {
        self::start();
        return session_regenerate_id($deleteOldSession);
    }

    /**
     * Destroy the session completely.
     *
     * @return void
     */
    public static function destroy(): void
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
