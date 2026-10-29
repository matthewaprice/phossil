<?php

namespace Phossil\Tests\Views\Articles;

use Phossil\Tests\Views\ViewTestCase;
use Phossil\Views\Articles\Articles;
use PHPUnit\Framework\Attributes\{
    CoversClass,
    Test
};

/**
 * Articles list test
 *
 * Ensures that the view renders content as expected.
 *
 * @author Matthew A Price
 */
#[CoversClass(Articles::class)]
class ArticlesTest extends ViewTestCase
{
    /** Articles in the shape ArticlesData lists them. */
    private const ARTICLES = [
        ['title' => 'First article', 'slug' => 'first-article', 'content' => 'What the first article says.'],
        ['title' => 'Second article', 'slug' => 'second-article', 'content' => 'What the second article says.'],
    ];

    #[Test]
    public function it_renders_a_card_for_each_article(): void
    {
        $articles = new Articles(articles: self::ARTICLES);

        $this->assertStringContainsString('First article', $articles);
        $this->assertStringContainsString('What the first article says.', $articles);
        $this->assertStringContainsString('Second article', $articles);
        $this->assertStringContainsString('What the second article says.', $articles);
    }

    #[Test]
    public function it_links_each_card_to_its_article_page(): void
    {
        $articles = new Articles(articles: self::ARTICLES);

        $this->assertStringContainsString('href="/articles/first-article/"', $articles);
        $this->assertStringContainsString('href="/articles/second-article/"', $articles);
    }

    #[Test]
    public function it_renders_inside_the_layout(): void
    {
        $articles = new Articles(articles: self::ARTICLES);

        $this->assertStringContainsString('<title>Articles · phossil</title>', $articles);
    }

    #[Test]
    public function it_renders_no_cards_when_there_are_no_articles(): void
    {
        $articles = new Articles(articles: []);

        $this->assertStringNotContainsString('Read More', $articles);
    }
}
