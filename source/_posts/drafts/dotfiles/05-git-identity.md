---
extends: _layouts.post
section: content
title: "Dotfiles part 5: per-machine git identity"
excerpt: "A shared gitconfig includes ~/.gitconfig.local, so each machine sets its own email and signing key."
date: 2026-11-13
description: "A shared gitconfig includes ~/.gitconfig.local, so each machine sets its own email and signing key."
featured: false
categories:
  - configuration
published: false
share: true
---

<!-- DRAFT. Source: ~/.dotfiles config/git/gitconfig. Check every fact and add your own voice before publishing. -->

Part 5 of the [dotfiles series](/posts/dotfiles-part-1-a-public-core-with-private-machine-profiles).

## The problem

My personal and work machines commit with different email addresses and different GPG keys. The shared `gitconfig` had one hardcoded email and one `signingkey`. Work commits went out under the wrong identity.

## The include

The shared `config/git/gitconfig` is symlinked to `~/.gitconfig`. It sets everything common: `delta` as the pager, `zdiff3` conflicts, `main` as the default branch, and `gpgsign = true`. Its last block is:

```ini
[include]
	path = ~/.gitconfig.local
```

git reads an included file at the point of the include. Being last, `~/.gitconfig.local` overrides anything above it. When the file is missing, git ignores the include without an error.

## Where the local file comes from

Each profile repo holds its own `gitconfig.local` with `email` and `signingkey`. The profile's `install.sh` symlinks it to `~/.gitconfig.local`. The email and key ID never enter the public repo.

## The global gitignore

The old config set `core.excludesfile` to an absolute path, `/Users/andrew/.dotfiles/...`. That broke on any machine with a different username. Now `bin/install` symlinks `config/git/gitignore_global` to `~/.gitignore_global`.

<!-- TODO: check how excludesfile is set today. The current gitconfig has no core.excludesfile line, and git's default is ~/.config/git/ignore. -->
