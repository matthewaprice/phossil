<?php

namespace App\Services\Router;

use App\Support\Request;
use App\Views\NotFound\NotFound;

class Router
{
    public function __construct(private readonly array $routes)
    {
    }

    /**
     * Echoes the view whose path matches the request, or the 404 page when none does.
     */
    public function process(): void
    {
        $path = Request::path();

        foreach ($this->routes as $route) {
            if ($route['path'] === $path && class_exists($route['view'])) {
                echo new $route['view']();
                return;
            }
        }

        http_response_code(404);
        echo new NotFound();
    }
}
