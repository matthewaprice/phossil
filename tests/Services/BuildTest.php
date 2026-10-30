<?php

namespace Phossil\Tests\Services;

use Phossil\Services\Build;
use Phossil\Services\Router\{
    Route,
    RouteType
};
use PHPUnit\Framework\Attributes\{
    CoversClass,
    Test
};
use PHPUnit\Framework\TestCase;

#[CoversClass(Build::class)]
class BuildTest extends TestCase
{
    /** The routes routes/web.php registered, restored after each test. */
    private array $site_routes;

    protected function setUp(): void
    {
        $this->site_routes = Route::$routes;
        Route::$routes = [];
    }

    protected function tearDown(): void
    {
        Route::$routes = $this->site_routes;
    }

    #[Test]
    public function it_builds_a_page_for_each_route_registered_from_content(): void
    {
        // Registered the way routes/web.php loops over its known content, one route per item.
        $posts = [['slug' => 'first-post'], ['slug' => 'second-post']];

        Route::create(
            new RouteType(path: 'posts'),
            ...array_map(fn (array $post): RouteType => new RouteType(path: 'posts/' . $post['slug']), $posts)
        );

        $this->assertSame(['/posts', '/posts/first-post', '/posts/second-post'], new Build()->urls());
    }

    #[Test]
    public function it_leaves_out_the_routes_marked_skip_build(): void
    {
        Route::create(
            new RouteType(path: ''),
            new RouteType(path: 'about'),
            new RouteType(path: 'draft', skip_build: true),
        );

        $this->assertSame(['/', '/about'], new Build()->urls());
    }
}
