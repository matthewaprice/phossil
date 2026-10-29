<?php

namespace Phossil\Services\Router;

class Route
{
    public static function create(RouteType ...$route_types): void
    {
        global $routes;

        foreach ($route_types as $route_type) {
            $route_type->pattern = self::compilePath($route_type->path, $route_type->where);
            $routes[] = $route_type;
        }
    }

    /**
     * Compiles a placeholder path into the anchored regex the router matches on.
     *
     * '{name}' becomes a named capture, and '{name?}' takes its leading slash into
     * the optional group with it, so 'articles/{slug?}' answers to 'articles' and
     * 'articles/article-1' alike. Literal segments are quoted and the pattern is
     * anchored, so a route matches its own paths and nothing that merely starts the same way.
     *
     * @param string $path The route path
     * @param array<string, string> $where Regex each placeholder is narrowed to
     * @return string The compiled pattern, delimiters included
     */
    public static function compilePath(string $path = '', array $where = []): string
    {
        $pattern = '';

        foreach (explode('/', trim($path, '/')) as $index => $segment) {
            $optional = false;

            if (preg_match('/^\{([a-z_][a-z0-9_]*)(\?)?\}$/i', $segment, $placeholder)) {
                $name = $placeholder[1];
                $optional = !empty($placeholder[2]);
                $piece = '(?P<' . $name . '>' . ($where[$name] ?? '[^/]+') . ')';
            } else {
                $piece = preg_quote($segment, '#');
            }

            $slash = $index === 0 ? '' : '/';

            $pattern .= $optional ? '(?:' . $slash . $piece . ')?' : $slash . $piece;
        }

        return '#^' . $pattern . '$#';
    }
}
