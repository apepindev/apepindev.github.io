---
extends: _layouts.post
section: content
title: "Setting a kill-switch before building a feature"
excerpt: "Visual diffing ships only if its false-positive rate stays at or under 5%. The benchmark test came before the feature."
date: 2026-11-12
description: "Visual diffing ships only if its false-positive rate stays at or under 5%. The benchmark test came before the feature."
featured: false
categories:
  - feature
published: false
share: true
---

<!-- DRAFT. Source: steadyer repo. Check every fact and add your own voice before publishing. -->

## The risk

Screenshot diffs are noisy. Fonts anti-alias differently, animations catch mid-frame, and ads rotate. A monitoring tool that cries wolf trains people to ignore it.

## The rule

Before building it, I wrote down the condition for cutting it: if the false-positive rate cannot be held at or under 5% cheaply, visual diffing does not ship.

## Making it testable

The worker repo has `tests/test_false_positive_benchmark.py`. It runs the diff over a corpus of page pairs that should match and fails if more than 5% get flagged. `diff.py` uses an anti-aliasing tolerant pixelmatch.

<!-- TODO: the real corpus is not built yet. Fill in the numbers once it is. -->

## What I learnt

A kill-switch written down in advance is easier to honour than one decided after weeks of work.
