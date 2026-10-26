<?php

namespace App\Views\Home;

use App\Components\Anchor;
use App\Components\AnchorOptions;
use App\Components\Button;
use App\Components\ButtonOptions;
use App\Components\Dialog;
use App\Components\DialogOptions;
use App\Components\View;
use App\Enums\Buttons;

readonly class Banner extends View
{
    public function render(): string
    {
        return $this->capture(function() {
            ?>
            <section class="bg-slate-900 text-white">
                <div class="shell py-20">
                    <h1 class="max-w-2xl text-4xl font-bold tracking-tight text-balance sm:text-5xl">
                        Write it in PHP. Ship it as HTML.
                    </h1>
                    <p class="mt-6 max-w-xl text-lg text-slate-300">
                        Routes, views and components while you develop; a folder of plain files when you deploy.
                    </p>
                    <div class="mt-8 flex flex-wrap gap-3">
                        <?php
                        Anchor::create(
                            new AnchorOptions(
                                href: '/about/',
                                text: 'How it works',
                                class: Buttons::make(Buttons::Large)
                            )
                        );

                        Button::create(
                            new ButtonOptions(
                                text: 'Open a dialog',
                                styles: [Buttons::Large, Buttons::Bordered],
                                data: ['dialog-target' => '#home-dialog'],
                                class: 'dialog-trigger'
                            )
                        );
                        ?>
                    </div>
                </div>
            </section>
            <?php
            Dialog::create(
                new DialogOptions(
                    title: 'Hello from a dialog',
                    content: '<p class="text-slate-600">' . e( 'This markup was rendered by the Dialog component and is emitted at the end of the page by the layout.' ) . '</p>',
                    size: 'sm',
                    id: 'home-dialog'
                )
            );
        });
    }
}
