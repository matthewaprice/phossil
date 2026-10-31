<?php

namespace Phossil\Tests\Services\Router\Fixtures;

use Phossil\Components\View;

/**
 * A stand-in 404 view for routing tests, so they check that the router falls
 * back to the not-found view without rendering a whole page.
 *
 * @author Matthew A Price
 */
readonly class NotFoundStub extends View
{
    public function render(): string
    {
        return '<p>not found</p>';
    }
}
