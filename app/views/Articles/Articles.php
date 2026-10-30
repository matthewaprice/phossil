<?php

namespace App\Views\Articles;

use App\Components\Layout;
use App\Components\LayoutOptions;
use Phossil\Components\Anchor;
use Phossil\Components\AnchorOptions;
use Phossil\Components\Card;
use Phossil\Components\CardOptions;
use Phossil\Components\View;

readonly class Articles extends View
{
    public function __construct(public array $articles)
    {
    }

    public function render(): string
    {
        return $this->capture(function() {
            Layout::create(
                new LayoutOptions(
                    title: 'Articles',
                    content: $this->content()
                )
            );
        });
    }

    private function content(): string
    {
        return $this->capture(function() {
            ?>
            <div class="shell py-4">
                <div class="text-zinc-600">
                    <?php
                    if (!empty($this->articles)) {
                        foreach ($this->articles as $article) {
                            Card::create(
                                new CardOptions(
                                    title: $article['title'],
                                    content: $article['content'],
                                    class: 'mb-6',
                                    footer: Anchor::create(
                                        new AnchorOptions(
                                            text: 'Read More',
                                            href: "/articles/{$article['slug']}/",
                                            ob: true
                                        )
                                    )
                                )
                            );
                        }
                    }
                    ?>
                </div>
            </div>
            <?php
        });
    }
}
