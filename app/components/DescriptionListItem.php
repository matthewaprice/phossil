<?php

namespace App\Components;

/**
 * A single term and description pair in a DescriptionList.
 *
 * @author Matthew A Price
 */
class DescriptionListItem
{
    /**
     * @param string $dt Term text; trusted HTML, so callers escape it.
     * @param string $dd Description text; trusted HTML, so callers escape it.
     */
    public function __construct(
        public string $dt = '',
        public string $dd = '',
        public ?string $id = ''
    ) {
    }
}
