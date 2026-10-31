<?php

namespace App\Tests\Controllers\Articles;

use App\Controllers\Articles\ArticleController;
use App\Data\ArticlesData;
use Phossil\Services\Router\RouteType;
use App\Views\Articles\Article;
use App\Views\NotFound\NotFound;
use PHPUnit\Framework\Attributes\{
    CoversClass,
    DataProvider,
    Test
};
use PHPUnit\Framework\TestCase;

/**
 * Article controller test
 *
 * Ensures each article route finds its article by the slug in its path.
 *
 * @author Matthew A Price
 */
#[CoversClass(ArticleController::class)]
class ArticleControllerTest extends TestCase
{
    /**
     * Every known article, keyed by slug, as routes/web.php registers a route for each.
     *
     * @return array<string, array{array<string, string>}>
     */
    public static function articles(): array
    {
        $cases = [];
        foreach (ArticlesData::getAll() as $article) {
            $cases[$article['slug']] = [$article];
        }

        return $cases;
    }

    #[Test]
    #[DataProvider('articles')]
    public function it_hands_the_view_the_article_its_route_names(array $article): void
    {
        $view = new ArticleController(new RouteType(path: 'articles/' . $article['slug']))->view();

        $this->assertInstanceOf(Article::class, $view);
        $this->assertSame($article, $view->article);
    }

    #[Test]
    public function it_returns_the_not_found_page_for_a_slug_no_article_has(): void
    {
        $view = new ArticleController(new RouteType(path: 'articles/no-such-article'))->view();

        $this->assertInstanceOf(NotFound::class, $view);
    }
}
