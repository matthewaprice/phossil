<?php

namespace App\Views\Articles;

use App\Components\Layout;
use App\Components\LayoutOptions;
use Phossil\Components\Card;
use Phossil\Components\CardOptions;
use Phossil\Components\View;

readonly class Article extends View
{
    public function __construct(public array $article)
    {
    }

    public function render(): string
    {
        return $this->capture(function() {
            Layout::create(
                new LayoutOptions(
                    title: $this->article['title'],
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
                        Card::create(
                            new CardOptions(
                                title: '',
                                content: $this->article['content'],
                            )
                        );
                    ?>
                </div>
            </div>
            <?php
        });
    }
}
