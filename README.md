# phossil

phossil is a PHP site kit with routing and typed components that builds to plain static HTML, ready for AWS Amplify or any static host.

You get routes, typed views and components while you build, and a folder of static files when you deploy.

**[Read the documentation at phossil.dev](https://phossil.dev)**

## Why

I love PHP, and I wanted the same `dist/` workflow JavaScript developers take for granted. phossil gives you one. Here are a couple of project types that I use this kit for:

- **Simple sites** that never needed a server. Build once and host the HTML anywhere.
- **Bigger sites** that will need infrastructure later but need client feedback now. Ship a static preview today and keep the PHP for when you add the backend.

Either way, it's one command: `composer build:zip` renders every route and zips it with your assets for a host like AWS Amplify.

## Quick start

phossil runs PHP, nginx and Vite in Docker. You also need PHP and Node on your machine, for Composer and pnpm:

- Docker, with Compose
- PHP 8.5 with Composer
- Node 24 or later, with pnpm

```sh
composer create-project matthewaprice/phossil-starter my-site
cd my-site
pnpm install       # Vite, Tailwind, oxfmt and oxlint
docker compose up --build
```

`create-project` copies the [phossil starter](https://github.com/matthewaprice/phossil-starter) into `my-site/` and installs phossil, the `matthewaprice/phossil` package in this repo, into `vendor/`. It then asks whether to start a git repository with a first commit, and what to call its default branch (press Enter for `main`). Answer `n` to the first question to skip it.

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
pnpm lint          # oxlint: app/js
```

## Documentation

- [Getting started](https://phossil.dev/getting-started/): install, run it locally, add a page, and build.
- [Directory structure](https://phossil.dev/features/#structure): what each file and folder does.
- [Deploy to AWS Amplify](https://phossil.dev/deploy-to-amplify/): deploy by hand or from GitHub Actions on every push.
- [Features](https://phossil.dev/features/): routing, typed views and components, Vite and Tailwind, Docker, and the build.

## Sites built with phossil

These sites run on phossil in production:

- [matthewaprice.com](https://matthewaprice.com): Matthew Price's personal site.
- [phossil.dev](https://phossil.dev): the phossil documentation.

## License

[MIT](LICENSE)
