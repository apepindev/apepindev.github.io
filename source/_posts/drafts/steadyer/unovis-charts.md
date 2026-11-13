---
extends: _layouts.post
section: content
title: "Replacing hand-rolled SVG charts with Unovis"
excerpt: "Swapping custom inline SVG charts for Unovis through shadcn-vue's chart container."
date: 2026-11-12
description: "Swapping custom inline SVG charts for Unovis through shadcn-vue's chart container."
featured: false
categories:
  - feature
published: false
share: true
---

<!-- DRAFT. Source: steadyer repo. Check every fact and add your own voice before publishing. -->

## The problem

<!-- TODO: what went wrong with the hand-rolled charts: tooltips, dark mode, axes, accessibility? -->

## The swap

On 2026-10-01 the charts moved to Unovis (`@unovis/vue`) through shadcn-vue's vendored `ChartContainer`. The change touched 50 files.

## Why go through ChartContainer

`ChartContainer` sets the CSS variables for tooltips and the crosshair, as well as the colours. Every chart, including a small inline sparkline, uses it, so all charts theme the same in light and dark mode.

## Colour rules

The design system allows saturated colour only for state: green healthy, amber watch, red regressed. Chart marks take `var(--color-<key>)` from a `ChartConfig`. Status series use status tokens, and everything else stays neutral.

## The pattern

Compose `ChartContainer :config` with `Vis*` marks such as `VisStackedBar` or `VisLine`, plus `VisAxis`.

<!-- TODO: add a code snippet from TrendSparkline.vue. -->
