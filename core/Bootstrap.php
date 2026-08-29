<?php

namespace Core;

use Core\Libraries\Database\Database;
use Core\Libraries\Modules\Module;
use Core\Libraries\Router\Router;
use Core\Libraries\Security\Security;
use Core\Libraries\Session\Session;
use Throwable;
use ErrorException;

class Bootstrap
{
    /**
     * @var string
     */
    protected static $rootDir;

    /**
     * Prevent direct instantiation.
     */
    private function __construct()
    {
    }

    /**
     * Run the application lifecycle.
     *
     * @return void
     */
    public static function run(): void
    {
        if (!defined('ROOT_PATH')) {
            define('ROOT_PATH', dirname(__DIR__));
            define('APP_PATH', ROOT_PATH . '/app');
            define('CORE_PATH', ROOT_PATH . '/core');
            define('PUBLIC_PATH', ROOT_PATH . '/public');
            define('STORAGE_PATH', ROOT_PATH . '/storage');
            define('CONFIG_PATH', APP_PATH . '/Config');
            define('VIEW_PATH', APP_PATH . '/Views');
            define('MODULES_PATH', APP_PATH . '/Modules');
            define('ROUTES_PATH', ROOT_PATH . '/routes');
        }

        self::$rootDir = ROOT_PATH;

        self::registerAutoloader();
        self::loadHelpers();
        self::registerErrorHandlers();
        self::initEnvironment();
        self::initSession();
        self::checkCsrfProtection();
        self::initModules();
        self::loadRoutes();
        self::dispatch();
    }

    /**
     * Register PSR-4 autoloader with built-in fallback.
     *
     * @return void
     */
    protected static function registerAutoloader(): void
    {
        // 1. Check Composer autoload
        $composerAutoload = ROOT_PATH . '/vendor/autoload.php';
        if (file_exists($composerAutoload)) {
            require_once $composerAutoload;
        }

        // 2. Built-in PSR-4 Autoloader fallback (ensures framework works even without vendor/autoload.php)
        spl_autoload_register(function ($class) {
            $prefixes = [
                'App\\'  => APP_PATH . '/',
                'Core\\' => CORE_PATH . '/',
            ];

            foreach ($prefixes as $prefix => $baseDir) {
                $len = strlen($prefix);
                if (strncmp($prefix, $class, $len) !== 0) {
                    continue;
                }

                $relativeClass = substr($class, $len);
                $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }

            return false;
        });
    }

    /**
     * Load global helper functions.
     *
     * @return void
     */
    protected static function loadHelpers(): void
    {
        $helpersPath = CORE_PATH . '/Helpers/helpers.php';
        if (file_exists($helpersPath)) {
            require_once $helpersPath;
        }
    }

    /**
     * Register error and exception handlers with Dev/Prod discrimination.
     *
     * @return void
     */
    protected static function registerErrorHandlers(): void
    {
        error_reporting(E_ALL);

        set_error_handler(function ($severity, $message, $file, $line) {
            if (!(error_reporting() & $severity)) {
                return;
            }
            throw new ErrorException($message, 0, $severity, $file, $line);
        });

        set_exception_handler(function (Throwable $e) {
            self::handleException($e);
        });
    }

    /**
     * Handle uncaught exceptions.
     *
     * @param Throwable $e
     * @return void
     */
    protected static function handleException(Throwable $e): void
    {
        $isDebug = config('app.debug', true);

        // Always log error to storage/logs
        self::logException($e);

        if (!headers_sent()) {
            http_response_code(500);
        }

        if ($isDebug) {
            self::renderDevelopmentException($e);
        } else {
            self::renderProductionException();
        }
        exit;
    }

    /**
     * Log exception into storage/logs/app-YYYY-MM-DD.log.
     *
     * @param Throwable $e
     * @return void
     */
    protected static function logException(Throwable $e): void
    {
        $logDir = STORAGE_PATH . '/logs';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }

        $logFile = $logDir . '/app-' . date('Y-m-d') . '.log';
        $time = date('Y-m-d H:i:s');
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        $entry = sprintf(
            "[%s] [%s %s] [IP: %s] %s in %s:%d\nStack Trace:\n%s\n\n",
            $time,
            $method,
            $uri,
            $ip,
            $e->getMessage(),
            $e->getFile(),
            $e->getLine(),
            $e->getTraceAsString()
        );

        file_put_contents($logFile, $entry, FILE_APPEND | LOCK_EX);
    }

    /**
     * Render detailed development error page.
     *
     * @param Throwable $e
     * @return void
     */
    protected static function renderDevelopmentException(Throwable $e): void
    {
        $class = get_class($e);
        $message = htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
        $file = htmlspecialchars($e->getFile(), ENT_QUOTES, 'UTF-8');
        $line = $e->getLine();
        $trace = htmlspecialchars($e->getTraceAsString(), ENT_QUOTES, 'UTF-8');

        echo <<<HTML
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Error - {$class}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; background: #0f172a; color: #e2e8f0; line-height: 1.6; padding: 30px; }
        .container { max-width: 1100px; margin: 0 auto; background: #1e293b; border-radius: 12px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.5); overflow: hidden; border: 1px solid #334155; }
        .header { background: #ef4444; color: #fff; padding: 25px 30px; }
        .header .badge { display: inline-block; background: rgba(0,0,0,0.25); padding: 4px 10px; border-radius: 4px; font-size: 13px; font-weight: 600; text-transform: uppercase; margin-bottom: 8px; }
        .header h1 { font-size: 22px; font-weight: 700; word-break: break-all; }
        .body { padding: 30px; }
        .location { background: #0f172a; padding: 16px 20px; border-radius: 8px; border-left: 4px solid #ef4444; font-family: monospace; font-size: 14px; margin-bottom: 25px; word-break: break-all; }
        .location span { color: #38bdf8; font-weight: bold; }
        .section-title { font-size: 16px; font-weight: 600; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px; }
        pre.trace { background: #090d16; color: #f1f5f9; padding: 20px; border-radius: 8px; font-family: monospace; font-size: 13px; overflow-x: auto; line-height: 1.6; border: 1px solid #1e293b; }
        .meta-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-top: 25px; }
        .meta-item { background: #0f172a; padding: 12px 16px; border-radius: 8px; border: 1px solid #334155; font-size: 13px; }
        .meta-label { color: #64748b; font-size: 11px; text-transform: uppercase; font-weight: bold; }
        .meta-val { color: #38bdf8; font-family: monospace; font-size: 14px; margin-top: 4px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <span class="badge">Uncaught {$class}</span>
            <h1>{$message}</h1>
        </div>
        <div class="body">
            <div class="section-title">Hata Konumu</div>
            <div class="location">
                {$file} : <span>Satır {$line}</span>
            </div>

            <div class="section-title">Stack Trace</div>
            <pre class="trace">{$trace}</pre>

            <div class="meta-grid">
                <div class="meta-item">
                    <div class="meta-label">PHP Versiyonu</div>
                    <div class="meta-val">PHP 7.3+ (Runtime: PHP 7.3)</div>
                </div>
                <div class="meta-item">
                    <div class="meta-label">HTTP Metodu</div>
                    <div class="meta-val">{$_SERVER['REQUEST_METHOD']}</div>
                </div>
                <div class="meta-item">
                    <div class="meta-label">İstek URL</div>
                    <div class="meta-val">{$_SERVER['REQUEST_URI']}</div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
HTML;
    }

    /**
     * Render safe production 500 error page.
     *
     * @return void
     */
    protected static function renderProductionException(): void
    {
        echo <<<HTML
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>500 - Sunucu Hatası</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #f8fafc; color: #1e293b; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; }
        .box { text-align: center; max-width: 500px; padding: 40px; background: white; border-radius: 12px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); }
        h1 { font-size: 48px; color: #ef4444; margin-bottom: 10px; }
        p { color: #64748b; margin-bottom: 25px; }
        a { display: inline-block; background: #3b82f6; color: white; padding: 10px 20px; border-radius: 6px; text-decoration: none; font-weight: 500; }
    </style>
</head>
<body>
    <div class="box">
        <h1>500</h1>
        <h2>Beklenmeyen Bir Hata Oluştu</h2>
        <p>Sistem yöneticileri bilgilendirildi. Lütfen daha sonra tekrar deneyiniz.</p>
        <a href="/">Ana Sayfaya Dön</a>
    </div>
</body>
</html>
HTML;
    }

    /**
     * Initialize environment settings.
     *
     * @return void
     */
    protected static function initEnvironment(): void
    {
        $timezone = config('app.timezone', 'Europe/Istanbul');
        date_default_timezone_set($timezone);

        $charset = config('app.charset', 'UTF-8');
        mb_internal_encoding($charset);
    }

    /**
     * Start and configure session.
     *
     * @return void
     */
    protected static function initSession(): void
    {
        Session::start();
    }

    /**
     * Check CSRF token for mutating requests.
     *
     * @return void
     */
    protected static function checkCsrfProtection(): void
    {
        Security::checkCsrf();
    }

    /**
     * Register & boot application modules.
     *
     * @return void
     */
    protected static function initModules(): void
    {
        $modules = config('app.modules', []);
        foreach ($modules as $module) {
            Module::register($module);
        }
        Module::boot();
    }

    /**
     * Load main application web routes.
     *
     * @return void
     */
    protected static function loadRoutes(): void
    {
        $webRoutes = ROUTES_PATH . '/web.php';
        if (file_exists($webRoutes)) {
            Router::loadRoutes($webRoutes);
        }
    }

    /**
     * Dispatch HTTP request through router.
     *
     * @return void
     */
    protected static function dispatch(): void
    {
        Router::dispatch();
    }
}
