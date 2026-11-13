---
extends: _layouts.post
section: content
title: "A cached config file made my whole test suite fail with 419"
excerpt: "php artisan config:cache outranks phpunit.xml, so tests silently ran on Postgres with CSRF switched on."
date: 2026-11-12
description: "php artisan config:cache outranks phpunit.xml, so tests silently ran on Postgres with CSRF switched on."
featured: false
categories:
  - laravel
  - configuration
published: false
share: true
---

<!-- DRAFT. Source: steadyer repo. Check every fact and add your own voice before publishing. -->

## The symptom

Every POST test in the suite failed with a 419. The tests looked broken. Nothing in the tests had changed.

## The cause

`php artisan config:cache` writes `bootstrap/cache/config.php`. Once that file exists, Laravel reads config from it and ignores every runtime environment variable. That includes the ones `phpunit.xml` sets.

Two things went wrong at once:

- `DB_CONNECTION=sqlite` and `DB_DATABASE=:memory:` were ignored, so the tests ran against the local Postgres database.
- The cache pinned `app.env=local`, so `Application::runningUnitTests()` returned false. `ValidateCsrfToken` stayed armed, and every POST returned 419.

## Where the cache came from

The serversideup PHP Docker image runs config, route, view and event caching on every container start by default. The project bind-mounts the code, so the cache landed in my working copy.

## The fix

- Set `AUTORUN_LARAVEL_CONFIG_CACHE`, `ROUTE_CACHE`, `VIEW_CACHE`, `EVENT_CACHE` and `OPTIMIZE` to `'false'` in `compose.yaml`.
- Exclude `bootstrap/cache/*.php` in `.dockerignore`. Docker ignores `.gitignore`, so the checked-in `bootstrap/cache/.gitignore` never covered image builds.
- Run `php artisan optimize:clear` if `config.php` reappears.

## What I learnt

Only `packages.php` and `services.php` belong in `bootstrap/cache/` during development. Anything else there is a bug waiting to happen.

<!-- TODO: add how long it took to spot, and the first wrong theory. -->
