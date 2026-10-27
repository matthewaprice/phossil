# phossil

A small PHP site with routing and components that builds to plain static HTML.

While you develop, nginx sends every page request to `phossil/index.php` and PHP renders it. To deploy, `composer build` requests every route from that same nginx and saves the responses to `dist/`, which any static host can serve. No PHP runs in production.

[Full Documentation - https://phossil.dev](https://phossil.dev)

## Why

I love PHP, and I wanted the same <code>dist/</code> workflow JavaScript developers take for
granted. phossil gives you one, and it covers two kinds of projects:

- **Simple sites** that never needed a server. Build once and host the HTML anywhere.
- **Bigger sites** that will need infrastructure later but need client feedback now. Ship a static preview today and keep the PHP for when you add the backend.

Either way, it's one command: <code>composer build:zip</code> renders every route and zips it
with your assets for a host like AWS Amplify.

- **Type safety while you build.** Components take typed options objects and views are plain PHP classes, so your IDE can flag issues as you work.
- **The same setup everywhere.** PHP, nginx and Vite all run in Docker, so every project and every machine runs the same versions with the same config.
- **A quick preview to share.** `composer build:zip` turns the site into a zip of static files that you can upload to a host such as AWS Amplify, with no server to set up.
- **Automated deploys if you want them.** The build is a single command, so it can also run as a step in a CI pipeline that deploys on every push.

## Setup

Curretly uses Docker, PHP 8.5 with Composer, and Node 24 with pnpm.

```sh
composer install   # generates the class autoloader in vendor/
pnpm install       # Vite, Tailwind, oxfmt and oxlint
docker compose up --build
```

Open http://localhost:8000.

The `vite` container runs the Vite dev server on port 5173. Pages load `phossil/css/app.css` (Tailwind) and `phossil/js/app.js` from it, so CSS and JS changes appear without a reload, and saving a PHP file reloads the page.

## Build

```sh
composer build:zip
```

This runs `pnpm build` (`vite build`, into `phossil/dist/`), then empties `dist/`, renders each route to `dist/<route>/index.html`, writes the 404 page to `dist/404.html`, and copies the CSS, JS and images to `dist/assets/`. The pages always link the built, hashed files, even while the dev server runs.

The built HTML is then tidied with oxfmt.

You get a dist.zip at the root of the repo to upload to, for example, AWS Amplify.

## Format and lint

```sh
pnpm format        # oxfmt: JS, CSS, JSON, Markdown
pnpm format:check  # the same, without writing
pnpm format:build. # run on the built dist folder
pnpm lint          # oxlint: phossil/js
```

## How it fits together

| Path                             | What it does                                                                                                                                       |
| -------------------------------- | -------------------------------------------------------------------------------------------------------------------------------------------------- |
| `phossil/index.php`              | Front controller: loads the app and hands the request to the router.                                                                               |
| `phossil/includes/bootstrap.php` | Loads config, the autoloader, helpers and routes, without handling a request. The build uses it too.                                               |
| `phossil/routes/web.php`         | The list of paths and the view each one renders. Add a page here.                                                                                  |
| `phossil/services/Router/`       | Matches the request path to a route and echoes its view, or the 404 view.                                                                          |
| `phossil/components/`            | `View` (base class for pages), `Layout` (page chrome), and small components (`Anchor`, `Card`), each configured by an `*Options` object.           |
| `phossil/views/`                 | One folder per page.                                                                                                                               |
| `phossil/services/Build.php`     | Crawls the routes through nginx and writes `dist/`.                                                                                                |
| `phossil/includes/build.php`     | The script `composer build` runs inside the php container.                                                                                         |
| `phossil/support/Vite.php`       | Behind the `vite()` helper: links the dev server while it runs, or the hashed files from Vite's manifest.                                          |
| `vite.config.js`                 | The Vite entries, Tailwind, and the dev server settings for running in Docker.                                                                     |
| `docker/nginx/site.conf`         | Serves `/assets/*` from `phossil/img/` and `phossil/dist/`, and sends every other URL to `index.php`. No other file under `phossil/` is reachable. |

### Assets

CSS and JS go through Vite. `Layout` links them with `vite('css/app.css', 'js/app.js')`: it points at the dev server while it runs, and otherwise at the hashed files such as `/assets/app-DQowg002.css`. A new build gives changed files new names, so browsers fetch them after a deploy. To add an entry, list it in `build.rolldownOptions.input` in `vite.config.js` and pass it to `vite()`.

Images are served as they are. Templates link them with `asset('img/favicon.svg')`, which gives `/assets/img/favicon.svg?v=<modified time>`. nginx serves that URL in development, and the build copies the file to the same path, so links work the same in both.

### Adding a page

1. Create `phossil/views/MyNewPage/MyNewPage.php`, a `readonly` class extending `Phossil\Components\View`. Copy `About.php` as a starting point.
2. Add `new RouteType(path: 'my-new-page', view: MyNewPage::class)` to `phossil/routes/web.php`.
3. Optionally, add it to `Layout::NAV`.

It is served at `/my-new-page/` straight away, and the next `composer build` writes `dist/my-new-page/index.html`.

To keep a page out of the build while you work on it, add `skip_build: true` to its `RouteType`. It is still served in development, but the build leaves it out, so a build shows only includes the pages that are ready for prime time. Remove anything that links to it, such as its `Layout::NAV` entry, before you build, or the static site will have a broken link.
