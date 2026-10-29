<?php

namespace Phossil\Tests\Services\Router;

use Phossil\Services\Router\{
    Route,
    RouteType
};
use Phossil\Tests\Services\Router\Fixtures\{
    AboutStub,
    HomeStub
};
use PHPUnit\Framework\Attributes\{
    CoversClass,
    Test
};
use PHPUnit\Framework\TestCase;

#[CoversClass(Route::class)]
#[CoversClass(RouteType::class)]
class RouteTest extends TestCase
{
    /** The routes routes/web.php registered, restored after each test. */
    private array $site_routes;

    protected function setUp(): void
    {
        global $routes;

        $this->site_routes = $routes;
        $routes = [];
    }

    protected function tearDown(): void
    {
        global $routes;

        $routes = $this->site_routes;
    }

    #[Test]
    public function it_registers_each_route_in_order(): void
    {
        global $routes;

        Route::create(
            new RouteType(path: '', view: HomeStub::class),
            new RouteType(path: 'about', view: AboutStub::class, skip_build: true),
        );

        $this->assertSame(['', 'about'], array_column($routes, 'path'));
        $this->assertSame([HomeStub::class, AboutStub::class], array_column($routes, 'view'));
        $this->assertSame([false, true], array_column($routes, 'skip_build'));
    }

    #[Test]
    public function it_adds_to_the_routes_already_registered(): void
    {
        global $routes;

        Route::create(new RouteType(path: '', view: HomeStub::class));
        Route::create(new RouteType(path: 'about', view: AboutStub::class));

        $this->assertSame(['', 'about'], array_column($routes, 'path'));
    }

    #[Test]
    public function a_route_type_defaults_to_the_home_page_and_the_build(): void
    {
        $route_type = new RouteType();

        $this->assertNull($route_type->controller);
        $this->assertSame('', $route_type->path);
        $this->assertSame('', $route_type->view);
        $this->assertFalse($route_type->skip_build);
    }
}
