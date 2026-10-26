<?php

namespace App\Components;

/**
 * Configuration for the DescriptionList component.
 *
 * @author Matthew A Price
 */
class DescriptionListOptions extends ComponentOptions
{
    /**
     * @param array<int, DescriptionListItem|null> $items The term/description pairs to render; null entries are skipped
     * @param mixed                 ...$args Forwarded to ComponentOptions.
     */
    public function __construct(
        public array $items = [],
        ...$args
    ) {
        parent::__construct(...$args);
    }
}
