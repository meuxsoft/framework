<?php

/**
 * PHP 7.3 Static MVC Framework
 *
 * @author Meux Soft
 * @license MIT
 */

define('FRAMEWORK_START', microtime(true));

// Composer Autoload if installed
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

// Bootstrap Framework Core
require_once __DIR__ . '/../core/Bootstrap.php';

// Launch Application
\Core\Bootstrap::run();
