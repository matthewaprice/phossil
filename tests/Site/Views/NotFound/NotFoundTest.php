<?php

namespace App\Tests\Views\NotFound;

use App\Tests\Views\ViewTestCase;
use App\Views\NotFound\NotFound;
use PHPUnit\Framework\Attributes\{
    CoversClass,
    Test
};

/**
 * 404 test
 *
 * Ensures that the view renders content as expected.
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
