---
extends: _layouts.post
section: content
title: "Adding visual diffs without touching the dashboard"
excerpt: "A screenshot diff became one more signal on an existing regression, so dashboards, reports and webhooks needed no changes."
date: 2026-11-12
description: "A screenshot diff became one more signal on an existing regression, so dashboards, reports and webhooks needed no changes."
featured: false
categories:
  - feature laravel
published: false
share: true
---

<!-- DRAFT. Source: steadyer repo. Check every fact and add your own voice before publishing. -->

## The feature

A separate Python worker (FastAPI and Playwright) captures screenshots of a page and diffs them against a baseline from the previous release. It posts results back to the Laravel app with a signed callback.

## The design decision

Steadyer already models a problem as a `Regression` with signals (error rate, load time). A visual diff became one more `RegressionSignal` with `type=visual`. It fires the existing `RegressionDetected` event.

The dashboard, deep view, client reports and webhooks already render regressions. They picked up visual diffs with zero changes.

## The plumbing

- `DispatchBrowserCapture` posts to the worker with a baseline from the current release.
- The worker calls back to `/internal/browser-results`, checked by bearer token plus HMAC.
- `RecordVisualSnapshot` stores the result. `EvaluateVisualDiff` opens or extends a regression.
- A Pennant flag, `browser-capture`, keeps it off by default.

## Still open

Visual detection and rollup detection can each open a regression for the same release. They need to converge on one.

<!-- TODO: this feature is behind a flag. Do not describe it as available in Steadyer. -->
