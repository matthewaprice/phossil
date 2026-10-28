<?php

use Phossil\Services\Router\{
    Route,
    RouteType
};
use Phossil\Views\About\About;
use Phossil\Views\Home\Home;

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
