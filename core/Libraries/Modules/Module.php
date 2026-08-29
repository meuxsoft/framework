<?php

namespace Core\Libraries\Modules;

use Core\Libraries\Router\Router;
use RuntimeException;

class Module
{
    /**
     * @var array
     */
    protected static $modules = [];

    /**
     * Get modules base directory path.
     *
     * @return string
     */
    public static function getModulesPath(): string
    {
        return defined('MODULES_PATH') ? MODULES_PATH : dirname(__DIR__, 3) . '/app/Modules';
    }

    /**
     * Prevent direct instantiation.
     */
    private function __construct()
    {
    }

    /**
     * Register a module by name.
     *
     * @param string $name Module folder name (e.g. 'Product', 'Auth', 'Admin')
     * @param bool $active
     * @return void
     */
    public static function register(string $name, bool $active = true): void
    {
        $base = self::getModulesPath();

        self::$modules[$name] = [
            'name'      => $name,
            'active'    => $active,
            'booted'    => false,
            'path'      => $base . '/' . $name,
            'routes'    => $base . '/' . $name . '/routes.php',
            'bootstrap' => $base . '/' . $name . '/Module.php',
        ];
    }

    /**
     * Boot all registered and active modules.
     *
     * @return void
     */
    public static function boot(): void
    {
        foreach (self::$modules as $name => &$module) {
            if (!$module['active'] || $module['booted']) {
                continue;
            }

            // Execute module bootstrap class if present
            if (file_exists($module['bootstrap'])) {
                require_once $module['bootstrap'];
                $moduleClass = "App\\Modules\\{$name}\\Module";
                if (class_exists($moduleClass) && method_exists($moduleClass, 'boot')) {
                    $moduleClass::boot();
                }
            }

            // Register module routes
            if (file_exists($module['routes'])) {
                Router::loadRoutes($module['routes']);
            }

            $module['booted'] = true;
        }
    }

    /**
     * Check if a module is registered and active.
     *
     * @param string $name
     * @return bool
     */
    public static function isActive(string $name): bool
    {
        return isset(self::$modules[$name]) && self::$modules[$name]['active'] === true;
    }

    /**
     * Enable a module.
     *
     * @param string $name
     * @return void
     */
    public static function enable(string $name): void
    {
        if (isset(self::$modules[$name])) {
            self::$modules[$name]['active'] = true;
        } else {
            self::register($name, true);
        }
    }

    /**
     * Disable a module.
     *
     * @param string $name
     * @return void
     */
    public static function disable(string $name): void
    {
        if (isset(self::$modules[$name])) {
            self::$modules[$name]['active'] = false;
        }
    }

    /**
     * Get all registered modules.
     *
     * @return array
     */
    public static function all(): array
    {
        return self::$modules;
    }
}
