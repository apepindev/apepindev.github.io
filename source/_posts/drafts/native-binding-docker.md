---
extends: _layouts.post
section: content
title: "Cannot find native binding: sharing node_modules between macOS and Docker"
excerpt: "A bind mount shares node_modules between host and container. Native binaries built for macOS do not run on Linux."
date: 2026-11-12
description: "A bind mount shares node_modules between host and container. Native binaries built for macOS do not run on Linux."
featured: false
categories:
  - configuration
published: false
share: true
---

<!-- DRAFT. Source: steadyer repo. Check every fact and add your own voice before publishing. -->

## The symptom

Inside the Docker container, eslint failed with `Cannot find native binding` from `unrs-resolver`. On the host it worked.

## The cause

The app container bind-mounts the project directory, `node_modules` included. When `bun install` ran on macOS, it fetched the darwin-arm64 binary for `unrs-resolver`. The Linux container has no matching binary, so eslint cannot load it.

## The workaround

Split the CI gate by where it runs:

- JavaScript checks on the host: `bun run lint:check && bun run format:check && bun run types:check`.
- PHP checks in the container: `docker compose exec app composer test`, then `composer typescript:check`.

The committed pre-commit hook (`.githooks/pre-commit`) also runs on the host, since both bun and pint are installed there.

<!-- TODO: decide whether to describe a named volume for node_modules as the proper fix. -->

## What I learnt

A bind mount shares files, and native binaries are files. Any package with a platform binary breaks when two operating systems share one `node_modules`.
