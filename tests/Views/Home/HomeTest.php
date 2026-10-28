<?php

namespace Phossil\Tests\Views\Home;

use Phossil\Tests\Views\ViewTestCase;
use Phossil\Views\Home\Home;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

/**
 * Homepage test
 *
 * Ensures that the view renders content as expected.
 *
 * @author Matthew A Price
 */
#[CoversClass(Home::class)]
class HomeTest extends ViewTestCase
{
    #[Test]
    public function it_renders_the_banner(): void
    {
        $home = new Home();

        $this->assertStringContainsString('Write it in PHP.', $home);
        $this->assertStringContainsString('Ship it as HTML.', $home);
        $this->assertStringContainsString('href="/about/"', $home);
    }

    #[Test]
    public function it_renders_a_card_for_each_feature(): void
    {
        $home = new Home();

        $this->assertStringContainsString('Routing', $home);
        $this->assertStringContainsString('Components', $home);
        $this->assertStringContainsString('Static build', $home);
    }

    #[Test]
    public function it_renders_inside_the_layout(): void
    {
        $home = new Home();

        $this->assertStringContainsString('<title>phossil</title>', $home);
        $this->assertStringContainsString('<meta name="description" content="A small PHP site that builds to static HTML.">', $home);
    }

    #[Test]
    public function it_emits_the_banner_dialog_at_the_end_of_the_page(): void
    {
        $home = new Home();

        $this->assertStringContainsString('data-dialog-target="#home-dialog"', $home);
        $this->assertStringContainsString('Hello from a dialog', $home);
        $this->assertGreaterThan(strpos($home, '</footer>'), strpos($home, 'id="home-dialog"'));
    }
}
