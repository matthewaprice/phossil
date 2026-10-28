<?php

namespace Phossil\Tests\Services\Router\Fixtures;

use Phossil\Components\View;

/**
 * A stand-in view for routing tests, so they check which view the router
 * picks without rendering a whole page.
 *
 * @author Matthew A Price
 */
readonly class HomeStub extends View
{
    public function render(): string
    {
        return '<p>home</p>';
    }
}
