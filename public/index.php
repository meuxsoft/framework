<?php

define('FRAMEWORK_START', microtime(true));

// Set Application Root Path
define('ROOT_PATH', dirname(__DIR__));

// Bootstrap Application
require_once ROOT_PATH . '/core/Bootstrap.php';

\Core\Bootstrap::run();
