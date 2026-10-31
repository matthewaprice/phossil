<?php

namespace App\Views\Home;

use App\Components\{
    Layout,
    LayoutOptions
};
use Phossil\Components\View;

readonly class Home extends View
{
    public function __construct(public array $features)
    {
    }

    public function render(): string
    {
        return $this->capture(function() {
            Layout::create(
                new LayoutOptions(
                    content: $this->content(),
                    description: 'A PHP site that builds to static HTML.'
                )
            );
        });
    }

    private function content(): string
    {
        return $this->capture(function() {
            echo new Banner();
            echo new Features($this->features);
        });
    }
}
