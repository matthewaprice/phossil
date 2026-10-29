<?php

namespace Phossil\Controllers\Home;

use Phossil\Enums\Icon;
use Phossil\Views\Home\Home;

class HomeController
{
    public function __construct()
    {
    }

    public function view(): Home
    {
        $features = [
            'Routing' => [Icon::map, 'One list of paths in routes/web.php decides which view renders each URL.'],
            'Components' => [Icon::puzzle_piece, 'Typed options objects in, markup out. No template language to learn.'],
            'Static build' => [Icon::rocket_launch, 'One command crawls every route and writes dist/ for any static host.'],
        ];

        return new Home(features: $features);
    }
}
