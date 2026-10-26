<?php

namespace App\Services\Router;

class Route
{
    public static function create(RouteType ...$route_types): void
    {
        global $routes;

        foreach ($route_types as $route_type) {
            $routes[] = [
                'path' => $route_type->path,
                'view' => $route_type->view,
                'skip_build' => $route_type->skip_build
            ];
        }
    }
}
