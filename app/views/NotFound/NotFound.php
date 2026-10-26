<?php

namespace App\Views\NotFound;

use App\Components\Anchor;
use App\Components\AnchorOptions;
use App\Components\Layout;
use App\Components\LayoutOptions;
use App\Components\View;

readonly class NotFound extends View
{
    public function render(): string
    {
        return $this->capture(function() {
            Layout::create(
                new LayoutOptions(
                    content: $this->content(),
                    title: 'Page not found'
                )
            );
        });
    }

    private function content(): string
    {
        return $this->capture(function() {
            ?>
            <section class="shell py-24 text-center">
                <p class="text-sm font-semibold text-slate-500">404</p>
                <h1 class="mt-2 text-3xl font-bold tracking-tight text-slate-900">Page not found</h1>
                <p class="mt-6">
                    <?php
                    Anchor::create(
                        new AnchorOptions(
                            href: '/',
                            text: 'Back to the home page',
                            class: 'font-medium text-slate-900 underline'
                        )
                    );
                    ?>
                </p>
            </section>
            <?php
        });
    }
}
