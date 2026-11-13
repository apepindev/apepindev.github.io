# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

Personal blog at apepin.dev. Static site built with Jigsaw (PHP, Blade, Markdown), Tailwind CSS 3, Laravel Mix and Vue 2. Forked from the jigsaw-blog-plus starter template.

## Commands

- `composer install` and `bun install` set up dependencies. PHP 8.4 is required.
- `bun run dev` builds the local site into `build_local/` using `config.php`.
- `bun run watch` rebuilds on change and serves `build_local/` with BrowserSync.
- `bun run prod` builds into `build_production/` with `config.production.php` merged over `config.php`.
- No test suite or linter exists. Verify changes by building and checking the output HTML.

Package scripts run under the Bun runtime (`bunfig.toml`). Homebrew Node 26 loads yargs as ESM and breaks Laravel Mix, so do not switch back to npm or node.

## Build and deploy

- `laravel-mix-jigsaw` drives the build: Mix compiles assets into `source/assets/build/`, then Jigsaw renders `source/` into `build_*`.
- `bootstrap.php` registers two `afterBuild` listeners in `listeners/`: `GenerateSitemap` writes `sitemap.xml`, `GenerateIndex` writes `index.json` for the Fuse.js search in `source/_assets/js/components/Search.vue`.
- Pushing to `main` runs `.github/workflows/publish.yml`. It runs `bun run prod`, adds a CNAME and force-pushes `build_production/` to the `gh-pages` branch.

## Content model

- Posts live in `source/_posts/**` as Markdown with front matter: `extends: _layouts.post`, `section: content`, `title`, `date`, `description`, `excerpt`, `categories`, `published`, `featured`, `cover_image`.
- The local build shows every post. The production build filters the `posts` collection to `published: true`, so drafts in `source/_posts/drafts/` stay off the live site.
- Post URLs come from the title (`posts/{-title}`), so renaming a title changes its URL.
- Categories are `source/_categories/<name>.md`. A post's `categories` values must match those filenames.
- `<!-- more -->` in a post body sets the excerpt cut point. `config.php` also defines `getExcerpt`, `getReadTime` and `getDate` helpers and the CommonMark extensions.
- `$page->production` gates production-only markup, such as the steadyer.app beacon in `source/_layouts/main.blade.php`.

## Design

`DESIGN.md` is the design system and `PRODUCT.md` is the product brief. Read both before changing layout or styles.

- Colours are CSS variables in `source/_assets/css/base.css`. The `.dark` class on `<html>` swaps them. Tailwind colour names (`paper`, `ink`, `muted`, `faint`, `rule`, `hl`) map to those variables in `tailwind.config.js`.
- New page sections use the `<x-row>` component (`source/_components/row.blade.php`) so the rails and crosshairs stay continuous.
- The site uses no cards, shadows, gradients, rounded corners or second accent colour. Highlighter `#D9F24A` is for emphasis and hover only.
- Fonts are self-hosted in `source/assets/fonts/`: Schibsted Grotesk for text, FiraCode Nerd Font for labels and code.
- Code blocks use highlight.js with the Snazzy theme in `source/_assets/css/snazzy.css`.
- `PRODUCT.md` forbids inventing projects, testimonials or metrics.
