---
extends: _layouts.post
section: content
title: "Sharding a Postgres CI job with Pest"
excerpt: "Splitting the Postgres test job across 3 GitHub Actions runners with 4 processes each, balanced by time."
date: 2026-11-12
description: "Splitting the Postgres test job across 3 GitHub Actions runners with 4 processes each, balanced by time."
featured: false
categories:
  - laravel configuration
published: false
share: true
---

<!-- DRAFT. Source: steadyer repo. Check every fact and add your own voice before publishing. -->

## The problem

The Postgres CI job ran the whole suite on one runner. It was the slowest job in the workflow.

<!-- TODO: add before and after timings from GitHub Actions. -->

## The setup

- A `matrix.shard: [1, 2, 3]` in `.github/workflows/tests.yml`.
- Each shard runs `vendor/bin/pest --shard=N/3 --parallel` with 4 processes against its own `postgres:18-alpine` service.
- Shards are time-balanced from `tests/.pest/shards.json`, which Pest writes with `vendor/bin/pest --parallel --update-shards`.

## Why time-balanced

Splitting by file count put the slow feature tests on one shard. Pest records how long each test file takes, so the shards finish at about the same time.

## The cache step

The same change cached Composer's `vendor` directory and bun's install cache (`~/.bun/install/cache`, keyed on `bun.lock`). Each shard installs dependencies, so caching saves that time three times over.

## What I learnt

Refresh `shards.json` when the suite grows. A stale file still works, but the balance drifts.
