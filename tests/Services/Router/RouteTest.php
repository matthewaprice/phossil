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
    public function it_compiles_each_path_into_the_pattern_the_router_matches(): void
    {
        global $routes;

        Route::create(new RouteType(path: 'articles/{slug?}', where: ['slug' => '[a-z0-9-]+']));

        $this->assertSame('#^articles(?:/(?P<slug>[a-z0-9-]+))?$#', $routes[0]->pattern);
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

        $this->assertSame('', $route_type->path);
        $this->assertSame('', $route_type->view);
        $this->assertFalse($route_type->skip_build);
    }

    #[Test]
    public function a_placeholder_matches_one_whole_segment_unless_where_narrows_it(): void
    {
        $pattern = Route::compilePath('articles/{slug}');
        $narrowed = Route::compilePath('articles/{id}', ['id' => '\\d+']);

        $this->assertMatchesRegularExpression($pattern, 'articles/article-1');
        $this->assertDoesNotMatchRegularExpression($pattern, 'articles');
        $this->assertDoesNotMatchRegularExpression($pattern, 'articles/article-1/extra');
        $this->assertMatchesRegularExpression($narrowed, 'articles/12');
        $this->assertDoesNotMatchRegularExpression($narrowed, 'articles/article-1');
    }

    #[Test]
    public function an_optional_placeholder_can_be_left_off(): void
    {
        $pattern = Route::compilePath('articles/{slug?}');

        $this->assertMatchesRegularExpression($pattern, 'articles');
        $this->assertMatchesRegularExpression($pattern, 'articles/article-1');
        $this->assertDoesNotMatchRegularExpression($pattern, 'articlesx');
    }

    #[Test]
    public function the_home_page_matches_only_the_empty_path(): void
    {
        $pattern = Route::compilePath('');

        $this->assertMatchesRegularExpression($pattern, '');
        $this->assertDoesNotMatchRegularExpression($pattern, 'about');
    }

    #[Test]
    public function a_route_url_drops_its_placeholders(): void
    {
        $this->assertSame('articles', new RouteType(path: 'articles/{slug?}')->url());
        $this->assertSame('about', new RouteType(path: 'about')->url());
        $this->assertSame('', new RouteType()->url());
    }

    #[Test]
    public function a_segment_the_url_left_out_reads_as_the_default(): void
    {
        $route_type = new RouteType(path: 'articles/{slug?}');
        $route_type->segments = ['slug' => 'article-1'];

        $this->assertSame('article-1', $route_type->segment('slug'));
        $this->assertSame('none', $route_type->segment('id', 'none'));
    }
}
