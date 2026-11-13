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
published: false
share: true
---

<!-- DRAFT. Source: ~/.dotfiles bin/install. Check every fact and add your own voice before publishing. -->

Part 3 of the [dotfiles series](/posts/dotfiles-part-1-a-public-core-with-private-machine-profiles).

A new machine needs two commands:

```sh
git clone git@github.com:apepindev/dotfiles.git ~/.dotfiles
~/.dotfiles/bin/install && exec zsh
```

## What the old script got wrong

The 2024 `fresh.sh` assumed a blank machine. It ran `rm -rf $HOME/.zshrc`, then `mkdir $HOME/Code` with no `-p`. A second run stopped at the `mkdir`. Fixing one step meant running everything again by hand.

## Six steps, each one checks first

`bin/install` prints a numbered header per step:

1. oh-my-zsh, installed only when `~/.oh-my-zsh` is missing.
2. Homebrew, installed only when `brew` is not on PATH.
3. Symlinks, made with `ln -sfn`, which replaces an existing link in place.
4. Machine profile, read from `profiles/current` or asked for.
5. Homebrew packages, from `config/Brewfile` and then the profile's `Brewfile`.
6. The profile's `install.sh` hook, when it exists and is executable.

Every step either skips or overwrites. Running the script twice gives the same result as running it once.

## Choosing a profile on a fresh machine

Profiles are private repos, so a new machine has none. The script lists any profile folders it finds, and also accepts a new name:

```
? Which machine profile? [personal work, or a new name]
```

When that folder is empty, the script asks for a git URL and clones the profile repo into it. A blank answer skips the clone with a warning.

## One hook per machine

The core links nothing machine-specific. Each profile can ship an `install.sh`, which runs last, after `brew bundle`, so brew tools are available. `$DOTFILES` is exported for it. The hook has no rules: symlinks, extra clones, anything.

This replaced a separate `clone.sh` that the 2024 version called from `fresh.sh`.

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

The script used to finish with `exec zsh`. The script runs as a child process, so that started a new zsh nested under the script. Ghostty then warned that a process was still running when I closed the tab.

Now the script prints `exec zsh` as the next command. A `dotup` function in `.functions` runs `bin/install && exec zsh` from the interactive shell, which replaces that shell directly.

Next: [halving zsh startup time](/posts/dotfiles-part-4-halving-zsh-startup-time).
