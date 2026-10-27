<?php

use Phossil\Services\Router\Router;

global $routes;

require __DIR__ . '/includes/bootstrap.php';

$router = new Router($routes);
$router->process();
