---
extends: _layouts.post
section: content
title: "Vite hot reload inside a Kubernetes pod"
excerpt: "A Vite sidecar, a hostPath mount and four reasons HMR went silent: inotify, bun, macOS native bindings and a wedged dev server."
date: 2026-11-13
description: "A Vite sidecar, a hostPath mount and four reasons HMR went silent: inotify, bun, macOS native bindings and a wedged dev server."
featured: false
categories:
  - configuration
  - laravel
published: false
share: true
---

<!-- DRAFT. Source: bulk-import chart deployment.yaml and values-source.yaml, commits 815818d, d89623c, 5f0d2ab; session memory for the Filament changes. Check every fact and add your own voice before publishing. -->

Two Laravel apps need a Vite dev server: suite and bulk-import, whose admin panel is Filament. Each pod runs Vite as a sidecar next to the PHP container. Both containers mount the same clone. Traefik serves the sidecar at `vite-<service>.kamma.localhost`, and the browser loads modules and the HMR websocket from there.

```yaml
- name: vite
  image: oven/bun:1.2.5-alpine
  ports:
    - containerPort: 5173
  envFrom:
    - configMapRef:
        name: bulk-import-env
  volumeMounts:
    - name: source
      mountPath: /var/www/html
    - name: vite-node-modules
      mountPath: /var/www/html/node_modules
```

The sidecar exists only in source mode. A released image carries built assets.

The env tells Vite which public origin it sits behind:

```yaml
VITE_DEV_HOST: "0.0.0.0"
VITE_DEV_ORIGIN: "https://vite-bulk-import.{{ .Values.platform.domain }}"
VITE_HMR_HOST: "vite-bulk-import.{{ .Values.platform.domain }}"
VITE_HMR_PROTOCOL: "wss"
VITE_HMR_CLIENT_PORT: "443"
```

Getting it to reload took four fixes.

## 1. No file events cross the mount

An edit on the Mac changed the file in the pod, and Vite never noticed. inotify events do not cross the macOS to VM to k3d hostPath boundary. The file content syncs. The event that says it changed does not.

The fix is polling, switched on only in the cluster:

```yaml
# values-source.yaml
VITE_WATCH_POLL: "true"
```

```js
// vite.config.js
watch: {
    ...(VITE_WATCH_POLL ? { usePolling: true, interval: 300 } : {}),
    ignored: [resolve(projectRoot, 'vendor/**'), resolve(projectRoot, 'storage/**')],
},
```

Vite 6 tolerated the missing events. Vite 7 did not, so HMR died with the upgrade. A 300 ms interval adds a reload lag nobody notices.

Running `bun run dev` on the Mac leaves the variable unset and keeps native events.

Watching every file under the repo costs CPU and memory. Vite watched `vendor/` and `storage/` too, and the sidecar crash looped on startup. The `ignored` list fixed it in suite and sso first. The paths are anchored to the project root on purpose: a glob like `**/vendor/**` also matches `resources/js/vendor/`, and those entrypoints silently dropped out of HMR.

## 2. Vite under bun fires no events

bulk-import's HMR stayed dead after polling worked for suite. Assets loaded with 200s. Editing a Blade view or a Filament resource did nothing.

The sidecar ran Vite under bun. Raw `fs.watch` worked in bun. Chokidar, which Vite bundles, attached its watchers and then emitted zero change events. Polling made no difference. Running the same Vite under Node fired events at once.

The sidecar now uses bun for the fast install and Node for the dev server:

```sh
set -e
cd /var/www/html
bun install --frozen-lockfile
apk add --no-cache nodejs >/dev/null
exec node node_modules/vite/bin/vite.js --host 0.0.0.0
```

`--host 0.0.0.0` fixed a second failure. Vite binds to localhost by default. The probes and Traefik dial the pod IP, so the pod never became Ready and Traefik answered "no available server".

## 3. Linux binaries in a macOS node_modules

`bun install` inside the pod writes to the mount, which is your clone on the Mac. It replaced macOS native bindings (esbuild, rollup) with Linux ones, and `bun run dev` on the Mac broke.

An `emptyDir` over `node_modules` gives the container its own copy:

```yaml
volumes:
  - name: vite-node-modules
    emptyDir: {}
```

The host `node_modules` stays untouched. The pod's copy survives container restarts and resets when the pod is recreated.

## 4. A dev server that stays up and serves nothing

Vite restarts itself when `.env` changes. Under bun that restart sometimes hung: the process stayed alive, port 5173 closed and never reopened. The app loaded without styles and nothing recovered it.

The original probe was a readiness probe with `failureThreshold: 60` to absorb the cold `bun install`. It hid the hang too. The probes split on 2026-06-12:

```yaml
startupProbe:          # cold start: bun install + first build
  tcpSocket: { port: 5173 }
  periodSeconds: 5
  failureThreshold: 60
readinessProbe:
  tcpSocket: { port: 5173 }
  periodSeconds: 5
livenessProbe:         # closed port: restart only this container
  tcpSocket: { port: 5173 }
  periodSeconds: 10
  failureThreshold: 3
```

The startup probe allows 5 minutes. After it passes, three failed liveness checks restart the Vite container alone. The `emptyDir` keeps `node_modules`, so the re-install is a no-op and Vite is back in seconds.

## Filament on Vite 7

bulk-import runs Vite 7 and needed three app changes on top of the sidecar.

Filament renders its own precompiled assets and never emits the app's `@vite` bundle. A render hook in `AdminPanelProvider` adds it, so the panel loads the Vite client and opens the HMR socket:

```php
fn (): string => Blade::render("@vite('resources/js/app.js')"),
```

`@vite` costs nothing in production. It emits the dev client only while a dev server is running.

The page loads from `bulk-import.kamma.localhost` and the modules from `vite-bulk-import.kamma.localhost`. That is cross-origin. Vite 6 allowed any origin, and Vite 7 rejects unknown ones, so the client never booted. The config allows every `kamma.localhost` subdomain, and only when the cluster's `VITE_*` env is set:

```js
cfg.cors = { origin: /^https?:\/\/([a-z0-9-]+\.)?kamma\.localhost(:\d+)?$/ };
```

A full page reload on every save threw away Filament form state. `@defstudio/vite-livewire-plugin` re-renders the Livewire components in place instead. laravel-vite-plugin's own `refresh` must be off, or its full reload wins:

```js
laravel({ input: ['resources/css/app.css', 'resources/js/app.js'], refresh: false }),
livewire({
    refresh: [
        'resources/views/**',
        'app/Livewire/**',
        'app/Filament/**',
        'app/Providers/Filament/**',
    ],
}),
```

The plugin is pinned to 3.0.x, whose peer dependency is Vite 7. Version 3.0.7 and later require Vite 8.

A change to `vite.config.js` still needs a restart of the sidecar container. Vite does not reload its own config through the polling watcher.
