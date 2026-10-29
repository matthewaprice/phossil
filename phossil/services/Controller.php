<?php

namespace Phossil\Services;

use Phossil\Services\Router\RouteType;

class Controller
{
    public function __construct(public readonly RouteType $route = new RouteType())
    {
    }
}
