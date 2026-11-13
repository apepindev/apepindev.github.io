---
extends: _layouts.post
section: content
title: "Building a cookieless analytics beacon"
excerpt: "A JavaScript beacon that writes no cookies or storage and never keeps IP addresses or user agents."
date: 2026-11-12
description: "A JavaScript beacon that writes no cookies or storage and never keeps IP addresses or user agents."
featured: false
categories:
  - feature
published: false
share: true
---

<!-- DRAFT. Source: steadyer repo. Check every fact and add your own voice before publishing. -->

## The constraints

The beacon runs on customers' sites, so its privacy position becomes theirs. The rules:

- No cookies, `localStorage` or `sessionStorage`.
- `/ingest` stores no IP address and no user agent. The IP only feeds the rate-limit key.
- The raw page path is used to match a configured path, then discarded.

## Redacting error text

JavaScript errors carry user data. Error messages pass through `redact.ts` in the beacon and `ErrorTextRedactor` on the server. Both strip email addresses and URL query strings.

## Unmatched paths

Behind a feature flag, unmatched visits keep a cleaned pattern count. `cleanPath.ts` turns `/orders/8421` into a pattern before it leaves the browser. The server cleans it again with `PagePathCleaner`.

## The ingest path

`POST /ingest` validates, queues and returns `202`. It lives in its own routes file with its own rate limit.

## Why it matters legally

<!-- TODO: explain PECR and why no device storage means no consent banner. Check wording with your legal doc. -->

Any change to what the beacon collects changes the privacy policy, so the two are updated together.
