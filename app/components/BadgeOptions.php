<?php

namespace App\Components;

/**
 * Configuration for the Badge component.
 *
 * @author Matthew A Price
 */
class BadgeOptions extends ComponentOptions
{
    /**
     * @param string $text      Label text; trusted HTML, so callers escape it.
     * @param string $color     Color scheme: `primary`, `blue`, `green`, `red` or `gray`; anything else renders gray.
     * @param string $font_size Tailwind text size without its `text-` prefix, as in `xs`.
     * @param mixed  ...$args   Forwarded to ComponentOptions.
     */
    public function __construct(
        public string $text = '',
        public string $color = 'blue',
        public string $font_size = 'xs',
        public bool $condensed = false,
        ...$args
    ) {
        parent::__construct(...$args);
    }
}
