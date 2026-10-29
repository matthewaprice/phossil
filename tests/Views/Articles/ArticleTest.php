<?php

namespace Phossil\Tests\Views\Articles;

use Phossil\Tests\Views\ViewTestCase;
use Phossil\Views\Articles\Article;
use PHPUnit\Framework\Attributes\{
    CoversClass,
    Test
};

/**
 * Article page test
 *
 * Ensures that the view renders content as expected.
 *
 * @author Matthew A Price
 */
#[CoversClass(Article::class)]
class ArticleTest extends ViewTestCase
{
    /** One article, in the shape ArticleController hands the view. */
    private const ARTICLE = ['title' => 'First article', 'slug' => 'first-article', 'content' => 'What the first article says.'];

    #[Test]
    public function it_names_the_page_after_the_article(): void
    {
        $article = new Article(article: self::ARTICLE);

        $this->assertStringContainsString('<title>First article · phossil</title>', $article);
    }

    #[Test]
    public function it_renders_the_article_content(): void
    {
        $article = new Article(article: self::ARTICLE);

        $this->assertStringContainsString('What the first article says.', $article);
    }
}
