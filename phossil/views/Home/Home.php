<?php

namespace Phossil\Views\Home;

use Phossil\Components\{
    Layout,
    LayoutOptions,
    View
};

readonly class Home extends View
{
    public function render(): string
    {
        return $this->capture(function() {
            Layout::create(
                new LayoutOptions(
                    content: $this->content(),
                    description: 'A small PHP site that builds to static HTML.'
                )
            );
        });
    }

    private function content(): string
    {
        return $this->capture(function() {
            echo new Banner();
            echo new Features();
        });
    }
}
