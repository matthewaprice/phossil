<?php

namespace App\Components;

/**
 * Base configuration for every component.
 *
 * Holds the options shared by all UI components.
 *
 * @author Matthew A Price
 */
class ComponentOptions
{
    /**
     * @param string $id    HTML id attribute; auto-generated when empty.
     * @param bool   $ob    Buffer the markup and return it instead of echoing it.
     */
    public function __construct(
        public string $id = '',
        public string $class = '',
        public bool $ob = false
    ) {
    }
}
