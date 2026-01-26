<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/bootstrap.php';

use App\Core\Router;
use App\Core\Response;
use App\Core\Request;
use App\Core\Installer;

$installer = new Installer();
if (!$installer->isInstalled() && !str_starts_with(Request::path(), '/install')) {
    Response::redirect('/install');
}

$router = Router::getInstance();
$router->dispatch(Request::method(), Request::path());
