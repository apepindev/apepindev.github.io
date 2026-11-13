---
extends: _layouts.post
section: content
title: "Dotfiles part 1: a public core with private machine profiles"
excerpt: "My dotfiles split into a public repo for shared shell config and one private repo per machine."
date: 2026-11-13
description: "My dotfiles split into a public repo for shared shell config and one private repo per machine."
featured: true
categories:
  - configuration
published: true
share: true
---

This is the first post in a series about my [dotfiles](https://github.com/apepindev/dotfiles) and how I manage profiles
between separate computers.

1. A public core with private machine profiles (you are here)
2. [Loading machine profiles in zsh](/posts/dotfiles-part-2-loading-machine-profiles-in-zsh)
3. [An idempotent install script](/posts/dotfiles-part-3-an-idempotent-install-script)
4. [Halving zsh startup time](/posts/dotfiles-part-4-halving-zsh-startup-time)
5. [Per-machine git identity](/posts/dotfiles-part-5-per-machine-git-identity)

## Where it started

For a long while I neglected my personal development environments until it got to the point where it was difficult to
get full visibility of it. After the millionth time of doing some spring-cleaning I decided to settle on an environment
and create a repo for it. It held a `.zshrc`, a 1,579 line `.p10k.zsh` for the Powerlevel10k prompt,
one `Brewfile` and a `fresh.sh` setup script. It was just a dump of what I was using at the time really.

There was a `fresh.sh` script that ran top to bottom once. It deleted `~/.zshrc`, symlinked the repo copy, ran
`brew bundle` and cloned _some_ repos, but this wasn't something that can run idempotently. That lead to falling into
bad habits again and eventually a neglected dotfiles repo very quickly.

## Two machines, one repo

The issue was mainly caused by the fact I'd have separate machines for work and personal use. By early 2026 the repo had
a `personal.Brewfile` and a `work.Brewfile` side by side. Work aliases and paths sat in the same public files as
everything else, which you can imagine was awkward to maintain.

## The overhaul

In July 2026 I came across a post, [Freek Van der Herten's dotfiles](https://github.com/freekmurze/dotfiles) and his [tour of them](https://freek.dev/3054-a-tour-of-my-dotfiles), and I was
inspired to follow in his footsteps. It's almost identical really apart from a small, but game changing detail:
**profiles**. 

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

The public repo holds config that is true on every machine. Common tool configs across machines, useful aliases and
functions, and a single `.zshrc`. However, Each folder under `profiles/` is a separate private git
repo that extends from this core environment.

An example profile would look something like this:

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

Not all profiles will need to extend (or even override) everything in core. Its purpose is just an extension. For
example, I have aliases for K8s that I use for work, but I don't need or use K8s on my personal machine (not yet anyway),
so they're not in core or my personal profile.

Having this clear separation really helped me get more visibility on my environment.

## Symlinks

It's a common practice, but `bin/install` symlinks `home/.zshrc` to `~/.zshrc` and each app config into `~/.config`.
That way any changes I need to make to my environment is done within the dotfiles repo, which immediately edits the
running config. Just needs a shell reload to pick up the change. Simple.

Next: [how `.zshrc` loads the active profile](/posts/dotfiles-part-2-loading-machine-profiles-in-zsh).
