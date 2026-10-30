<?php

namespace App\Views\Home;

use Phossil\Components\{
    Anchor,
    AnchorOptions,
    Button,
    ButtonOptions,
    Dialog,
    DialogOptions,
    View
};
use Phossil\Enums\Buttons;

readonly class Banner extends View
{
    public function render(): string
    {
        return $this->capture(function() {
            ?>
            <section class="bg-zinc-900 text-white">
                <div class="shell py-20">
                    <h1 class="max-w-2xl text-4xl font-bold tracking-tight text-balance sm:text-5xl">
                        Write it in PHP. <span class="text-primary-400">Ship it as HTML.</span>
                    </h1>
                    <p class="mt-6 max-w-xl text-lg text-zinc-400">
                        Routes, views and components while you develop; a folder of plain files when you deploy.
                    </p>
                    <div class="mt-8 flex flex-wrap gap-3">
                        <?php
                        Anchor::create(
                            new AnchorOptions(
                                href: '/about/',
                                text: 'How it works',
                                class: Buttons::make(Buttons::Large, Buttons::Primary)
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
                    content: 'This markup was rendered by the Dialog component and is emitted at the end of the page by the layout.',
                    footer: 'Here is some dialog footer text',
                    size: 'sm',
                    id: 'home-dialog'
                )
            );
        });
    }
}
