<?php

declare(strict_types=1);

use App\Core\App;
use App\Core\Config;

define('BASE_PATH', dirname(__DIR__));

require BASE_PATH . '/app/Core/helpers.php';

spl_autoload_register(static function (string $class): void {
    $prefix = 'App\\';
    if (strncmp($class, $prefix, strlen($prefix)) !== 0) {
        return;
    }
    $relative = substr($class, strlen($prefix));
    $path = BASE_PATH . '/app/' . str_replace('\\', '/', $relative) . '.php';
    if (is_file($path)) {
        require $path;
    }
});

Config::load(BASE_PATH . '/.env', BASE_PATH . '/.env.example');

date_default_timezone_set(Config::get('APP_TIMEZONE', 'UTC'));

$debug = (bool) Config::get('APP_DEBUG', false);
ini_set('display_errors', $debug ? '1' : '0');
error_reporting($debug ? E_ALL : (E_ALL & ~E_NOTICE & ~E_DEPRECATED));

App::boot();

$app = new App();
$app->run();
