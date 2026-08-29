<?php

namespace Core\Libraries\Modules;

use Core\Libraries\Router\Router;

class Module
{
    protected static $modules = [];

    private function __construct()
    {
    }

    public static function register($name, $active = true)
    {
        $base = MODULES_PATH;

        self::$modules[$name] = [
            'name'      => $name,
            'active'    => $active,
            'booted'    => false,
            'path'      => $base . '/' . $name,
            'routes'    => $base . '/' . $name . '/routes.php',
            'bootstrap' => $base . '/' . $name . '/Module.php',
        ];
    }

    public static function boot()
    {
        foreach (self::$modules as $name => &$module) {
            if (!$module['active'] || $module['booted']) {
                continue;
            }

            if (file_exists($module['bootstrap'])) {
                require_once $module['bootstrap'];
                $moduleClass = "App\\Modules\\{$name}\\Module";
                if (class_exists($moduleClass) && method_exists($moduleClass, 'boot')) {
                    $moduleClass::boot();
                }
            }

            if (file_exists($module['routes'])) {
                Router::loadRoutes($module['routes']);
            }

            $module['booted'] = true;
        }
    }

    public static function isActive($name)
    {
        return isset(self::$modules[$name]) && self::$modules[$name]['active'] === true;
    }

    public static function enable($name)
    {
        if (isset(self::$modules[$name])) {
            self::$modules[$name]['active'] = true;
        } else {
            self::register($name, true);
        }
    }

    public static function disable($name)
    {
        if (isset(self::$modules[$name])) {
            self::$modules[$name]['active'] = false;
        }
    }

    public static function all()
    {
        return self::$modules;
    }
}
