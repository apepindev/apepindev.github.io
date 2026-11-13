---
extends: _layouts.post
section: content
title: "Multi-tenancy in Laravel with global scopes and parent keys"
excerpt: "Tenant models carry a global scope. Child models reach the tenant through their parent, and that is where leaks hide."
date: 2026-11-12
description: "Tenant models carry a global scope. Child models reach the tenant through their parent, and that is where leaks hide."
featured: false
categories:
  - laravel
  - php
published: false
share: true
---

<!-- DRAFT. Source: steadyer repo. Check every fact and add your own voice before publishing. -->

## Two kinds of model

Models with their own `workspace_id` (`App`, `Report`, `WebhookEndpoint`) use a `BelongsToWorkspace` trait. It adds a global scope:

```php
static::addGlobalScope('workspace', function (Builder $builder): void {
    $workspaceId = app(CurrentWorkspace::class)->resolve();

    if ($workspaceId === null && (app()->runningInConsole() || app()->runningUnitTests())) {
        return;
    }

    $builder->where($builder->qualifyColumn('workspace_id'), $workspaceId);
});
```

A real request that resolves no workspace stays scoped to nothing, so it returns no rows.

Child models (`Check`, `Release`, `Event`, `Regression`) have no `workspace_id`. They reach the tenant through a parent foreign key such as `app_id`.

## Where leaks hide

`Regression::find($id)` has no scope. Nothing stops it returning another tenant's row. The rule: query children through their parent, or authorize the parent before a lookup by id.

## Enforcing it

- Every test that touches tenant data includes a cross-tenant isolation assertion.
- Every tenant table has a cascading `workspace_id` or `app_id` foreign key, so deleting a workspace removes everything.

<!-- TODO: add an example isolation test. -->
