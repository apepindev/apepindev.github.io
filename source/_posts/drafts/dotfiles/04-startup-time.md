---
extends: _layouts.post
section: content
title: "Dotfiles part 4: halving zsh startup time"
excerpt: "Swapping nvm for fnm and lazy-loading thefuck cut a new shell from about 1.5s to 0.8s."
date: 2026-11-13
description: "Swapping nvm for fnm and lazy-loading thefuck cut a new shell from about 1.5s to 0.8s."
featured: false
categories:
  - configuration
published: false
share: true
---

<!-- DRAFT. Source: ~/.dotfiles commit 0abd9dd and home/.exports. Check every fact and add your own voice before publishing. -->

Part 4 of the [dotfiles series](/posts/dotfiles-part-1-a-public-core-with-private-machine-profiles).

A new shell took about 1.5 seconds to start. After two changes it took about 0.8 seconds.

<!-- TODO: add the measurement command, e.g. `for i in $(seq 10); do /usr/bin/time zsh -i -c exit; done`, and real before and after numbers. -->

## nvm to fnm

nvm is a shell script. Sourcing it on every shell start was the largest single cost. [fnm](https://github.com/Schniz/fnm) is a compiled Rust binary that does the same job:

```zsh
command -v fnm &> /dev/null && eval "$(fnm env --use-on-cd)"
```

`--use-on-cd` switches Node version when a directory has a `.nvmrc` or `.node-version` file.

## Lazy-loading thefuck

`thefuck --alias` starts Python to print the alias, about 130ms on every shell start. The alias is rarely needed in a given shell. A small function defers the cost to first use:

```zsh
if command -v thefuck &> /dev/null; then
  fuck() {
    unfunction fuck
    eval "$(thefuck --alias)"
    fuck "$@"
  }
fi
```

On the first call, the function deletes itself, defines the real alias, and runs it with the same arguments. Later calls go straight to the alias.

## Pointing starship at its config

starship reads `~/.config/starship.toml` by default. The symlink lives at `~/.config/starship/starship.toml`, so starship was running without my config. One export fixed it:

```zsh
export STARSHIP_CONFIG="$HOME/.config/starship/starship.toml"
```

The same commit disabled starship's `aws` module.

<!-- TODO: confirm whether the STARSHIP_CONFIG and aws changes affected startup time, or only correctness. -->

Next: [per-machine git identity](/posts/dotfiles-part-5-per-machine-git-identity).
