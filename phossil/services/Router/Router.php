<?php

namespace Phossil\Services\Router;

use Phossil\Support\Request;
use Phossil\Views\NotFound\NotFound;

class Router
{
    /**
     * @param list<RouteType> $routes The routes Route::create() registered.
     */
    public function __construct(private readonly array $routes)
    {
    }

    /**
     * Echoes the page for the route whose path matches the request, or the 404 page
     * when none does.
     */
    public function process(): void
    {
        $path = Request::path();

        foreach ($this->routes as $route) {
            if ($route->path !== $path) {
                continue;
            }

            $html = $this->render($route);
            if ($html !== null) {
                echo $html;
                return;
            }
        }

        http_response_code(404);
        echo new NotFound();
    }

    /**
     * Renders a matched route through its controller, or straight from its view.
     *
     * A controller that returns null is saying there is no such page, and the
     * request falls through to the 404.
     *
     * @return string|null The page, or null when the route has nothing to render.
     */
    private function render(RouteType $route): ?string
    {
        $controller_name = $route->controller->controller ?? '';
        $controller_method = $route->controller->method ?? '';

        if (class_exists($controller_name) && method_exists($controller_name, $controller_method)) {
            $controller = new $controller_name($route);
            $response = $controller->{$controller_method}();

            return $response !== null ? (string) $response : null;
        }

        if (class_exists($route->view)) {
            return (string) new $route->view();
        }

        return null;
    }
}
