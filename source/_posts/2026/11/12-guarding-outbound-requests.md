---
extends: _layouts.post
section: content
title: "Guarding outbound requests to customer URLs in Laravel"
excerpt: "Uptime checks and webhooks fetch URLs that customers type. A Guzzle middleware blocks private addresses and pins DNS."
date: 2026-11-12
description: "Uptime checks and webhooks fetch URLs that customers type. A Guzzle middleware blocks private addresses and pins DNS."
featured: false
categories:
  - laravel php
published: false
share: true
---

<!-- DRAFT. Source: steadyer repo. Check every fact and add your own voice before publishing. -->

## The risk

Uptime checks and webhook deliveries fetch URLs a tenant typed. Without a guard, a tenant could point one at `127.0.0.1`, a private network address or a cloud metadata endpoint (`169.254.169.254`).

## Validating is not enough

A form rule that checks the hostname runs once. DNS can return a public address at validation time and a private one at request time. Redirects can also land on a private address.

## The middleware

`GuardedOutboundRequest` is Guzzle middleware:

1. `PublicUrlGuard::assertPublic()` resolves the host and refuses private, loopback and link-local addresses.
2. The approved IPs are pinned with `CURLOPT_RESOLVE`, so curl connects to exactly those addresses.
3. It sits inside Guzzle's redirect middleware, so every redirect hop is checked and pinned.
4. A blocked request throws `ConnectException`. Laravel reports it as a `ConnectionException`, and the webhook package records a failed attempt.

The `PublicHttpUrl` validation rule still runs on input, for a fast error message.

## Testing offline

A fake `HostResolver` resolves every hostname to one public IP in tests. `fakeHostResolver(['host' => ['10.0.0.1']])` simulates a private target.

<!-- TODO: add the Slack webhook egress guard from 2026-10-31. -->
