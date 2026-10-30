<?php

use App\Controllers\Articles\ArticleController;
use App\Controllers\Articles\ArticlesController;
use App\Controllers\Home\HomeController;
use App\Data\ArticlesData;
use Phossil\Services\Router\{
    Route,
    RouteController,
    RouteType
};
use App\Views\About\About;

Route::create(
    ...array_map(
        fn(array $article) => new RouteType(
            path: 'articles/' . $article['slug'],
            controller: new RouteController(controller: ArticleController::class, method: 'view')
        ),
        ArticlesData::getAll()
    )
);

Route::create(
    new RouteType(
        path: '',
        controller: new RouteController(controller: HomeController::class, method: 'view')
    ),
    new RouteType(
        path: 'about',
        view: About::class
    ),
    new RouteType(
        path: 'articles',
        controller: new RouteController(controller: ArticlesController::class, method: 'view')
    )
);
