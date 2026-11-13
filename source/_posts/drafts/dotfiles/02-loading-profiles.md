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
published: false
share: true
---

<!-- DRAFT. Source: ~/.dotfiles home/.zshrc and home/.exports. Check every fact and add your own voice before publishing. -->

Part 2 of the [dotfiles series](/posts/dotfiles-part-1-a-public-core-with-private-machine-profiles).

## Picking the profile

`profiles/current` holds one word, `personal` or `work`. `bin/install` writes it. `.zshrc` reads it first:

```zsh
[ -r "$DOTFILES/profiles/current" ] && PROFILE="$(cat "$DOTFILES/profiles/current")"
```

## Plugins come before oh-my-zsh

oh-my-zsh reads the `plugins` array once, when `oh-my-zsh.sh` is sourced. A plugin added afterwards never loads. So the profile's `plugins.zsh` is sourced early, apart from the profile's other files:

```zsh
plugins=(git macos docker)
[ -n "$PROFILE" ] && [ -r "$DOTFILES/profiles/$PROFILE/plugins.zsh" ] \
  && source "$DOTFILES/profiles/$PROFILE/plugins.zsh"

source "$ZSH/oh-my-zsh.sh"
```

A profile's `plugins.zsh` appends with `plugins+=(...)`. The shared base of `git`, `macos` and `docker` stays in place.

## Shared files, then the profile

After oh-my-zsh, `.zshrc` sources the shared `.exports`, `.aliases` and `.functions`. Then it sources every `*.zsh` in the profile, skipping `plugins.zsh`:

```zsh
for file in "$DOTFILES_PROFILE_DIR"/*.zsh; do
  [ "$file" = "$DOTFILES_PROFILE_DIR/plugins.zsh" ] && continue
  [ -r "$file" ] && source "$file"
done
append_path "$DOTFILES_PROFILE_DIR/bin"
```

The profile loads after the shared files, so a profile alias overrides a shared one with the same name.

starship starts last with `eval "$(starship init zsh)"`. `ZSH_THEME=""` stops oh-my-zsh drawing its own prompt.

## Re-sourcing safely

`source ~/.zshrc` runs every line again. A plain `PATH="$dir:$PATH"` adds a duplicate entry on each reload. `prepend_path` in `.exports` adds a directory only when it is missing:

```zsh
function prepend_path {
  case ":$PATH:" in
    *":$1:"*) :;;
    *) PATH="$1:$PATH";;
  esac
}
```

## Guarded tool setup

Both machines install the same version managers, but a missing tool should not break the shell. Each init line checks first:

```zsh
command -v zoxide &> /dev/null && eval "$(zoxide init zsh)"
command -v rbenv &> /dev/null && eval "$(rbenv init - zsh)"
```

Aliases for `eza`, `bat` and `rg` use the same guard. On a machine without `eza`, `ls` stays plain `ls`.

Next: [the install script](/posts/dotfiles-part-3-an-idempotent-install-script).
