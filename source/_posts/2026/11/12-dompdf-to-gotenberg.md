---
extends: _layouts.post
section: content
title: "Replacing dompdf with Gotenberg for PDF reports"
excerpt: "Client reports needed modern CSS. Gotenberg renders HTML in headless Chromium, with no URL fetching."
date: 2026-11-12
description: "Client reports needed modern CSS. Gotenberg renders HTML in headless Chromium, with no URL fetching."
featured: false
categories:
  - laravel
  - php
published: false
share: true
---

<!-- DRAFT. Source: steadyer repo. Check every fact and add your own voice before publishing. -->

## Why dompdf went

<!-- TODO: list the CSS that dompdf could not render (flex, grid, custom properties, web fonts?). -->

The swap landed on 2026-09-05 and touched 32 files.

## How it works now

- Reports render through Blade, so the in-app preview, the email and the PDF share one template.
- `RenderReportPdf` posts the HTML to a Gotenberg sidecar (`gotenberg/gotenberg:8`) on its Chromium HTML route.
- `RenderReportPdfJob` runs it on the queue. The preview page polls for status and downloads the file when it is ready.
- The client has a 5 second connect timeout and a 45 second total timeout.
- The file lands on a private `reports` disk and streams through the app.

## Closing the SSRF hole

A headless browser fetches any URL in the HTML it renders. White-label logos and fonts are inlined as data URIs, so Gotenberg never fetches an app URL. Only the HTML route is used. Gotenberg has no authentication, so it stays on the private Docker network.

## Testing without Chromium

CI has no Gotenberg sidecar. A global `beforeEach` in `tests/Pest.php` binds a fake PSR-18 client that returns canned PDF bytes. `fakeGotenbergPdf($history)` captures the outgoing request for assertions.
