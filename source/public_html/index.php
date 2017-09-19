<?php
@session_start();
//echo $_SESSION ["{$session_name}"];
//if ($_SERVER['APPLICATION_ENV'] == 'development') {
    error_reporting(E_ALL);
    ini_set("display_errors", 1);
	ini_set("display_startup_errors", 1);
//}
define('PATH_BASE_ROOT', dirname(__FILE__));
define('DS', DIRECTORY_SEPARATOR);
define('LANG_PATH', PATH_BASE_ROOT.'/../core/module/lang');
define('PATH_MODULE', PATH_BASE_ROOT.'/../core/module');
define('PRICE_UNIT', 'đ');
/**
 * This makes our life easier when dealing with paths. Everything is relative
 * to the application root now.
 */
chdir(dirname(__FILE__).'/../core/');
// Decline static file requests back to the PHP built-in webserver
if (php_sapi_name() === 'cli-server' && is_file(__DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH))) {
    return false;
}
// Setup autoloading
require 'init_autoloader.php';
include 'config/constants.php';
// Run the application!
Zend\Mvc\Application::init(require 'config/application.config.php')->run();