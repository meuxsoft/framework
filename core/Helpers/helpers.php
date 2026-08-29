<?php

use Core\Helpers\Helper;
use Core\Libraries\Layout\Layout;
use Core\Libraries\Session\Session;
use Core\Libraries\Request\Request;
use Core\Libraries\Security\Security;

if (!function_exists('config')) {
    function config($key = null, $default = null)
    {
        return Helper::config($key, $default);
    }
}

if (!function_exists('url')) {
    function url($path = '')
    {
        return Helper::url($path);
    }
}

if (!function_exists('asset')) {
    function asset($path)
    {
        return Helper::asset($path);
    }
}

if (!function_exists('redirect')) {
    function redirect($url, $status = 302)
    {
        Helper::redirect($url, $status);
    }
}

if (!function_exists('back')) {
    function back()
    {
        Helper::back();
    }
}

if (!function_exists('view')) {
    function view($view, $data = [], $layout = 'main')
    {
        return Layout::render($layout, $view, $data);
    }
}

if (!function_exists('e')) {
    function e($value)
    {
        return Security::escape($value);
    }
}

if (!function_exists('request')) {
    function request($key = null, $default = null)
    {
        return Request::input($key, $default);
    }
}

if (!function_exists('session')) {
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
    function flash($key, $value = null)
    {
        if ($value !== null) {
            Session::flash($key, $value);
            return null;
        }
        return Session::getFlash($key);
    }
}

if (!function_exists('old')) {
    function old($key, $default = null)
    {
        $oldInput = Session::get('_old_input', []);
        return $oldInput[$key] ?? $default;
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token()
    {
        return Session::csrfToken();
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field()
    {
        $token = csrf_token();
        return '<input type="hidden" name="_csrf_token" value="' . e($token) . '">';
    }
}

if (!function_exists('method_field')) {
    function method_field($method)
    {
        return '<input type="hidden" name="_method" value="' . strtoupper(e($method)) . '">';
    }
}
