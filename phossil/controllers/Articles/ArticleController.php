<?php

namespace Phossil\Controllers\Articles;

use Phossil\Components\View;
use Phossil\Data\ArticlesData;
use Phossil\Services\Controller;
use Phossil\Views\Articles\Article;
use Phossil\Views\NotFound\NotFound;

class ArticleController extends Controller
{

    private array $article = [];

    public function view(): View
    {
        $articles = ArticlesData::getAll();
        $slug = explode('/', $this->route->path);

        if ($slug[1] !== '') {
            $this->article = array_find($articles, fn (array $article): bool => $article['slug'] === $slug[1]) ?? [];

            if (empty($this->article)) {
                return new NotFound();
            }
        }

        return new Article(article: $this->article);
    }
}
