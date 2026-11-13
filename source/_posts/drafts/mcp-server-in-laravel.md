---
extends: _layouts.post
section: content
title: "Building an MCP server inside a Laravel monolith"
excerpt: "A plan for a separate Python MCP server became three read-only tools on laravel/mcp, sharing the API's auth."
date: 2026-11-12
description: "A plan for a separate Python MCP server became three read-only tools on laravel/mcp, sharing the API's auth."
featured: false
categories:
  - laravel
  - feature
published: false
share: true
---

<!-- DRAFT. Source: steadyer repo. Check every fact and add your own voice before publishing. -->

## The original plan

The architecture doc planned a separate Python service for the MCP server. That meant a second deploy, a second auth story and a copy of the data access rules.

## What I built instead

On 2026-10-06 I moved it into the monolith on `laravel/mcp`:

- `/mcp` lives in `routes/ai.php`.
- Three read-only tools: `list_apps`, `list_regressions` and `get_regression`.
- It shares the `/api/v1` gate: the same tier and the same `read` token ability.
- An `mcp` rate limiter allows 120 requests a minute per token.

## Same actions, three front doors

The tools call the same actions as the web and API controllers (`ListRegressions`, `GetRegressionDetail`). Tenancy and authorization come for free.

## Pinning the output format

The homepage shows a sample of what `get_regression` returns. A golden test reads that snippet from `Welcome.vue` and checks the tool can produce every line. The marketing copy cannot drift from the real output.

<!-- TODO: add a real session with an MCP client once you have one. -->
