<?php

namespace Phossil\Components;

/**
 * Configuration for the Layout component.
 *
 * @author Matthew A Price
 */
class LayoutOptions extends ComponentOptions
{
    /**
     * @param string $content     Page body; trusted HTML, so callers escape it.
     * @param string $title       Page name for the <title> and the page's <h1>; plain text, escaped on output. The site name is appended to the <title>.
     * @param string $description Meta description.
     * @param mixed  ...$args     Forwarded to ComponentOptions.
     */
    public function __construct(
        public string $content = '',
        public string $title = '',
        public string $description = '',
        ...$args
    ) {
        parent::__construct(...$args);
    }
}
