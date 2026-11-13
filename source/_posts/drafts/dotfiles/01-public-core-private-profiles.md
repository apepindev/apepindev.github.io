---
extends: _layouts.post
section: content
title: "Dotfiles part 1: a public core with private machine profiles"
excerpt: "My dotfiles split into a public repo for shared shell config and one private repo per machine."
date: 2026-11-13
description: "My dotfiles split into a public repo for shared shell config and one private repo per machine."
featured: false
categories:
  - configuration
published: false
share: true
---

<!-- DRAFT. Source: ~/.dotfiles git history. Check every fact and add your own voice before publishing. -->

This is the first post in a series about my dotfiles.

1. A public core with private machine profiles (this post)
2. [Loading machine profiles in zsh](/posts/dotfiles-part-2-loading-machine-profiles-in-zsh)
3. [An idempotent install script](/posts/dotfiles-part-3-an-idempotent-install-script)
4. [Halving zsh startup time](/posts/dotfiles-part-4-halving-zsh-startup-time)
5. [Per-machine git identity](/posts/dotfiles-part-5-per-machine-git-identity)

## Where it started

The first commit landed on 17 November 2024. It held a `.zshrc`, a 1,579 line `.p10k.zsh` for the Powerlevel10k prompt, one `Brewfile` and a `fresh.sh` setup script.

`fresh.sh` ran top to bottom once. It deleted `~/.zshrc`, symlinked the repo copy, ran `brew bundle` and created `~/Code/apepindev`. Running it a second time failed on `mkdir`, because the directory already existed.

## Two machines, one repo

I use a personal Mac and a work Mac. By March 2026 the repo had `personal.Brewfile` and `work.Brewfile` side by side. Work aliases and paths sat in the same public files as everything else.

That month I replaced Powerlevel10k with [starship](https://starship.rs) and added a [Ghostty](https://ghostty.org) config. Deleting `.p10k.zsh` removed 1,579 lines in one commit.

## The overhaul

In July 2026 I rebuilt the repo after reading [Freek Van der Herten's dotfiles](https://github.com/freekmurze/dotfiles) and his [tour of them](https://freek.dev/3054-a-tour-of-my-dotfiles). The overhaul commit touched 27 files: 500 lines added, 585 removed.

The new layout:

```
~/.dotfiles/
  bin/install              # bootstrap, safe to re-run
  home/
    .zshrc
    .exports
    .aliases
    .functions
  config/
    Brewfile               # packages for every machine
    ghostty/config
    starship/starship.toml
    zed/settings.json
    git/gitconfig
    git/gitignore_global
    oh-my-zsh/             # shared ZSH_CUSTOM
  profiles/                # contents gitignored
    current                # "personal" or "work"
    personal/              # its own private repo
    work/                  # its own private repo
```

## The split

The public repo holds config that is true on every machine. Each folder under `profiles/` is a separate private git repo. The public repo's `.gitignore` excludes everything in `profiles/` except a `.gitkeep`.

A profile mirrors the core layout:

```
profiles/<profile>/
  plugins.zsh    # plugins+=(...)
  aliases.zsh
  exports.zsh
  paths.zsh
  functions.zsh
  Brewfile       # packages beyond config/Brewfile
  bin/           # added to PATH
  install.sh     # optional setup hook
```

Work aliases, client paths and work-only packages now live in a repo nobody else can read. The public repo stays safe to share.

## Symlinks

`bin/install` symlinks `home/.zshrc` to `~/.zshrc` and each app config into `~/.config`. Editing a file in the repo edits the live config. A shell reload picks up the change.

Next: [how `.zshrc` loads the active profile](/posts/dotfiles-part-2-loading-machine-profiles-in-zsh).
