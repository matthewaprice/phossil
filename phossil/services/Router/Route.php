<?php

namespace Phossil\Services\Router;

class Route
{
    public static function create(RouteType ...$route_types): void
    {
        global $routes;

        foreach ($route_types as $route_type) {
            $routes[] = $route_type;
        }
    }
}
