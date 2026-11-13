---
extends: _layouts.post
section: content
title: "Dotfiles part 3: an idempotent install script"
excerpt: "bin/install sets up a new Mac in six steps and does nothing harmful when run again."
date: 2026-11-13
description: "bin/install sets up a new Mac in six steps and does nothing harmful when run again."
featured: false
categories:
  - configuration
published: true
share: true
---

Part 3 of my [dotfiles series](/posts/dotfiles-part-1-a-public-core-with-private-machine-profiles).

When I get a new machine, all I need to do is run two commands:

```sh
git clone git@github.com:apepindev/dotfiles.git ~/.dotfiles
~/.dotfiles/bin/install && exec zsh
```

For me, this doesn't happen frequently, but when it does come to it, it makes setting it up a breeze.

My old `fresh.sh` script always assumed it was running on a fresh machine. Naively, it ran `rm -rf $HOME/.zshrc`, then
`mkdir $HOME/Code` with no `-p`. A second run stopped at the `mkdir`. Fixing one step meant running everything again by
hand. But _now_ the `~/.dotfiles/bin/install` can be run repeatedly after first install because...

## Six steps, each one checks first

1. oh-my-zsh, installed only when `~/.oh-my-zsh` is missing.
2. Homebrew, installed only when `brew` is not on PATH.
3. Symlinks, made with `ln -sfn`, which replaces an existing link in place.
4. Machine profile, read from `profiles/current` or asked for if not set.
5. Homebrew packages, from `config/Brewfile` and then the profile's `Brewfile`.
6. The profile's `install.sh` hook, when it exists and is executable.

Every step either skips or overwrites. Running the script twice gives the same result as running it once.

## Choosing a profile on a fresh machine

As mentioned on a previous post, profiles are private repos, so a new machine has none. The script lists any profile
folders it finds, but it will also accept a new name:

```
? Which machine profile? [personal work, or a new name]
```

When that folder is empty, the script asks for a git URL and clones the profile repo into it.

A blank answer scaffolds a new profile instead. The script creates `plugins.zsh`, `aliases.zsh`, `exports.zsh`,
`paths.zsh`, `functions.zsh`, a `Brewfile`, a `bin/` folder and an executable `install.sh`. It then runs `git init`, so
the profile only needs a private remote and a push. Existing profile are protected though and never overwritten.

## One hook per machine

The core links nothing machine-specific. Each profile can ship an `install.sh`, which runs last, after `brew bundle`, so
brew tools are available. `$DOTFILES` is exported for it. The hook has no rules: symlinks, extra clones, anything.

This replaced a separate `clone.sh` from my first attempt at managing my dotfiles that was called from `fresh.sh`.

## Output that stays clean in logs

Colours come from `tput`, and only when stdout is a terminal:

```sh
if [ -t 1 ]; then
  BOLD=$(tput bold); GREEN=$(tput setaf 2)
else
  BOLD=; GREEN=
fi
```

Piped to a file, the output has no escape codes.

## Reloading the shell

The script used to finish with `exec zsh`. The script runs as a child process, so that started a new zsh nested under
the script. Ghostty then warned that a process was still running when I closed the tab.

Now the script prints `exec zsh` as the next command. A `dotsync` function in `.functions` runs `bin/install && exec zsh`
from the interactive shell, which replaces that shell directly.

Next: [halving zsh startup time](/posts/dotfiles-part-4-halving-zsh-startup-time).
