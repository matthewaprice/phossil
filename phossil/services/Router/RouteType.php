<?php

namespace Phossil\Services\Router;

class RouteType
{
    /** The compiled regex the router matches request paths against, set by Route::create(). */
    public string $pattern = '';

    /**
     * The path's named placeholders, as the matched URL filled them in.
     *
     * Empty until the router matches a request against this route.
     *
     * @var array<string, string>
     */
    public array $segments = [];

    /**
     * @param RouteController|null  $controller Optional controller and method that build the logic for the view.
     * @param string                $path URL path without outer slashes, e.g. 'about'; '' is the home page.
     *                                    '{name}' is a placeholder for one segment and '{name?}' an optional one,
     *                                    so 'articles/{slug?}' answers to 'articles' and 'articles/article-1'.
     * @param string                $view Class name of the View that renders it.
     * @param bool                  $skip_build Serve it in development but leave it out of the static build, for a page still in progress.
     * @param array<string, string> $where Narrows a placeholder to a regex, e.g. ['id' => '\d+']. Anything unlisted matches a whole segment.
     */
    public function __construct(
        public ?RouteController $controller = null,
        public string $path = '',
        public string $view = '',
        public bool $skip_build = false,
        public array $where = []
    ) {
    }

    /**
     * A named placeholder as the URL filled it in.
     *
     * @param string $name The placeholder name
     * @param string $default Returned when the URL did not supply the segment
     * @return string The segment's value
     */
    public function segment(string $name = '', string $default = ''): string
    {

        return !empty($this->segments[$name]) ? $this->segments[$name] : $default;
    }

    /**
     * The path with its placeholders dropped, e.g. 'articles' for 'articles/{slug?}'.
     *
     * @return string Path with no leading or trailing slash.
     */
    public function url(): string
    {
        $literals = array_filter(
            explode('/', $this->path),
            fn (string $segment): bool => !str_starts_with($segment, '{')
        );

        return implode('/', $literals);
    }
}
