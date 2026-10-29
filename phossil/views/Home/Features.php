<?php

namespace Phossil\Views\Home;

use Phossil\Components\{
    Card,
    CardOptions,
    View
};
use Phossil\Enums\{
    HeroiconSize,
    HeroiconStyle
};
use Phossil\Support\Heroicon;

readonly class Features extends View
{
    public function __construct(public array $features)
    {
    }

    public function render(): string
    {
        return $this->capture(function() {
            ?>
            <section class="shell grid gap-6 py-16 md:grid-cols-3">
                <?php
                foreach ( $this->features as $title => [$icon, $description] ) {
                    Card::create(
                        new CardOptions(
                            title: '<span class="flex items-center gap-2">' . Heroicon::render( $icon, HeroiconSize::Mini, HeroiconStyle::Solid, class: 'size-5 text-primary-500' ) . e( $title ) . '</span>',
                            content: e( $description )
                        )
                    );
                }
                ?>
            </section>
            <?php
        });
    }
}
