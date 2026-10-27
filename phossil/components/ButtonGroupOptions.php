<?php

namespace Phossil\Components;

/**
 * Configuration for the ButtonGroup component.
 *
 * @author Matthew A Price
 */
class ButtonGroupOptions extends ComponentOptions
{
    /**
     * @param list<ButtonGroupItem> $items              The group's links, in render order.
     * @param bool                  $stacked            Stack vertically on large screens instead of inline.
     * @param bool                  $compact            Use the smaller size variant.
     * @param string                $active_class       Extra CSS classes for the item matching the current page.
     * @param mixed                 ...$args            Forwarded to ComponentOptions.
     */
    public function __construct(
        public array $items = [],
        public bool $stacked = false,
        public bool $compact = false,
        public string $active_class = 'bg-primary-50 text-primary-800',
        ...$args
    ) {
        parent::__construct(...$args);
    }

    /**
     * Reports whether the group has anything left to render.
     *
     * A caller wrapping the group in its own markup asks this first, so a group
     * whose every item is skipped leaves no empty wrapper behind.
     *
     * @return bool True when at least one item survives its `skip` flag.
     */
    public function hasItems(): bool
    {
        return array_any($this->items, fn(ButtonGroupItem $item) => !$item->skip);
    }
}
