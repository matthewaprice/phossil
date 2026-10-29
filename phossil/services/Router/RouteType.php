<?php

namespace Phossil\Services\Router;

class RouteType
{
    /**
     * @param RouteController|null $controller Optional controller and method that build the logic for the view.
     * @param string               $path URL path without outer slashes, e.g. 'about'; '' is the home page.
     * @param string               $view Class name of the View that renders it.
     * @param bool                 $skip_build Serve it in development but leave it out of the static build, for a page still in progress.
     */
    public function __construct(
        public ?RouteController $controller = null,
        public string $path = '',
        public string $view = '',
        public bool $skip_build = false
    ) {
    }
}
