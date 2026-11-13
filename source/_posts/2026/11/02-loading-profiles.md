---
extends: _layouts.post
section: content
title: "Dotfiles part 2: loading machine profiles in zsh"
excerpt: "One file names the active profile. The load order matters because oh-my-zsh reads its plugins array once."
date: 2026-11-13
description: "One file names the active profile. The load order matters because oh-my-zsh reads its plugins array once."
featured: false
categories:
  - configuration
published: true
share: true
---

Part 2 of my [dotfiles series](/posts/dotfiles-part-1-a-public-core-with-private-machine-profiles).

## Picking the profile

`profiles/current` just holds one word, `personal` or `work`, or whatever you name the profile directory. `bin/install`
writes it but will ask which existing/new profile to select. It then becomes the first thing `.zshrc` reads to set the
profile for terminal session:

```zsh
[ -r "$DOTFILES/profiles/current" ] && PROFILE="$(cat "$DOTFILES/profiles/current")"
```

## Plugins come before oh-my-zsh

oh-my-zsh needs to read the `plugins` array early and only once when `oh-my-zsh.sh` is sourced, as a plugin added
afterwards never loads. So the profile's `plugins.zsh` is sourced and appended to the core plugins early with
`plugins+=(...)`. The shared base of `git`, `macos` and `docker` stays in place:

```zsh
plugins=(git macos docker)
[ -n "$PROFILE" ] && [ -r "$DOTFILES/profiles/$PROFILE/plugins.zsh" ] \
  && source "$DOTFILES/profiles/$PROFILE/plugins.zsh"

source "$ZSH/oh-my-zsh.sh"
```

## Shared files, then the profile

After oh-my-zsh, `.zshrc` sources the shared `.exports`, `.aliases` and `.functions`. Then it sources every `*.zsh` in
the profile, except `plugins.zsh`, which is already loaded:

```zsh
for file in "$DOTFILES_PROFILE_DIR"/*.zsh; do
  [ "$file" = "$DOTFILES_PROFILE_DIR/plugins.zsh" ] && continue
  [ -r "$file" ] && source "$file"
done
prepend_path "$DOTFILES_PROFILE_DIR/bin"
```

The profile loads after the shared files, so a profile alias takes precedence over a core one with the same name.

Starship starts last with `eval "$(starship init zsh)"`. `ZSH_THEME=""` just prevents oh-my-zsh drawing its own as I'm 
just using Starship for the prompt.

## Re-sourcing safely

As `source ~/.zshrc` runs every line again, a plain `PATH="$dir:$PATH"` would add a duplicate entry on each reload.
`prepend_path` in `.exports` adds a directory only when it is missing:

```zsh
function prepend_path {
  case ":$PATH:" in
    *":$1:"*) :;;
    *) PATH="$1:$PATH";;
  esac
}
```

## Guarded tool setup

Both machines install the same version managers, but a missing tool shouldn't break the shell. So each init line checks
if the tool exists first:

```zsh
command -v zoxide &> /dev/null && eval "$(zoxide init zsh)"
command -v rbenv &> /dev/null && eval "$(rbenv init - zsh)"
```

Aliases for `eza` (for `ls`), `bat` (for `cat`) and `rg` (for `grep`) use the same guard. So for example a machine
without `eza`, `ls` stays plain `ls`.

Next: [the install script](/posts/dotfiles-part-3-an-idempotent-install-script).
