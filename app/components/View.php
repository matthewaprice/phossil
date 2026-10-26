<?php

namespace App\Components;

/**
 * Base class for typed page views.
 *
 * Subclasses declare their data as readonly constructor properties and build
 * markup in `render()`, usually through {@see self::capture()}. The router
 * echoes the view itself and lets `__toString()` render it.
 *
 * @author Matthew A Price
 */
readonly abstract class View
{
    /**
     * Renders the view, so it can be echoed directly.
     *
     * @return string The rendered HTML.
     */
    public function __toString(): string
    {
        return $this->render();
    }

    /**
     * Buffers an inline template block and returns its markup.
     *
     * @param \Closure $template Template body; bound to `$this`, so it reads the view's properties directly.
     * @return string The captured markup.
     */
    protected function capture(\Closure $template): string
    {
        ob_start();
        $template->call($this);
        return ob_get_clean();
    }

    /**
     * Builds the view's markup.
     *
     * @return string The rendered HTML.
     */
    abstract public function render(): string;
}
