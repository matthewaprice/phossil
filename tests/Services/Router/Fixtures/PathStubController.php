<?php

namespace Phossil\Tests\Services\Router\Fixtures;

use Phossil\Services\Controller;

/**
 * A stand-in controller for routing tests, echoing the path of the route the
 * router handed it so the tests can see which route reached the controller.
 *
 * @author Matthew A Price
 */
class PathStubController extends Controller
{
    /**
     * @return string|null Null for a path ending in 'missing', so the router serves the 404.
     */
    public function view(): ?string
    {
        return !str_ends_with($this->route->path, 'missing') ? '<p>' . $this->route->path . '</p>' : null;
    }
}
