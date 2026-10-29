<?php

namespace Phossil\Controllers\Articles;

use Phossil\Data\ArticlesData;
use Phossil\Services\Controller;
use Phossil\Views\Articles\Articles;

class ArticlesController extends Controller
{
    public function view(): Articles
    {
        return new Articles(articles: ArticlesData::getAll());
    }
}
