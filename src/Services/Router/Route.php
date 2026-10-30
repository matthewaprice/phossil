<?php

namespace Phossil\Services\Router;

class Route
{
    /**
     * Every route registered so far, in order. The app hands them to the Router,
     * and the build renders each one.
     *
     * @var list<RouteType>
     */
    public static array $routes = [];

    public static function create(RouteType ...$route_types): void
    {
        foreach ($route_types as $route_type) {
            self::$routes[] = $route_type;
        }
    }
}
