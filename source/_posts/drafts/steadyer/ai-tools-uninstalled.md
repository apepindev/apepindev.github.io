---
extends: _layouts.post
section: content
title: "Two AI coding tools I uninstalled"
excerpt: "OpenWolf crashed on macOS's file limit and beads took over my git hooks. Serena stayed."
date: 2026-11-12
description: "OpenWolf crashed on macOS's file limit and beads took over my git hooks. Serena stayed."
featured: false
categories:
  - personal
  - configuration
published: false
share: true
---

<!-- DRAFT. Source: steadyer repo. Check every fact and add your own voice before publishing. -->

## What I wanted

Persistent memory for coding agents and less context spent reading files.

## OpenWolf

OpenWolf runs a daemon, a dashboard and a file watcher. The watcher ignored its own `exclude_patterns` and `respect_gitignore` settings. It watched Laravel's `vendor/` directory, about 26,000 files, and crashed with `EMFILE` on macOS's 256 file descriptor limit. `openwolf dashboard` also started servers that died soon after.

## beads

beads (`bd`) auto-committed its tracking files. It also set `git config core.hooksPath` to its own hooks, which disabled the project's `.githooks` pre-commit hook that runs pint and eslint.

## Removal

On 2026-09-22 both went: the `.wolf/` directory, their Claude Code hooks and commands, the global npm package, and `core.hooksPath` restored to `.githooks`.

## What stayed

Serena, an MCP server that gives agents symbol-level code lookup. It has no daemon and no watcher, and it indexes PHP, TypeScript and Vue.

## What I learnt

Prefer tools that keep state in files and run only when called.
