<?php

namespace App\Components;

use App\Enums\Icon;

/**
 * A single link within a ButtonGroup.
 *
 * A caller gates an item with `skip` rather than by withholding it, so the
 * group's full shape stays readable at the call site whatever a permission
 * decides.
 *
 * @author Matthew A Price
 */
class ButtonGroupItem
{
    /**
     * @param ?string               $text   Visible label; trusted HTML, so callers escape it.
     * @param ?Icon                 $icon   Leading icon; with text alongside it the label hides below the `lg` breakpoint.
     * @param string                $class  Extra CSS classes, kept ahead of the ones the group compiles.
     * @param string                $title  Title attribute used as the hover tooltip.
     * @param string                $target Target attribute, such as `_self` or `_blank`.
     * @param array<string, string> $data   Attribute names without the `data-` prefix, mapped to values.
     * @param list<string>          $active URI segments that mark the item as the current page, for a path its href does not match.
     * @param bool                  $skip   Drop the item from the group rather than rendering it.
     */
    public function __construct(
        public string $href,
        public ?string $text = null,
        public ?Icon $icon = null,
        public string $id = '',
        public string $class = '',
        public string $title = '',
        public string $target = '_self',
        public array $data = [],
        public array $active = [],
        public bool $skip = false
    ) {
    }
}
