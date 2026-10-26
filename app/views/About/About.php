<?php

namespace App\Views\About;

use App\Components\DescriptionList;
use App\Components\DescriptionListItem;
use App\Components\DescriptionListOptions;
use App\Components\Layout;
use App\Components\LayoutOptions;
use App\Components\View;

readonly class About extends View
{
    /** Section anchor => heading, in page order. */
    private const SECTIONS = [
        'getting-started' => 'Getting started',
        'adding-a-page' => 'Adding a page',
        'components' => 'Views and components',
        'assets' => 'CSS, JS and images',
        'building' => 'Building and deploying',
        'commands' => 'Commands',
    ];

    /** Example view for the "Adding a page" section. */
    private const EXAMPLE_VIEW = <<<'PHP'
        <?php

        namespace App\Views\Pricing;

        use App\Components\Layout;
        use App\Components\LayoutOptions;
        use App\Components\View;

        readonly class Pricing extends View
        {
            public function render(): string
            {
                return $this->capture(function() {
                    Layout::create(
                        new LayoutOptions(
                            content: $this->content(),
                            title: 'Pricing',
                            description: 'Plans and prices.'
                        )
                    );
                });
            }

            private function content(): string
            {
                return $this->capture(function() {
                    ?>
                    <section class="shell py-16">
                        <h1>Pricing</h1>
                    </section>
                    <?php
                });
            }
        }
        PHP;

    /** Example route for the "Adding a page" section. */
    private const EXAMPLE_ROUTE = <<<'PHP'
        new RouteType(
            path: 'pricing',
            view: Pricing::class
        ),
        PHP;

    public function render(): string
    {
        return $this->capture(function() {
            Layout::create(
                new LayoutOptions(
                    content: $this->content(),
                    title: 'About',
                    description: 'How to run, extend and build a phossil site.'
                )
            );
        });
    }

    private function content(): string
    {
        return $this->capture(function() {
            ?>
            <article class="shell py-16">
                <div class="max-w-3xl text-slate-600">
                    <h1 class="text-3xl font-bold tracking-tight text-slate-900">About phossil</h1>
                    <p class="mt-6 text-lg">
                        phossil is a small PHP site kit that builds to static HTML. You write routes, typed views and
                        components in PHP, preview them with PHP running in Docker, then deploy a folder of plain files.
                        No PHP runs in production.
                    </p>
                    <p class="mt-4">
                        This page covers the essentials. The full documentation lives at
                        <a href="https://phossil.dev" target="_blank" class="font-medium text-slate-900 underline">phossil.dev</a>.
                    </p>

                    <nav class="mt-8 rounded-lg border border-slate-200 bg-white p-5 text-sm">
                        <p class="font-semibold text-slate-900">On this page</p>
                        <ul class="mt-2 grid gap-1 sm:grid-cols-2">
                            <?php
                            foreach ( self::SECTIONS as $anchor => $heading ) {
                                ?>
                                <li><a href="#<?= $anchor; ?>" class="hover:text-slate-900 hover:underline"><?= e( $heading ); ?></a></li>
                                <?php
                            }
                            ?>
                        </ul>
                    </nav>

                    <?= $this->heading( 'getting-started' ); ?>
                    <p class="mt-4">
                        You need Docker with Compose, which runs PHP, nginx and Vite, plus PHP 8.5 with Composer and
                        Node 24 or later with pnpm on your machine.
                    </p>
                    <?= $this->codeBlock( "composer install\npnpm install\ndocker compose up --build" ); ?>
                    <p class="mt-4">
                        Open <code>http://localhost:8000</code>. Pages load CSS and JS from the Vite dev server on port
                        5173, so style changes appear without a reload, and saving a PHP file reloads the page.
                    </p>

                    <?= $this->heading( 'adding-a-page' ); ?>
                    <p class="mt-4">
                        A page is a view class plus a route. Create <code>app/views/Pricing/Pricing.php</code>:
                    </p>
                    <?= $this->codeBlock( self::EXAMPLE_VIEW ); ?>
                    <p class="mt-4">Then add a route for it in <code>app/routes/web.php</code>:</p>
                    <?= $this->codeBlock( self::EXAMPLE_ROUTE ); ?>
                    <p class="mt-4">
                        The page is served at <code>/pricing/</code> straight away, and the next build writes
                        <code>dist/pricing/index.html</code>. To link it from the header, add it to
                        <code>Layout::NAV</code>.
                    </p>
                    <p class="mt-4">
                        In development, nginx sends every request to <code>app/index.php</code> and the router renders
                        the matching view, or the 404 page. The build reads the same route list, so the preview matches
                        what ships. Pass <code>skip_build: true</code> to a route to serve it in development but leave
                        it out of the build, for a page still in progress.
                    </p>

                    <?= $this->heading( 'components' ); ?>
                    <p class="mt-4">
                        A view is a <code>readonly</code> class extending <code>App\Components\View</code>. Its
                        <code>render()</code> method usually calls <code>capture()</code>, so templates can read the
                        view's properties directly. Views nest: echoing one view inside another renders it in place.
                    </p>
                    <p class="mt-4">
                        Components are classes configured by typed options objects, which gives you IDE completion and
                        type checking without a template language. They echo their markup by default; pass
                        <code>ob: true</code> to get it back as a string instead. The kit includes <code>Anchor</code>,
                        <code>Button</code>, <code>ButtonGroup</code>, <code>Badge</code>, <code>Card</code>,
                        <code>DescriptionList</code>, <code>Dialog</code> and <code>Layout</code>, all in
                        <code>app/components/</code>.
                    </p>
                    <p class="mt-4">
                        Views can load data in their constructor from any source, such as SQLite, a folder of Markdown
                        or a headless CMS, and routes can be generated from data, one page per row. The build is a
                        snapshot, so rebuild when the data changes.
                    </p>

                    <?= $this->heading( 'assets' ); ?>
                    <p class="mt-4">
                        CSS and JS are built by Vite with Tailwind CSS v4, which reads class names straight from the
                        PHP templates. The <code>vite()</code> helper points at the dev server while the Vite container
                        is running, and otherwise links the hashed files from Vite's manifest. The <code>asset()</code>
                        helper adds the file's modified time to image URLs so changed images are fetched again.
                    </p>

                    <?= $this->heading( 'building' ); ?>
                    <p class="mt-4">With the containers running:</p>
                    <?= $this->codeBlock( 'composer build:zip' ); ?>
                    <p class="mt-4">
                        The build runs <code>vite build</code>, requests every route from nginx and saves each response
                        as <code>dist/&lt;route&gt;/index.html</code>. It writes the 404 page to
                        <code>dist/404.html</code>, copies assets to <code>dist/assets/</code>, formats the HTML and
                        zips the result into <code>dist.zip</code>. A route that doesn't return a 200 stops the build,
                        so broken pages don't ship.
                    </p>
                    <p class="mt-4">
                        To deploy to AWS Amplify, create an app without a Git provider and upload
                        <code>dist.zip</code>.  This should hold true for most other hosting providers like Cloudflare Pages or Vercel.
                    </p>

                    <?= $this->heading( 'commands' ); ?>
                    <div class="mt-4 rounded-lg border border-slate-200 bg-white p-5">
                        <?php
                        DescriptionList::create(
                            new DescriptionListOptions(
                                items: [
                                    new DescriptionListItem( dt: '<code>docker compose up</code>', dd: e( 'Start PHP, nginx and Vite for development.' ) ),
                                    new DescriptionListItem( dt: '<code>composer build</code>', dd: e( 'Render every route to static HTML in dist/.' ) ),
                                    new DescriptionListItem( dt: '<code>composer build:zip</code>', dd: e( 'Build, then zip dist/ into dist.zip.' ) ),
                                    new DescriptionListItem( dt: '<code>pnpm format</code>', dd: e( 'Format JS, CSS, JSON and Markdown.' ) ),
                                    new DescriptionListItem( dt: '<code>pnpm format:check</code>', dd: e( 'Check formatting without writing changes.' ) ),
                                    new DescriptionListItem( dt: '<code>pnpm lint</code>', dd: e( 'Lint the JavaScript in app/js.' ) ),
                                ]
                            )
                        );
                        ?>
                    </div>

                    <p class="mt-12 border-t border-slate-200 pt-6">
                        For deploying on every push with GitHub Actions, connecting a database and more, see
                        <a href="https://phossil.dev" target="_blank" class="font-medium text-slate-900 underline">phossil.dev</a>.
                    </p>
                </div>
            </article>
            <?php
        });
    }

    /**
     * A section heading, anchored so the "On this page" links can jump to it.
     */
    private function heading( string $anchor ): string
    {
        return '<h2 id="' . $anchor . '" class="mt-12 scroll-mt-6 text-xl font-semibold text-slate-900">' . e( self::SECTIONS[$anchor] ) . '</h2>';
    }

    /**
     * A block of code, escaped and preformatted.
     */
    private function codeBlock( string $source ): string
    {
        return '<pre class="mt-4 overflow-x-auto rounded-lg bg-slate-900 p-4 text-sm text-slate-100"><code>' . e( $source ) . '</code></pre>';
    }
}
