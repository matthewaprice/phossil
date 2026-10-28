<?php

namespace Phossil\Views\Home;

use Phossil\Components\{
    Card,
    CardOptions,
    View
};
use Phossil\Enums\{
    HeroiconSize,
    HeroiconStyle,
    Icon
};
use Phossil\Support\Heroicon;

readonly class Features extends View
{
    /** Feature title => [icon, description]. */
    private const FEATURES = [
        'Routing' => [Icon::map, 'One list of paths in routes/web.php decides which view renders each URL.'],
        'Components' => [Icon::puzzle_piece, 'Typed options objects in, markup out. No template language to learn.'],
        'Static build' => [Icon::rocket_launch, 'One command crawls every route and writes dist/ for any static host.'],
    ];

    public function render(): string
    {
        return $this->capture(function() {
            ?>
            <section class="shell grid gap-6 py-16 md:grid-cols-3">
                <?php
                foreach ( self::FEATURES as $title => [$icon, $description] ) {
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
