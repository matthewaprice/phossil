<?php

namespace Phossil\Tests\Services\Router;

use Phossil\Services\Router\{
    Route,
    RouteController,
    Router,
    RouteType
};
use Phossil\Tests\Services\Router\Fixtures\{
    AboutStub,
    HomeStub,
    NotFoundStub,
    PathStubController
};
use PHPUnit\Framework\Attributes\{
    CoversClass,
    RunInSeparateProcess,
    Test
};
use PHPUnit\Framework\TestCase;

#[CoversClass(Router::class)]
class RouterTest extends TestCase
{
    protected function setUp(): void
    {
        unset($_SERVER['REQUEST_URI']);
    }

    protected function tearDown(): void
    {
        unset($_SERVER['REQUEST_URI']);
    }

    #[Test]
    public function it_echoes_the_view_whose_path_matches_the_request(): void
    {
        $this->assertSame('<p>about</p>', $this->process('/about/'));
    }

    #[Test]
    public function it_matches_the_home_page(): void
    {
        $this->assertSame('<p>home</p>', $this->process('/'));
    }

    #[Test]
    public function it_ignores_the_query_string_and_repeated_slashes(): void
    {
        $this->assertSame('<p>about</p>', $this->process('//about//?ref=nav'));
    }

    #[Test]
    public function it_serves_a_route_left_out_of_the_build(): void
    {
        $routes = $this->register(new RouteType(path: 'draft', view: AboutStub::class, skip_build: true));

        $this->assertSame('<p>about</p>', $this->process('/draft/', $routes));
    }

    #[Test]
    public function it_hands_the_controller_the_route_it_matched(): void
    {
        $this->assertSame('<p>articles/article-1</p>', $this->process('/articles/article-1/', $this->articleRoutes()));
    }

    #[Test]
    public function it_keeps_the_list_route_apart_from_the_item_routes(): void
    {
        $this->assertSame('<p>articles</p>', $this->process('/articles/', $this->articleRoutes()));
        $this->assertSame('<p>articles/article-2</p>', $this->process('/articles/article-2/', $this->articleRoutes()));
    }

    // http_response_code() can't set a code once PHPUnit has printed anything,
    // so the 404 tests run in a fresh process.

    #[Test]
    #[RunInSeparateProcess]
    public function it_renders_the_not_found_page_when_no_route_matches(): void
    {
        $html = $this->process('/missing/');

        $this->assertSame(404, http_response_code());
        $this->assertSame('<p>not found</p>', $html);
    }

    #[Test]
    #[RunInSeparateProcess]
    public function it_renders_the_not_found_page_when_the_view_class_does_not_exist(): void
    {
        $routes = $this->register(new RouteType(path: 'broken', view: 'Phossil\Tests\Missing'));

        $html = $this->process('/broken/', $routes);

        $this->assertSame(404, http_response_code());
        $this->assertSame('<p>not found</p>', $html);
    }

    #[Test]
    #[RunInSeparateProcess]
    public function it_renders_the_not_found_page_for_an_item_no_route_was_registered_for(): void
    {
        $html = $this->process('/articles/article-3/', $this->articleRoutes());

        $this->assertSame(404, http_response_code());
        $this->assertSame('<p>not found</p>', $html);
    }

    #[Test]
    #[RunInSeparateProcess]
    public function it_renders_the_not_found_page_when_the_controller_returns_null(): void
    {
        $html = $this->process('/articles/missing/', $this->articleRoutes());

        $this->assertSame(404, http_response_code());
        $this->assertSame('<p>not found</p>', $html);
    }

    #[Test]
    #[RunInSeparateProcess]
    public function it_answers_a_plain_not_found_without_a_not_found_view(): void
    {
        $_SERVER['REQUEST_URI'] = '/missing/';

        ob_start();
        new Router([])->process();
        $html = (string) ob_get_clean();

        $this->assertSame(404, http_response_code());
        $this->assertSame('Not Found', $html);
    }

    /**
     * Runs the router for a request and returns what it echoed.
     *
     * @param list<RouteType>|null $routes Defaults to a home and an about route.
     */
    private function process(string $uri, ?array $routes = null): string
    {
        $_SERVER['REQUEST_URI'] = $uri;

        $routes ??= $this->register(
            new RouteType(path: '', view: HomeStub::class),
            new RouteType(path: 'about', view: AboutStub::class),
        );

        ob_start();
        new Router($routes, not_found: NotFoundStub::class)->process();
        return (string) ob_get_clean();
    }

    /**
     * An articles list route plus one route per article, registered the way
     * routes/web.php loops over the known articles, all served by the stub controller.
     *
     * @return list<RouteType>
     */
    private function articleRoutes(): array
    {
        $controller = new RouteController(controller: PathStubController::class, method: 'view');

        return $this->register(
            new RouteType(path: 'articles', controller: $controller),
            ...array_map(
                fn (string $slug): RouteType => new RouteType(path: 'articles/' . $slug, controller: $controller),
                ['article-1', 'article-2', 'missing']
            )
        );
    }

    /**
     * Registers routes through Route::create(), the way routes/web.php does,
     * without touching the routes routes/web.php registered.
     *
     * @return list<RouteType> The routes as Route::create() registered them.
     */
    private function register(RouteType ...$route_types): array
    {
        $site_routes = Route::$routes;
        Route::$routes = [];

        Route::create(...$route_types);

        [$registered, Route::$routes] = [Route::$routes, $site_routes];

        return $registered;
    }
}
