<?php

namespace Phossil\Components;

use Phossil\Enums\{
    HeroiconSize,
    HeroiconStyle,
    Icon
};
use Phossil\Support\{
    Heroicon,
    Request
};

/**
 * Renders the page chrome around a view's content, echoing rather than returning.
 *
 * @author Matthew A Price
 */
class Layout extends Component
{
    /** The main navigation, as URL => label. */
    private const NAV = [
        '/' => 'Home',
        '/about/' => 'About'
    ];

    /** The project's source repository. */
    private const GITHUB_URL = 'https://github.com/matthewaprice/phossil';

    /** The project's documentation site. */
    private const DOCS_URL = 'https://phossil.dev';

    /** Configuration for this layout. */
    public LayoutOptions $options;

    public function __construct(LayoutOptions $options)
    {
        $this->options = $options;
    }

    /**
     * Builds a layout and echoes it.
     */
    public static function create(LayoutOptions $options): void
    {
        ( new self($options) )->get();
    }

    /**
     * Echoes the whole page around the view's content.
     */
    private function get(): void
    {
        $title = $this->options->title !== '' ? $this->options->title . ' · phossil' : 'phossil';
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="utf-8">
            <title><?= e( $title ); ?></title>
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <meta name="description" content="<?= e( $this->options->description ); ?>">

            <?= vite( 'css/app.css', 'js/app.js' ); ?>
            <link rel="icon" type="image/svg+xml" href="<?= asset( 'img/favicon.svg' ); ?>">
        </head>

        <body class="flex min-h-screen flex-col">
            <header class="border-b border-slate-200 bg-white">
                <div class="shell flex flex-wrap items-center gap-4 py-4">
                    <a href="/" class="flex items-center gap-2 font-semibold text-slate-900">
                        <img src="<?= asset( 'img/favicon.svg' ); ?>" alt="" class="size-7">
                        phossil
                    </a>
                    <nav class="ml-auto flex gap-1 text-sm font-medium">
                        <?php
                        foreach ( self::NAV as $href => $text ) {
                            Anchor::create(
                                new AnchorOptions(
                                    href: $href,
                                    text: $text,
                                    current: $this->isCurrent( $href ),
                                    class: 'rounded-md px-3 py-2 text-slate-600 hover:bg-slate-100 hover:text-slate-900 aria-[current=page]:bg-slate-900 aria-[current=page]:text-white',
                                )
                            );
                        }
                        ?>
                    </nav>
                    <div class="flex items-center gap-1 border-l border-slate-200 pl-3 text-sm font-medium">
                        <?php
                        Anchor::create(
                            new AnchorOptions(
                                href: self::DOCS_URL,
                                text: Heroicon::render( Icon::book_open, HeroiconSize::Mini, HeroiconStyle::Solid, class: 'size-5' ) . '<span>Docs</span>',
                                target: '_blank',
                                title: 'Documentation',
                                class: 'flex items-center gap-1.5 rounded-md px-3 py-2 text-slate-600 hover:bg-slate-100 hover:text-slate-900',
                            )
                        );
                        Anchor::create(
                            new AnchorOptions(
                                href: self::GITHUB_URL,
                                text: $this->githubMark() . '<span class="sr-only">GitHub</span>',
                                target: '_blank',
                                title: 'View on GitHub',
                                class: 'flex items-center rounded-md p-2 text-slate-600 hover:bg-slate-100 hover:text-slate-900',
                            )
                        );
                        ?>
                    </div>
                </div>
            </header>

            <main class="flex-1">
                <?= $this->options->content; ?>
            </main>

            <footer class="border-t border-slate-200 bg-white">
                <div class="shell py-6 text-sm text-slate-500">
                    &copy<?= date('Y'); ?> My Site
                </div>
            </footer>

            <?php Dialog::addDialogs(); ?>
        </body>
        </html>
        <?php
    }

    /**
     * The GitHub logo as inline SVG, since heroicons has no brand marks.
     */
    private function githubMark(): string
    {
        return '<svg class="size-5" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><path d="M8 0c4.42 0 8 3.58 8 8a8.013 8.013 0 0 1-5.45 7.59c-.4.08-.55-.17-.55-.38 0-.27.01-1.13.01-2.2 0-.75-.25-1.23-.54-1.48 1.78-.2 3.65-.88 3.65-3.95 0-.88-.31-1.59-.82-2.15.08-.2.36-1.02-.08-2.12 0 0-.67-.22-2.2.82-.64-.18-1.32-.27-2-.27-.68 0-1.36.09-2 .27-1.53-1.03-2.2-.82-2.2-.82-.44 1.1-.16 1.92-.08 2.12-.51.56-.82 1.28-.82 2.15 0 3.06 1.86 3.75 3.64 3.95-.23.2-.44.55-.51 1.07-.46.21-1.61.55-2.33-.66-.15-.24-.6-.83-1.23-.82-.67.01-.27.38.01.53.34.19.73.9.82 1.13.16.45.68 1.31 2.69.94 0 .67.01 1.3.01 1.49 0 .21-.15.45-.55.38A7.995 7.995 0 0 1 0 8c0-4.42 3.58-8 8-8Z"/></svg>';
    }

    /**
     * Whether a nav link points at the page being rendered.
     */
    private function isCurrent( string $href ): bool
    {
        return trim( $href, '/' ) === Request::path();
    }
}
