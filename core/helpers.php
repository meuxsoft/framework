<?php

use Core\Helpers\Helper;
use Core\Libraries\Layout\Layout;
use Core\Libraries\Session\Session;
use Core\Libraries\Request\Request;
use Core\Libraries\Security\Security;

if (!function_exists('config')) {
    /**
     * Retrieve configuration setting.
     *
     * @param string|null $key
     * @param mixed $default
     * @return mixed
     */
    function config(?string $key = null, $default = null)
    {
        return Helper::config($key, $default);
    }
}

if (!function_exists('url')) {
    /**
     * Generate URL for application.
     *
     * @param string $path
     * @return string
     */
    function url(string $path = ''): string
    {
        return Helper::url($path);
    }
}

if (!function_exists('asset')) {
    /**
     * Generate URL for assets.
     *
     * @param string $path
     * @return string
     */
    function asset(string $path): string
    {
        return Helper::asset($path);
    }
}

if (!function_exists('redirect')) {
    /**
     * Redirect to URL.
     *
     * @param string $url
     * @param int $status
     * @return void
     */
    function redirect(string $url, int $status = 302): void
    {
        Helper::redirect($url, $status);
    }
}

if (!function_exists('back')) {
    /**
     * Redirect back to previous page.
     *
     * @return void
     */
    function back(): void
    {
        Helper::back();
    }
}

if (!function_exists('view')) {
    /**
     * Render a view with optional layout.
     *
     * @param string $view
     * @param array $data
     * @param string|null $layout
     * @return string
     */
    function view(string $view, array $data = [], ?string $layout = 'main')
    {
        return Layout::render($layout, $view, $data);
    }
}

if (!function_exists('e')) {
    /**
     * XSS escape function.
     *
     * @param mixed $value
     * @return string
     */
    function e($value): string
    {
        return Security::escape($value);
    }
}

if (!function_exists('request')) {
    /**
     * Get input from request.
     *
     * @param string|null $key
     * @param mixed $default
     * @return mixed
     */
    function request(?string $key = null, $default = null)
    {
        return Request::input($key, $default);
    }
}

if (!function_exists('session')) {
    /**
     * Get or set session value.
     *
     * @param string|array|null $key
     * @param mixed $default
     * @return mixed
     */
    function session($key = null, $default = null)
    {
        if ($key === null) {
            return Session::all();
        }

        if (is_array($key)) {
            foreach ($key as $k => $v) {
                Session::set($k, $v);
            }
            return null;
        }

        return Session::get($key, $default);
    }
}

if (!function_exists('flash')) {
    /**
     * Flash data helper or flash message retriever.
     *
     * @param string $key
     * @param mixed $value
     * @return mixed
     */
    function flash(string $key, $value = null)
    {
        if ($value !== null) {
            Session::flash($key, $value);
            return null;
        }
        return Session::getFlash($key);
    }
}

if (!function_exists('old')) {
    /**
     * Retrieve old flashed form input.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function old(string $key, $default = null)
    {
        $oldInput = Session::get('_old_input', []);
        return $oldInput[$key] ?? $default;
    }
}

if (!function_exists('csrf_token')) {
    /**
     * Get current CSRF token string.
     *
     * @return string
     */
    function csrf_token(): string
    {
        return Session::csrfToken();
    }
}

if (!function_exists('csrf_field')) {
    /**
     * Generate hidden HTML input field containing CSRF token.
     *
     * @return string
     */
    function csrf_field(): string
    {
        $token = csrf_token();
        return '<input type="hidden" name="_csrf_token" value="' . e($token) . '">';
    }
}

if (!function_exists('method_field')) {
    /**
     * Generate hidden HTML input for HTTP method spoofing (PUT, DELETE, PATCH).
     *
     * @param string $method
     * @return string
     */
    function method_field(string $method): string
    {
        return '<input type="hidden" name="_method" value="' . strtoupper(e($method)) . '">';
    }
}
