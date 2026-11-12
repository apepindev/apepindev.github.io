---
name: apepin.dev drafting sheet
colors:
  paper: "#FFFFFF"
  margin: "#F4F5F7"
  ink: "#111418"
  muted: "#374151"
  faint: "#4B5563"
  rule: "#A3ABB6"
  rule-soft: "#D6DAE0"
  reg: "#6B7380"
  hatch: "#C3C9D1"
  highlighter: "#D9F24A"
  code-bg: "#282A36"
  code-ink: "#EFF0EB"
  dark-paper: "#121417"
  dark-margin: "#0B0C0E"
  dark-ink: "#E9ECEF"
  dark-muted: "#BAC1CB"
  dark-rule: "#3D444E"
  dark-rule-soft: "#262B32"
typography:
  display:
    fontFamily: Schibsted Grotesk
    fontWeight: 800
    letterSpacing: -0.04em
    lineHeight: 0.98
  heading:
    fontFamily: Schibsted Grotesk
    fontWeight: 700
    letterSpacing: -0.02em
  body:
    fontFamily: Schibsted Grotesk
    fontSize: 17px
    lineHeight: 1.75
  label:
    fontFamily: FiraCode Nerd Font
    fontSize: 11px
    letterSpacing: 0.06em
  code:
    fontFamily: FiraCode Nerd Font
    fontSize: 13px
    lineHeight: 1.8
rounded:
  none: 0px
spacing:
  sheet-gutter: 64px
  sheet-gutter-mobile: 20px
  sheet-max: 1200px
components:
  icon-button:
    backgroundColor: "{colors.paper}"
    textColor: "{colors.ink}"
    size: 44px
    rounded: "{rounded.none}"
  icon-button-hover:
    backgroundColor: "{colors.highlighter}"
    textColor: "{colors.ink}"
  tag:
    textColor: "{colors.ink}"
    typography: "{typography.label}"
    padding: 6px 9px
---

# Design System: apepin.dev

## Overview

The site is drawn as a technical drawing sheet. A 1200px white sheet sits between two 1px rails on a dotted margin. Every section is a full-width row rule, and small crosshair marks sit where a rule meets a rail. Hatched bands separate the header and footer from the content. The footer is an engineering title block.

Source of the direction: prototype A (layout) with prototype C (colour and type) from the design canvas made on 2026-11-12. The user pinned a blueprint or wireframe look inspired by laravel.com and tailwindcss.com, without copying either.

## Colors

All colours are CSS variables in `source/_assets/css/base.css`. The `.dark` class on `<html>` swaps the same names, so components never name a hex value.

### Primary
- Ink `#111418` carries text only. Rails and row rules use rule `#A3ABB6`, crosshairs use reg `#6B7380`, hatch stripes use hatch `#C3C9D1`, so text stays the darkest mark on the sheet.

### Secondary
- Highlighter `#D9F24A` marks emphasis and hover. It is drawn as a marker band behind the lower part of the text (`.hl`, `.hl-hover`). In dark mode the band drops to the bottom 14% of the line so light text keeps its contrast. It fills icon buttons and tags on hover.

### Neutral
- Paper `#FFFFFF` for the sheet, margin `#F4F5F7` with a 16px dot grid outside it.
- Muted `#374151` for body copy, faint `#4B5563` for labels and dates.
- Rule-soft `#D6DAE0` for dashed secondary dividers between rows.

### Named Rules
- Solid lines are structure. Dashed lines are secondary: row dividers, tags, buttons, frames.
- No other accent colours. Categories all share one tag style, whatever `color` their Markdown file sets.

## Typography

Schibsted Grotesk for all reading text and headings. FiraCode Nerd Font for labels, dates, read times, tags, code and the `// apepin.dev` mark. Mono never decorates prose.

### Hierarchy
- Display: page titles at `clamp(2.5rem, 10vw, 5rem)`, home greeting up to 6rem, weight 800, tracking -0.04em.
- Section heading: 2rem, weight 800.
- Schedule title: 1.375rem, weight 700.
- Body: 17px, line height 1.75, measure 68ch in `.prose`.
- Label: 11px FiraCode Nerd Font, uppercase, tracking 0.06em, faint colour.

## Layout

- `<x-row>` (`source/_components/row.blade.php`) renders one full-width rule and the sheet inside it. Pass `:first="true"` to drop the rule and crosshairs.
- Horizontal padding inside the sheet is `px-16 px-sheet` (64px, 20px under 860px).
- Post lists use the schedule: `_components/schedule-head` then `_components/post-row` per post (date, title, description, read time, category tags, arrow).
- Post pages use a three-column `.article-grid`: details on the left, prose in the middle, an "On this page" list on the right built from h2 headings by script.

## Elevation & Depth

None. The sheet is flat. Depth comes from rules, dashed frames and the dotted margin. No shadows, no rounded corners.

## Components

- Icon button (`.icon-btn`): 44px square, dashed ink border, fills with highlighter on hover. Used for search, theme switch, menu and pagination.
- Tag (`.tag`): dashed border, 11px mono text.
- Frame (`.frame` + `.dimension`): dashed box with a measured dimension line above and a `FIG. n` caption below (`.fig-caption`).
- Code block: dark panel in both themes with a 1px border, FiraCode Nerd Font, Snazzy colours (`source/_assets/css/snazzy.css`) on #282A36.
- Title block footer (`.title-block`): 4 by 2 grid of label and value cells.

## Do's and Don'ts

- Do add new sections as `<x-row>` blocks so the rails and crosshairs stay continuous.
- Do keep the highlighter for emphasis and hover only.
- Don't add cards, shadows, gradients or rounded corners.
- Don't add a second accent colour or colour-coded categories.
- Don't use mono for paragraphs or headings.
