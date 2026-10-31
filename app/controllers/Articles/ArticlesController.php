<?php

namespace App\Controllers\Articles;

use App\Data\ArticlesData;
use Phossil\Services\Controller;
use App\Views\Articles\Articles;

class ArticlesController extends Controller
{
    public function view(): Articles
    {
        return new Articles(articles: ArticlesData::getAll());
    }
}
