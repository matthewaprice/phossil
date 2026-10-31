<?php

namespace App\Tests\Controllers\Articles;

use App\Controllers\Articles\ArticlesController;
use App\Data\ArticlesData;
use Phossil\Services\Router\RouteType;
use App\Views\Articles\Articles;
use PHPUnit\Framework\Attributes\{
    CoversClass,
    Test
};
use PHPUnit\Framework\TestCase;

/**
 * Articles controller test
 *
 * Ensures the list page gets every known article.
 *
 * @author Matthew A Price
 */
#[CoversClass(ArticlesController::class)]
class ArticlesControllerTest extends TestCase
{
    #[Test]
    public function it_hands_the_list_view_every_article(): void
    {
        $articles = new ArticlesController(new RouteType(path: 'articles'))->view();

        $this->assertInstanceOf(Articles::class, $articles);
        $this->assertSame(ArticlesData::getAll(), $articles->articles);
    }
}
