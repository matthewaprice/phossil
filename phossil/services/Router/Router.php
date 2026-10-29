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
     * Echoes the page for the first route whose pattern matches the request, or the
     * 404 page when none does.
     */
    public function process(): void
    {
        $path = Request::path();

        foreach ($this->routes as $registered) {
            if (!preg_match($registered->pattern, $path, $captures)) {
                continue;
            }

            // The registered route stays as declared; the one carrying this URL's segments is a copy.
            $route = clone $registered;
            $route->segments = self::segments($captures);

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
     * A controller that returns null is saying there is no such page, e.g. an
     * article slug that does not exist, and the request falls through to the 404.
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

    /**
     * Reduces what preg_match reported to the path's named placeholders.
     *
     * preg_match reports every group by offset as well as by name, and an optional
     * placeholder the URL skipped comes back empty; neither is a segment.
     *
     * @param array<int|string, string> $captures The matches preg_match reported
     * @return array<string, string> The segments the URL filled in
     */
    private static function segments(array $captures): array
    {
        return array_filter(
            $captures,
            fn (string $value, int|string $name): bool => is_string($name) && $value !== '',
            ARRAY_FILTER_USE_BOTH
        );
    }
}
