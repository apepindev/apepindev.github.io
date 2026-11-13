---
extends: _layouts.post
section: content
title: "A local Kubernetes cluster for nine services: the layout"
excerpt: "One k3d cluster, one Tiltfile and nine Helm charts replace a docker-compose stack per service. Part 1 covers the file layout."
date: 2026-11-13
description: "One k3d cluster, one Tiltfile and nine Helm charts replace a docker-compose stack per service. Part 1 covers the file layout."
featured: false
categories:
  - configuration
published: false
share: true
---

<!-- DRAFT. Source: dev-cluster repo (46 commits, 2026-05-15 to 2026-10-23) and session memory. Check every fact and add your own voice before publishing. Work repo: confirm what you can share, and keep the GCP project id, GitHub org and GlitchTip DSN out. -->

## Why build it

<!-- TODO: your reason. Something like: nine services, nine docker-compose stacks, each with its own Postgres and Redis, and no way to run a request through all of them at once. -->

Each service at work had its own docker-compose stack. Running a request that crosses five services meant five stacks, five Postgres containers and five copies of Traefik fighting over port 443. Production runs on GKE, so the local setup looked nothing like the place the code ships to.

The replacement is one repo, `dev-cluster`. It started on 2026-05-15 and reached its current shape on 2026-10-23, 46 commits later. Two commands bring the whole platform up:

```sh
make cluster-create   # k3d, certs, Traefik, shared infra, observability
tilt up               # build or pull every service and helm-deploy it
```

Every service then answers at `https://<service>.kamma.localhost` with a trusted certificate.

## The series

1. The layout (this post)
2. [Released image or local clone, per service](#) <!-- TODO: link -->
3. [macOS networking: IPv6, Xdebug and database ports](#)
4. [Hot module reload through a hostPath mount](#)
5. [Logs, traces and metrics on a laptop](#)

## The tools

- k3d runs k3s inside Docker containers on OrbStack. A cluster is one container per node plus a load balancer container.
- Traefik ships with k3s. It terminates TLS for every ingress and exposes Postgres, MariaDB and Valkey on TCP ports.
- Helm renders one chart per service. The charts live in this repo, so the service repos stay untouched.
- Tilt builds images, deploys the charts and gives a web UI with buttons per service.
- mkcert mints a wildcard certificate for `*.kamma.localhost` and installs its CA into the macOS trust store.
- Make owns everything that is not a service: cluster lifecycle, certificates, shared infra and the observability stack.

The split between Make and Tilt matters. Shared infra gets applied once at cluster creation and never rebuilds. Tilt only handles the parts that change while you work.

## The file layout

```
dev-cluster/
├── k3d-config.yaml        # cluster shape, host ports, volumes, registry mirror
├── k3d/lb-nginx.tmpl      # load balancer nginx template (IPv6 listeners)
├── Tiltfile               # builds or pulls every service, helm-deploys it
├── Makefile               # cluster lifecycle, certs, shared infra, pod helpers
├── images.yaml            # pinned released tag per service
├── platform/values.yaml   # where the database, cache, S3 and collector live
├── envs/                  # per-developer config (gitignored)
│   ├── local.yaml         # which services run from your clone
│   └── secrets.yaml       # SSH key path, composer auth
├── scripts/images-sync.sh # re-pin images.yaml from production
└── k8s/
    ├── traefik/           # TLSStore, HTTP to HTTPS redirect, TCP entrypoints
    ├── shared/            # pgsql, mariadb, valkey, seaweedfs, redis-insight
    ├── observability/     # Grafana, Loki, Tempo, Prometheus, Alloy, alerts
    └── kamma-<service>/chart/
```

Applications run in the `default` namespace, so `kubectl logs` and `kubectl exec` need no `-n` flag. Shared infra runs in `kamma-shared`. A service reaches Postgres at `pgsql.kamma-shared`, and the namespace in the hostname shows that it crosses a boundary.

## The cluster

The whole cluster is one YAML file:

```yaml
apiVersion: k3d.io/v1alpha5
kind: Simple
metadata:
  name: kamma-local
servers: 1
agents: 0
volumes:
  # Pods hostPath-mount service repos at their real macOS path.
  - volume: $HOME:$HOME
    nodeFilters: [server:*, agent:*]
  # Named Docker volumes outlive `k3d cluster delete`.
  - volume: kamma-pgsql-data:/data/pgsql
    nodeFilters: [server:*, agent:*]
ports:
  - port: 443:443
    nodeFilters: [loadbalancer]
  - port: 5432:5432
    nodeFilters: [loadbalancer]
registries:
  use:
    - k3d-registry.localhost:5050
```

Three choices in that file came from problems, and part 3 covers them:

- `agents: 0` removed a race after every Mac reboot.
- The named volumes keep databases and buckets across `make cluster-delete`.
- The registry is a standalone container, so the image cache survives a cluster rebuild.

The `$HOME:$HOME` mount is the core of the design. A pod can mount `/Users/you/Code/kamma/bulk-import` at `/var/www/html`, exactly like a docker-compose bind mount. Saving a file on the Mac changes it inside the pod. No rebuild, no pod restart.

## One function per service

The Tiltfile defines `kamma_service()`, and each service is one call to it:

```python
kamma_service(
    "bulk-import",
    dockerfile="docker/serversideup/Dockerfile",
    target="development",
    build_args={"WWWUSER": UID, "WWWGROUP": GID},
    composer_home=True,
    composer_auth_env=True,
)

# Go services pull private modules over SSH at build time.
kamma_service("api-v3", dockerfile="docker/Dockerfile", ssh_secret=True)
```

The PHP services pass the host UID as a build arg, so files the app writes to the mount belong to you on the Mac. The Go services get the SSH key as a BuildKit secret. Nine services fit in about 60 lines.

The function either builds the image from your clone or deploys the released image. Part 2 explains how it picks. Either way it renders the chart with `helm()` and groups the Ingress, ConfigMap and Secret under the service in the Tilt UI.

Builds are manual. The image carries the runtime only, and the source arrives through the mount. A default Tilt setup watches the build context and rebuilds on every save, which would roll the pod each time. So the Tiltfile sets:

```python
trigger_mode(TRIGGER_MODE_MANUAL)
```

A Dockerfile change still needs a rebuild: click the button in the UI or run `tilt trigger <service>`.

## A guardrail

Kubernetes tools act on whatever context is current. A stray production context turns `tilt up` into a production deploy. The first lines of the Tiltfile refuse to run anywhere else:

```python
allow_k8s_contexts("k3d-kamma-local")
if k8s_context() != "k3d-kamma-local":
    fail("wrong kube-context %r — switch to k3d-kamma-local first" % k8s_context())
```

The Makefile does the same by hard-coding `kubectl --context=k3d-kamma-local`.

## Skaffold first, then Tilt

The first version used Skaffold. Each service had a `skaffold.yaml.tmpl`, and `make prepare` ran `envsubst` over 9 templates to fill in absolute paths and the host UID. On 2026-07-06 one Tiltfile replaced all of it. Starlark computes the paths and UID directly, so the template step disappeared.

The charts, cluster, certificates and observability stack stayed unchanged in that migration. One behaviour changed: Skaffold could side-load images straight into the k3d node, and Tilt always pushes to the registry. With the registry on `localhost:5050` the push costs little.

<!-- TODO: what else pushed you to Tilt? The UI buttons, per-service rebuild, or speed? -->

## Adding a service

1. Copy `k8s/kamma-bulk-import` to `k8s/kamma-<service>` and rename the chart.
2. Point the chart's `platformEnv` at the shared services.
3. Add the database to the `pgsql-init` ConfigMap.
4. Add a `kamma_service()` call to the Tiltfile.
5. Add the service to `images.yaml`.

`tilt up` then serves it at `https://<service>.kamma.localhost` with no hosts file edits. Part 3 explains why no hosts file is needed.
