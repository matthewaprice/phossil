# phossil

phossil is a small PHP site kit with routing and typed components that builds to plain static HTML, ready for AWS Amplify or any static host.

You get routes, typed views and components while you build, and a folder of static files when you deploy.

**[Read the documentation at phossil.dev](https://phossil.dev)**

## Why

I love PHP, and I wanted the same `dist/` workflow JavaScript developers take for granted. phossil gives you one, and it covers two kinds of projects:

- **Simple sites** that never needed a server. Build once and host the HTML anywhere.
- **Bigger sites** that will need infrastructure later but need client feedback now. Ship a static preview today and keep the PHP for when you add the backend.

Either way, it's one command: `composer build:zip` renders every route and zips it with your assets for a host like AWS Amplify.

## Quick start

phossil runs PHP, nginx and Vite in Docker. You also need PHP and Node on your machine, for Composer and pnpm:

- Docker, with Compose
- PHP 8.5 with Composer
- Node 24 or later, with pnpm

```sh
git clone https://github.com/matthewaprice/phossil.git my-site
cd my-site
composer install   # generates the class autoloader in vendor/
pnpm install       # Vite, Tailwind, oxfmt and oxlint
docker compose up --build
```

Open http://localhost:8000.

## Build

With the containers still running, build the site and zip it:

```sh
composer build:zip
```

You get `dist/`, and `dist.zip` beside it at the root of the repo, ready to upload to any static host.

## Format and lint

```sh
pnpm format        # oxfmt: JS, CSS, JSON, Markdown
pnpm format:check  # the same, without writing
pnpm format:build  # run on the built dist folder
pnpm lint          # oxlint: phossil/js
```

## Documentation

- [Getting started](https://phossil.dev/getting-started/): install, run it locally, add a page, and build.
- [Directory structure](https://phossil.dev/directory-structure/): what each file and folder does.
- [Deploy to AWS Amplify](https://phossil.dev/deploy-to-amplify/): deploy by hand or from GitHub Actions on every push.
- [Features](https://phossil.dev/features/): routing, typed views and components, Vite and Tailwind, Docker, and the build.
