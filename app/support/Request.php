<?php

namespace App\Support;

/**
 * The current HTTP request's URL path, read from REQUEST_URI.
 *
 * The one place the path is normalised, so the router and the components that
 * ask "which page is this?" always agree.
 *
 * @author Matthew A Price
 */
class Request
{
    /**
     * The request path without its query string, repeated slashes or outer slashes.
     *
     * '/about//?x=1' gives 'about'; the home page gives ''.
     *
     * @return string Path with no leading or trailing slash.
     */
    public static function path(): string
    {
        $uri = preg_replace('/\/+/', '/', $_SERVER['REQUEST_URI'] ?? '');

        return trim(explode('?', $uri)[0], '/');
    }

    /**
     * The request path split into its segments.
     *
     * @return list<string> Segments in order; empty for the home page.
     */
    public static function segments(): array
    {
        $path = self::path();

        return $path === '' ? [] : explode('/', $path);
    }
}
