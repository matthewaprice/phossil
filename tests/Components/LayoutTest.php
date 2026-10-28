<?php

namespace Phossil\Tests\Components;

use Phossil\Components\{
    Dialog,
    DialogOptions,
    Layout,
    LayoutOptions
};
use Phossil\Tests\Support\FakesViteDevServer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(Layout::class)]
#[CoversClass(LayoutOptions::class)]
class LayoutTest extends ComponentTestCase
{
    use FakesViteDevServer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->startFakeViteDevServer();
    }

    protected function tearDown(): void
    {
        $this->stopFakeViteDevServer();
        parent::tearDown();
    }

    #[Test]
    public function it_renders_a_whole_page(): void
    {
        $html = $this->layout();

        $this->assertStringContainsString('<!DOCTYPE html>', $html);
        $this->assertStringContainsString('<html lang="en">', $html);
        $this->assertStringContainsString('</html>', $html);
    }

    #[Test]
    public function it_appends_the_site_name_to_the_title(): void
    {
        $this->assertStringContainsString('<title>About · phossil</title>', $this->layout(title: 'About'));
    }

    #[Test]
    public function it_uses_the_site_name_alone_without_a_title(): void
    {
        $this->assertStringContainsString('<title>phossil</title>', $this->layout());
    }

    #[Test]
    public function it_escapes_the_title_and_description_in_the_head(): void
    {
        $html = $this->layout(title: 'Tom & Jerry', description: 'Cats "and" mice');

        $this->assertStringContainsString('<title>Tom &amp; Jerry · phossil</title>', $html);
        $this->assertStringContainsString('<meta name="description" content="Cats &quot;and&quot; mice">', $html);
    }

    #[Test]
    public function it_renders_the_title_as_the_page_heading(): void
    {
        $this->assertMatchesRegularExpression('/<h1 class="[^"]*">About<\/h1>/', $this->layout(title: 'About'));
    }

    #[Test]
    public function it_escapes_the_title_in_the_page_heading(): void
    {
        $this->assertMatchesRegularExpression('/<h1 class="[^"]*">Tom &amp; &lt;Jerry&gt;<\/h1>/', $this->layout(title: 'Tom & <Jerry>'));
    }

    #[Test]
    public function it_omits_the_heading_without_a_title(): void
    {
        $this->assertStringNotContainsString('<h1', $this->layout());
    }

    #[Test]
    public function it_renders_the_content_in_main(): void
    {
        $html = $this->layout(content: '<section id="body">Hello</section>');

        $this->assertMatchesRegularExpression('/<main class="flex-1">\s*<section id="body">Hello<\/section>\s*<\/main>/', $html);
    }

    #[Test]
    public function it_loads_the_vite_entries(): void
    {
        $html = $this->layout();

        $this->assertStringContainsString('<script type="module" src="http://localhost:5173/@vite/client"></script>', $html);
        $this->assertStringContainsString('<link rel="stylesheet" href="http://localhost:5173/css/app.css">', $html);
        $this->assertStringContainsString('<script type="module" src="http://localhost:5173/js/app.js"></script>', $html);
    }

    #[Test]
    public function it_marks_the_current_page_in_the_nav(): void
    {
        $_SERVER['REQUEST_URI'] = '/about/';

        $html = $this->layout();

        $this->assertStringContainsString('href="/about/" target="_self" aria-current="page">', $html);
        $this->assertStringContainsString('href="/" target="_self">', $html);
    }

    #[Test]
    public function it_links_the_docs_and_github(): void
    {
        $html = $this->layout();

        $this->assertStringContainsString('href="https://phossil.dev" target="_blank" title="Documentation"', $html);
        $this->assertStringContainsString('href="https://github.com/matthewaprice/phossil" target="_blank" title="View on GitHub"', $html);
    }

    #[Test]
    public function it_emits_registered_dialogs_before_the_body_closes(): void
    {
        Dialog::create(new DialogOptions(id: 'help'));

        $html = $this->layout();
        $dialog = strpos($html, '<dialog');

        $this->assertNotFalse($dialog);
        $this->assertGreaterThan(strpos($html, '</footer>'), $dialog);
        $this->assertLessThan(strpos($html, '</body>'), $dialog);
    }

    #[Test]
    public function it_shows_the_current_year_in_the_footer(): void
    {
        $this->assertStringContainsString('&copy' . date('Y') . ' My Site', $this->layout());
    }

    /**
     * Echoes a layout and returns the page.
     */
    private function layout(mixed ...$options): string
    {
        return $this->echoed(fn () => Layout::create(new LayoutOptions(...$options)));
    }
}
