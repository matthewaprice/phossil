<?php

namespace Phossil\Services\Router;

class RouteController
{
    /**
     *
     * @param string $controller
     * @param string $method
     * @return void
     */
    public function __construct(
        public string $controller = '',
        public string $method = ''
    ) {}
}
