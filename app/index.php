<?php

use App\Views\NotFound\NotFound;
use Phossil\Services\Router\{
    Route,
    Router
};

require __DIR__ . '/includes/bootstrap.php';

$router = new Router(Route::$routes, not_found: NotFound::class);
$router->process();
