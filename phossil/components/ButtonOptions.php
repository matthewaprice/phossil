<?php

namespace Phossil\Components;

use Phossil\Enums\Buttons;

/**
 * Configuration for the Button component.
 *
 * @author Matthew A Price
 */
class ButtonOptions extends ComponentOptions
{
    /**
     * @param string        $text    Visible content; trusted HTML, so callers escape it.
     * @param list<Buttons> $styles  Cases passed to {@see Buttons::make()}, such as a size and Bordered.
     * @param string        $cursor  Tailwind cursor suffix, appended to `cursor-`.
     * @param AnchorOptions $link    When its text is set, renders a styled anchor instead of a button.
     * @param array<string, string|int> $data Attribute names without the `data-` prefix, mapped to values.
     * @param mixed         ...$args Forwarded to ComponentOptions.
     */
    public function __construct(
        public string $text = '',
        public array $styles = [Buttons::Medium],
        public string $cursor = 'pointer',
        public bool $disabled = false,
        public AnchorOptions $link = new AnchorOptions(),
        public array $data = [],
        ...$args
    ) {
        parent::__construct(...$args);
    }
}
