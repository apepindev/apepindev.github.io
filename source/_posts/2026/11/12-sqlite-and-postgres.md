---
extends: _layouts.post
section: content
title: "Running one Laravel test suite on SQLite and Postgres 18"
excerpt: "The first Postgres run of a suite that passed on SQLite found 9 failures. Here is what they were."
date: 2026-11-12
description: "The first Postgres run of a suite that passed on SQLite found 9 failures. Here is what they were."
featured: false
categories:
  - laravel php
published: false
share: true
---

<!-- DRAFT. Source: steadyer repo. Check every fact and add your own voice before publishing. -->

## Why two databases

Tests run on SQLite `:memory:` because it is fast and needs no service. Production runs Postgres 18. On 2026-10-10 I added a CI job that runs the same Pest suite on a `postgres:18-alpine` service.

## The guard

`tests/TestCase.php` refuses to run against Postgres unless `TESTS_ALLOW_POSTGRES=1` is set and the database name ends in `_ci`. A misconfigured run can never wipe the local dev database.

## What broke

The first run found 9 failures:

- Tests that caught a constraint violation. Postgres aborts the whole transaction after an error, while SQLite carries on. `RefreshDatabase` wraps each test in a transaction, so every query after the violation failed. Wrapping the failing statement in `DB::transaction()` creates a savepoint and fixes it.
- Fixtures that used non-UUID strings in UUID columns. SQLite stores anything.
- A boolean assertion. SQLite returns `1`, Postgres returns `true`.
- A missing column default in one action.
- Two tests that simulate race conditions. They now skip on Postgres.

## What I learnt

SQLite is a permissive database. A suite that only runs there tests less than it appears to.

<!-- TODO: link the sharding post. Add the CI job YAML snippet. -->
