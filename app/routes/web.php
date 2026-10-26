<?php

use App\Services\Router\Route;
use App\Services\Router\RouteType;
use App\Views\About\About;
use App\Views\Home\Home;

Route::create(
    new RouteType(
        path: '',
        view: Home::class
    ),
    new RouteType(
        path: 'about',
        view: About::class
    )
);
