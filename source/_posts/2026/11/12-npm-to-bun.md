---
extends: _layouts.post
section: content
title: "Why I moved a Laravel project from npm to bun"
excerpt: "npm's arborist crashed on pinned platform optionalDependencies in CI. bun installed them without complaint."
date: 2026-11-12
description: "npm's arborist crashed on pinned platform optionalDependencies in CI. bun installed them without complaint."
featured: false
categories:
  - configuration
published: false
share: true
---

<!-- DRAFT. Source: steadyer repo. Check every fact and add your own voice before publishing. -->

## The error

CI failed during `npm install` with:

```
Cannot read properties of null (reading 'edgesOut')
```

The project pins platform-specific `optionalDependencies` (native binaries for tools like the Rollup and lint resolvers). npm's arborist, its dependency tree builder, choked on them.

<!-- TODO: name the exact packages and npm version. -->

## The switch

On 2026-09-05 I replaced npm with bun in the package scripts and in CI:

- CI gets bun from the `oven-sh/setup-bun` action.
- The local Docker image copies a pinned bun binary with `COPY --from=oven/bun` in its `local` stage.
- The production stage has no bun. It only needs compiled assets.

`composer setup` and `composer ci:check` now run `bun install` and `bun run …`.

## What I learnt

npm was not installed anywhere else in the stack, so nothing tied the project to it. Switching the package manager was a 3-file change.
