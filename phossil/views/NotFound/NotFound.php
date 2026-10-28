<?php

namespace Phossil\Views\NotFound;

use Phossil\Components\Anchor;
use Phossil\Components\AnchorOptions;
use Phossil\Components\Layout;
use Phossil\Components\LayoutOptions;
use Phossil\Components\View;

readonly class NotFound extends View
{
    public function render(): string
    {
        return $this->capture(function() {
            Layout::create(
                new LayoutOptions(
                    content: $this->content(),
                    title: 'Page not found - 404'
                )
            );
        });
    }

    private function content(): string
    {
        return $this->capture(function() {
            ?>
            <section class="shell py-4">
                <p class="mt-6">
                    <?php
                    Anchor::create(
                        new AnchorOptions(
                            href: '/',
                            text: 'Back to the home page',
                            class: 'link'
                        )
                    );
                    ?>
                </p>
            </section>
            <?php
        });
    }
}
