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
published: true
share: true
---

Part 5 of my [dotfiles series](/posts/dotfiles-part-1-a-public-core-with-private-machine-profiles).

## The problem

My personal and work machines commit with different email addresses and different GPG keys. Having a shared `gitconfig`
with hardcoded email and `signingkey` caused work commits going out under the wrong identity.

## The include

The shared `config/git/gitconfig` is symlinked to `~/.gitconfig`. It sets everything common: `delta` as the pager,
`zdiff3` conflicts, `main` as the default branch, and `gpgsign = true`. Its last block is:

```ini
[include]
path = ~/.gitconfig.local
```

git reads an included file at the point of the include. Being last, `~/.gitconfig.local` overrides anything above it.
When the file is missing, git ignores the include without an error.

## Where the local file comes from

Each profile repo holds its own `gitconfig.local` with `email` and `signingkey`. The profile's `install.sh` symlinks it
to `~/.gitconfig.local`. The email and key ID never enter the public repo.

## The global gitignore

The old config set `core.excludesfile` to an absolute path, `/Users/andrew/.dotfiles/...`. That broke on any machine
with a different username.

With `core.excludesfile` unset, git reads `~/.config/git/ignore` as the global ignore file. So the gitconfig sets
nothing, and `bin/install` symlinks the repo file to that default path:

```sh
ln -sfn "$DOTFILES/config/git/gitignore_global" "$HOME/.config/git/ignore"
```

The path has no username in it, and the gitconfig needs no extra line.

For a while the symlink pointed at `~/.gitignore_global` instead. Git never read that file, because nothing in the
config named it.
