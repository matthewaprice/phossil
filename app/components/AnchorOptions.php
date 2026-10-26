<?php

namespace App\Components;

/**
 * Configuration for the Anchor component.
 *
 * @author Matthew A Price
 */
class AnchorOptions extends ComponentOptions
{
    /**
     * @param string                $text    Visible content; trusted HTML, so callers escape it.
     * @param string                $target  Target attribute, such as `_self` or `_blank`.
     * @param array<string, string> $data    Attribute names without the `data-` prefix, mapped to values.
     * @param string                $title   Title attribute used as the hover tooltip.
     * @param bool                  $current Marks the link as the current page with `aria-current`.
     * @param mixed                 ...$args Forwarded to ComponentOptions.
     */
    public function __construct(
        public string $href = '',
        public string $text = '',
        public string $target = '_self',
        public array $data = [],
        public string $title = '',
        public bool $current = false,
        ...$args
    ) {
        parent::__construct(...$args);
    }
}
