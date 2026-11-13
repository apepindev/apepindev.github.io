---
extends: _layouts.post
section: content
title: "Versioned legal documents as Markdown files"
excerpt: "Privacy policy and terms live as dated Markdown files with front matter variables, frozen once published."
date: 2026-11-12
description: "Privacy policy and terms live as dated Markdown files with front matter variables, frozen once published."
featured: false
categories:
  - laravel
  - feature
published: false
share: true
---

<!-- DRAFT. Source: steadyer repo. Check every fact and add your own voice before publishing. -->

## The structure

Each document is a file at `resources/legal/<type>/<YYYY-MM-DD>.md`. Types: privacy policy, terms, refund policy, data processing agreement and sub-processors. They are served at `/legal/{type}` and `/legal/{type}/{version}`.

## Front matter

- `status`: `draft` or `published`.
- `effective_from` and `supersedes`.
- Any other key is a variable. `{{legal_name}}` in the body takes that key's value.

Variables live in each version's front matter, so a company name or registration number is frozen with that version.

## Safety rails

- A published version is never edited. A new file supersedes it, and the old one stays reachable by date.
- An empty variable stays visible as `{{token}}`, and a test fails if a published version contains one.
- A document with nothing published shows a placeholder, so no draft goes public.

## Acceptance

Signup records the accepted versions in `legal_acceptances`. A `legal` middleware holds a signed-in user on `/legal/accept` until they accept the versions in force.

<!-- TODO: mention unpublishing pages on 2026-11-06 while waiting for the ICO registration number. -->
