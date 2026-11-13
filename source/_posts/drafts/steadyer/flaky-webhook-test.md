---
extends: _layouts.post
section: content
title: "A flaky webhook test, four fixes and a second queue worker"
excerpt: "A test that passed locally and failed in CI turned out to have a second consumer on its Redis queue."
date: 2026-11-12
description: "A test that passed locally and failed in CI turned out to have a second consumer on its Redis queue."
featured: false
categories:
  - laravel
  - php
published: false
share: true
---

<!-- DRAFT. Source: steadyer repo. Check every fact and add your own voice before publishing. -->

## The test

`DispatchWebhookJobTest` checks that a failing endpoint exhausting its retries does not stop a healthy endpoint's job for the same event. It pushes 2 jobs onto Redis and drains the queue with `queue:work`.

## The attempts

Between 2026-08-20 and 2026-08-22 it took 5 commits:

1. Fix a race condition in the test.
2. Wrap the scenario in a retry loop.
3. Raise the memory limit for the `queue:work` call.
4. Trace per-test memory in CI to diagnose a 128 MB threshold, then revert the tracing.
5. Remove the retry wrapper and keep the memory fix plus the real fix.

## The real cause

The test used the `webhooks` queue. Horizon's `supervisor-2` watches that queue in every environment that runs Horizon, including a local `docker compose up`. A second worker could pop either job before the test's own `queue:work` reached it. The delivery then stayed `Pending`, which looked like a failed assertion.

## The fix

Each test run uses a random queue name, `'webhooks-test-'.Str::uuid()`. No other worker can see it. A Guzzle handler routes responses by host, so the result no longer depends on which job sends its request first.

## What I learnt

A retry wrapper hides a flaky test. Find out what else touches the shared resource.

<!-- TODO: confirm the memory limit story from the CI logs. -->
