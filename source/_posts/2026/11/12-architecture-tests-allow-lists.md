---
extends: _layouts.post
section: content
title: "Architecture tests with allow-lists that only shrink"
excerpt: "Pest arch() tests keep domain code away from controllers. Existing violations sit in a list that may only get shorter."
date: 2026-11-12
description: "Pest arch() tests keep domain code away from controllers. Existing violations sit in a list that may only get shorter."
featured: false
categories:
  - laravel
  - php
published: false
share: true
---

<!-- DRAFT. Source: steadyer repo. Check every fact and add your own voice before publishing. -->

## The rule

The app is a modular monolith under `app/Domains/`. Each domain has a core (`Actions`, `Models`, `Jobs`) and delivery layers (`Http`, `Console`). The core must never import a delivery layer, Filament or the MCP server. Controllers must never query models directly.

## The test

`tests/Unit/ArchitectureTest.php` globs `app/Domains/*/*` and writes one `arch()` per namespace:

```php
arch("{$namespace} never depends on a delivery layer")
    ->expect($namespace)
    ->not->toUse(['App\Http', 'App\Filament', 'App\Mcp', ...$domainHttpLayers]);
```

## A Pest gotcha

One `arch()` per namespace matters. `not->toUse()` given an array of *expected* namespaces passes as soon as any one is clean, so a single combined expectation misses violations.

## The allow-list

The rule arrived after code existed that broke it. Those violations live in an allow-list. The rule for the list: shrink it as code moves, never add to it. New code meets the rule from day one, and old code gets fixed over time.

<!-- TODO: how many entries did the list start with, and how many are left? -->
