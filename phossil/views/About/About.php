<?php

namespace Phossil\Views\About;

use Phossil\Components\DescriptionList;
use Phossil\Components\DescriptionListItem;
use Phossil\Components\DescriptionListOptions;
use Phossil\Components\Layout;
use Phossil\Components\LayoutOptions;
use Phossil\Components\View;

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

        namespace Phossil\Views\NewPage;

        use Phossil\Components\Layout;
        use Phossil\Components\LayoutOptions;
        use Phossil\Components\View;

        readonly class NewPage extends View
        {
            public function render(): string
            {
                return $this->capture(function () {
                    Layout::create(
                        new LayoutOptions(
                            content: $this->content(),
                            title: 'New Page',
                            description: 'My awesome new page'
                        )
                    );
                });
            }

            private function content(): string
            {
                return $this->capture(function () {
                    ?>
                    <p>Some cool content.</p>
                    <?php
                });
            }
        }
        PHP;

    /** Example route for the "Adding a page" section. */
    private const EXAMPLE_ROUTE = <<<'PHP'
        new RouteType(
            path: 'new-page',
            view: NewPage::class
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
                <div class="max-w-3xl text-zinc-600">
                    <h1 class="text-3xl font-bold tracking-tight text-zinc-900">About phossil</h1>
                    <p class="mt-6 text-lg">
                        Write it in PHP. Ship it as HTML. phossil is a small PHP site kit designed to deploy static
                        HTML files and assets. You get routes, typed views and components while you build, and a folder
                        of static files when you deploy.
                    </p>
                    <p class="mt-4">
                        This page covers the essentials. The full documentation lives at
                        <a href="https://phossil.dev" target="_blank" class="link">phossil.dev</a>.
                    </p>

                    <nav class="mt-8 rounded-lg border border-zinc-200 bg-white p-5 text-sm">
                        <p class="font-semibold text-zinc-900">On this page</p>
                        <ul class="mt-2 grid gap-1 sm:grid-cols-2">
                            <?php
                            foreach ( self::SECTIONS as $anchor => $heading ) {
                                ?>
                                <li><a href="#<?= $anchor; ?>" class="transition hover:text-primary-700"><?= e( $heading ); ?></a></li>
                                <?php
                            }
                            ?>
                        </ul>
                    </nav>

                    <?= $this->heading( 'getting-started' ); ?>
                    <p class="mt-4">
                        phossil runs PHP, nginx and Vite in Docker. You also need PHP and Node on your machine, for
                        Composer and pnpm: Docker with Compose, PHP 8.5 with Composer, and Node 24 or later with pnpm.
                    </p>
                    <?= $this->codeBlock( "composer install\npnpm install\ndocker compose up --build" ); ?>
                    <p class="mt-4">
                        Open <code>http://localhost:8000</code>. Pages load their CSS and JS from the Vite dev server on
                        port 5173, so CSS and JS changes appear without a reload, and saving a PHP file reloads the page.
                    </p>

                    <?= $this->heading( 'adding-a-page' ); ?>
                    <p class="mt-4">
                        A page is a route and a view class. Create <code>phossil/views/NewPage/NewPage.php</code>, a
                        <code>readonly</code> class that extends <code>Phossil\Components\View</code>:
                    </p>
                    <?= $this->codeBlock( self::EXAMPLE_VIEW ); ?>
                    <p class="mt-4">Then add a <code>RouteType</code> for it to <code>phossil/routes/web.php</code>:</p>
                    <?= $this->codeBlock( self::EXAMPLE_ROUTE ); ?>
                    <p class="mt-4">
                        It's served at <code>/new-page/</code> straight away, and the next build writes
                        <code>dist/new-page/index.html</code>. Optionally, add it to the navigation in
                        <code>Layout::NAV</code>.
                    </p>
                    <p class="mt-4">
                        In development, nginx sends each request to <code>phossil/index.php</code>, and the router
                        echoes the matching view, or the 404 page when nothing matches. The build reads the same list,
                        so the site you preview is the site you ship. A route with <code>skip_build: true</code> is
                        still served in development, but the build leaves it out, so a live site shows only the pages
                        that are ready for prime time.
                    </p>

                    <?= $this->heading( 'components' ); ?>
                    <p class="mt-4">
                        A view is a <code>readonly</code> class that extends <code>Phossil\Components\View</code>. Its
                        <code>render()</code> method returns the page's markup by buffering a template closure bound to
                        the view, so the template reads the view's properties directly. Views nest. Echoing a view
                        renders it, so a page can place a section with <code>echo new Hero()</code>.
                    </p>
                    <p class="mt-4">
                        Components are classes paired with an options object: a class whose constructor takes named,
                        typed parameters. Your IDE completes every option and flags a wrong type as you write it, and
                        there's no template language to learn. Components echo their markup by default. Pass
                        <code>ob: true</code> to get it back as a string instead, to hand to another component. phossil
                        comes with <code>Anchor</code>, <code>Button</code>, <code>ButtonGroup</code>,
                        <code>Badge</code>, <code>Card</code>, <code>DescriptionList</code>, <code>Dialog</code> and
                        <code>Layout</code>, all in <code>phossil/components/</code>.
                    </p>
                    <p class="mt-4">
                        The router creates each view with <code>new</code> and no arguments, so a view's constructor is
                        a natural place to load its data: from a database, a folder of Markdown or a headless CMS.
                        Routes are PHP too, so one route per database row gives you a page for every post, product or
                        team member. The HTML is a snapshot from the moment you build, so when the data changes, build
                        again.
                    </p>

                    <?= $this->heading( 'assets' ); ?>
                    <p class="mt-4">
                        CSS and JS go through Vite, with Tailwind CSS v4. Tailwind reads the class names straight from
                        your PHP templates. The layout links them with <code>vite()</code>. While the Vite container
                        runs, it points at the dev server. Otherwise it links the hashed files from Vite's manifest, and
                        a new build gives changed files new names, so browsers fetch them after a deploy.
                    </p>
                    <p class="mt-4">
                        Images are served as they are. <code>asset()</code> adds the file's modified time to its URL, so
                        browsers fetch new copies of exactly the images that changed.
                    </p>

                    <?= $this->heading( 'building' ); ?>
                    <p class="mt-4">With the containers still running, build the site and zip it:</p>
                    <?= $this->codeBlock( 'composer build:zip' ); ?>
                    <p class="mt-4">
                        The build runs <code>vite build</code>, then asks nginx for every route in turn and saves each
                        response as <code>dist/&lt;route&gt;/index.html</code>. It writes the 404 page to
                        <code>dist/404.html</code>, copies the CSS, JS and images to <code>dist/assets/</code>, tidies
                        the HTML with oxfmt, and zips <code>dist/</code> into <code>dist.zip</code>, ready to upload.
                        Because the build fetches each page over HTTP, it saves exactly what a browser would get. A
                        route that doesn't answer with a 200 stops the build, so a broken page never ships.
                    </p>
                    <p class="mt-4">
                        To put the site, or a preview for a client, on AWS Amplify Hosting, create a new app in the
                        Amplify console, choose to deploy without a Git provider, and upload <code>dist.zip</code>. Any
                        other static host works the same way: upload what's in <code>dist/</code>.
                    </p>

                    <?= $this->heading( 'commands' ); ?>
                    <div class="mt-4 rounded-lg border border-zinc-200 bg-white p-5">
                        <?php
                        DescriptionList::create(
                            new DescriptionListOptions(
                                items: [
                                    new DescriptionListItem( dt: '<code>docker compose up</code>', dd: e( 'Start PHP, nginx and Vite for development.' ) ),
                                    new DescriptionListItem( dt: '<code>composer build</code>', dd: e( 'Render every route to static HTML in dist/.' ) ),
                                    new DescriptionListItem( dt: '<code>composer build:zip</code>', dd: e( 'Build, then zip dist/ into dist.zip.' ) ),
                                    new DescriptionListItem( dt: '<code>pnpm format</code>', dd: e( 'Format JS, CSS, JSON and Markdown with oxfmt.' ) ),
                                    new DescriptionListItem( dt: '<code>pnpm format:check</code>', dd: e( 'Check formatting without writing changes.' ) ),
                                    new DescriptionListItem( dt: '<code>pnpm lint</code>', dd: e( 'Check the JS in phossil/js with oxlint.' ) ),
                                ]
                            )
                        );
                        ?>
                    </div>

                    <p class="mt-12 border-t border-zinc-200 pt-6">
                        For deploying on every push with GitHub Actions, connecting a database and more, see
                        <a href="https://phossil.dev" target="_blank" class="link">phossil.dev</a>.
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
        return '<h2 id="' . $anchor . '" class="mt-12 scroll-mt-6 text-xl font-semibold text-zinc-900">' . e( self::SECTIONS[$anchor] ) . '</h2>';
    }

    /**
     * A block of code, escaped and preformatted.
     */
    private function codeBlock( string $source ): string
    {
        return '<pre class="mt-4 overflow-x-auto rounded-lg bg-zinc-900 p-4 text-sm text-zinc-100"><code>' . e( $source ) . '</code></pre>';
    }
}
