<?php

namespace Phossil\Tests\Services\Router;

use Phossil\Services\Router\Router;
use Phossil\Tests\Services\Router\Fixtures\{
    AboutStub,
    HomeStub
};
use Phossil\Tests\Support\FakesViteDevServer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(Router::class)]
class RouterTest extends TestCase
{
    use FakesViteDevServer;

    /** Routes in the shape Route::create() registers them. */
    private const ROUTES = [
        ['path' => '', 'view' => HomeStub::class, 'skip_build' => false],
        ['path' => 'about', 'view' => AboutStub::class, 'skip_build' => false],
    ];

    protected function setUp(): void
    {
        unset($_SERVER['REQUEST_URI']);

        // The 404 page renders through the Layout, which writes Vite tags.
        $this->startFakeViteDevServer();
    }

    protected function tearDown(): void
    {
        $this->stopFakeViteDevServer();

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
        $routes = [['path' => 'draft', 'view' => AboutStub::class, 'skip_build' => true]];

        $this->assertSame('<p>about</p>', $this->process('/draft/', $routes));
    }

    // http_response_code() can't set a code once PHPUnit has printed anything,
    // so the 404 tests run in a fresh process.

    #[Test]
    #[RunInSeparateProcess]
    public function it_renders_the_not_found_page_when_no_route_matches(): void
    {
        $html = $this->process('/missing/');

        $this->assertSame(404, http_response_code());
        $this->assertStringContainsString('Page not found - 404', $html);
    }

    #[Test]
    #[RunInSeparateProcess]
    public function it_renders_the_not_found_page_when_the_view_class_does_not_exist(): void
    {
        $routes = [['path' => 'broken', 'view' => 'Phossil\Views\Missing', 'skip_build' => false]];

        $html = $this->process('/broken/', $routes);

        $this->assertSame(404, http_response_code());
        $this->assertStringContainsString('Page not found - 404', $html);
    }

    /**
     * Runs the router for a request and returns what it echoed.
     *
     * @param array<int, array{path: string, view: string, skip_build: bool}> $routes
     */
    private function process(string $uri, array $routes = self::ROUTES): string
    {
        $_SERVER['REQUEST_URI'] = $uri;

        ob_start();
        new Router($routes)->process();
        return (string) ob_get_clean();
    }
}
