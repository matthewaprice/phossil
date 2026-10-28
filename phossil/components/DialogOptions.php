<?php

namespace Phossil\Components;

/**
 * Configuration for the Dialog component.
 *
 * @author Matthew A Price
 */
class DialogOptions extends ComponentOptions
{
    /**
     * @param string               $title    Title text; trusted HTML, so callers escape it.
     * @param string               $content  Main content; trusted HTML, so callers escape it.
     * @param string               $footer   Footer content, shown below the content; trusted HTML, so callers escape it.
     * @param array{field?: string} $focus   Id of the field to focus when the dialog opens.
     * @param string               $size     Size variant: `sm`, `md` or `lg`.
     * @param bool                 $open     Open automatically on page load.
     * @param string               $position Position variant: `center`, `right`, `left`, `top` or `bottom`.
     * @param mixed                ...$args  Forwarded to ComponentOptions.
     */
    public function __construct(
        public string $title = '',
        public string $content = '',
        public string $footer = '',
        public array $focus = [],
        public string $size = 'md',
        public bool $open = false,
        public string $position = 'center',
        ...$args
    ) {
        parent::__construct(...$args);
    }
}
