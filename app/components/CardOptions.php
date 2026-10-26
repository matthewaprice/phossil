<?php

namespace App\Components;

/**
 * Configuration for the Card component.
 *
 * @author Matthew A Price
 */
class CardOptions extends ComponentOptions
{
    /**
     * @param string                $title   Title text; trusted HTML, so callers escape it.
     * @param string                $content Main content; trusted HTML, so callers escape it.
     * @param string                $footer  Footer content; trusted HTML, so callers escape it.
     * @param array<string, string> $data    Attribute names without the `data-` prefix, mapped to values.
     * @param mixed                 ...$args Forwarded to ComponentOptions.
     */
    public function __construct(
        public string $title = '',
        public string $content = '',
        public string $footer = '',
        public array $data = [],
        ...$args
    ) {
        parent::__construct(...$args);
    }
}
