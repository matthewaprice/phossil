<?php

namespace Phossil\Tests\Views\NotFound;

use Phossil\Tests\Views\ViewTestCase;
use Phossil\Views\NotFound\NotFound;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

/**
 * Tests the page the router renders when no route matches, and that the build
 * writes to 404.html.
 *
 * @author Matthew A Price
 */
#[CoversClass(NotFound::class)]
class NotFoundTest extends ViewTestCase
{
    #[Test]
    public function it_names_the_page_in_the_title_and_heading(): void
    {
        $not_found = new NotFound();

        $this->assertStringContainsString('<title>Page not found - 404 · phossil</title>', $not_found);
        $this->assertMatchesRegularExpression('/<h1 class="[^"]*">Page not found - 404<\/h1>/', $not_found);
    }

    #[Test]
    public function it_links_back_to_the_home_page(): void
    {
        $not_found = new NotFound();

        $this->assertStringContainsString('class="link" href="/"', $not_found);
        $this->assertStringContainsString('Back to the home page', $not_found);
    }
}
