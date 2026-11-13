---
extends: _layouts.post
section: content
title: "Released image or local clone, per service"
excerpt: "Every service runs the tag production runs, until you switch one to your clone. Part 2 covers images.yaml, the source switch and five layers of env vars."
date: 2026-11-13
description: "Every service runs the tag production runs, until you switch one to your clone. Part 2 covers images.yaml, the source switch and five layers of env vars."
featured: false
categories:
  - configuration
published: false
share: true
---

<!-- DRAFT. Source: dev-cluster repo, commits e0cd13a to b31215e (2026-10-21 to 2026-10-23). Check every fact and add your own voice before publishing. Replace the registry path with a placeholder. -->

## The problem

The first version built every service from source. A new starter had to clone 9 repos, run `composer install` in 5 of them and wait for 9 image builds before the first request worked. Most days you change one service and only need the other eight to answer.

<!-- TODO: confirm the onboarding pain was the trigger. -->

Since 2026-10-21, each service runs in one of two modes:

- `image` (the default) pulls the released image at the tag production runs. No clone, no build.
- `local` builds from your clone and hostPath-mounts it, so edits are live.

## Pinning to production

`images.yaml` holds one pinned tag per service:

```yaml
registry: europe-west4-docker.pkg.dev/<project>/<repo>
services:
  bulk-import:
    package: kamma-bulk-import
    helmChart: kamma-bulk-import
    tag: "v2.10.2"
  ld-api:
    package: licence-determination
    helmChart: kamma-ldapi
    tag: "v4.5.4"
```

Production deploys are GitOps: a Helm values file on a branch of the helm-charts repo names each tag. `make images-sync` reads those files through the GitHub API, so it needs no local clone:

```bash
values="$(gh api -H "Accept: application/vnd.github.raw" \
  "repos/$repo/contents/$chart/values/production_values.yaml?ref=$branch")"
tag="$(printf '%s\n' "$values" | yq '.tag')"

if ! [[ "$tag" =~ ^v?[0-9]+\.[0-9]+\.[0-9]+$ ]]; then
  echo "$svc: production tag '$tag' is not semver, keeping $old" >&2; continue
fi
if ! gcloud artifacts docker images describe "$registry/$package:$tag" >/dev/null 2>&1; then
  echo "$svc: $registry/$package:$tag not found, keeping $old" >&2; continue
fi
yq -i ".services[\"$svc\"].tag = \"$tag\"" "$pins"
```

The script refuses two kinds of tag. A non-semver tag like `latest` or a branch name moves under you. A tag missing from Artifact Registry fails at pull time with an error far from the cause. Either way the old pin stays and the script exits non-zero.

The first sync read a local clone of helm-charts. A clone drifts behind its remote, and the pins drifted with it. Reading from GitHub removed that step on 2026-10-23.

## Choosing per machine

Each developer has a gitignored `envs/local.yaml`:

```yaml
services:
  bulk-import:
    source: local
    path: /Users/you/Code/kamma/bulk-import
    env:
      IMPORT_SCHEDULES_ENABLED: "false"
  api-v3:
    path: /Users/you/Code/kamma/api-v3   # kept so switching back is one click
    tag: v0.41.2                         # image mode, a different tag
```

The Tiltfile reads it at load time and fails fast on a typo:

```python
def service_mode(svc):
    src = str((LOCAL_ENV.get(svc) or {}).get("source", "image")).strip()
    if src not in ["image", "local"]:
        fail("%s: source %r in %s must be image or local" % (svc, src, LOCAL_ENV_FILE))
    return src

for _svc in LOCAL_ENV:
    if _svc not in IMAGES["services"]:
        fail("%s: %r isn't a service in images.yaml" % (LOCAL_ENV_FILE, _svc))
```

Each service also gets a button in the Tilt UI that rewrites `envs/local.yaml` with `yq`. Tilt watches the file, reloads the Tiltfile and rolls the pod into the other mode.

## Pulling with a one-hour token

Image mode pulls from a private GCP registry with your `gcloud auth login` token. Access tokens last one hour. A pod rescheduled in the afternoon would fail its pull with a 401. A Tilt `local_resource` writes the pull secret at startup and rewrites it every 45 minutes:

```python
_pull_secret = (
    "kubectl -n default create secret docker-registry gcp-artifact-registry "
    + "--docker-server=%s --docker-username=oauth2accesstoken "
    + '--docker-password="$(gcloud auth print-access-token)" '
    + "--dry-run=client -o yaml | kubectl apply -f -"
) % AR_HOST

local_resource(
    "gcp-pull-secret",
    cmd=_pull_secret,
    serve_cmd="while sleep 2700; do %s; done" % _pull_secret,
)
```

`cmd` runs once before any service deploys. `serve_cmd` keeps running for the life of `tilt up`.

## Five layers of env vars

A released image carries no `.env` file. In source mode your clone's `.env` sits on the mount. The chart has to produce a working environment either way, so its ConfigMap merges five layers, later ones winning:

| Layer | File | Holds |
|---|---|---|
| Base | `chart/values.yaml` `env:` | Ports, log format, OTel names |
| Mode | `values-source.yaml` or `values-image.yaml` | Source: Xdebug, Vite HMR. Image: the keys from `.env.example` |
| Yours | `envs/local.yaml` | Anything you change on your machine |
| Platform | `platform/values.yaml` | Database, cache, S3, collector endpoints |
| Services | `chart/values.yaml` `serviceUrls:` | Sibling service URLs |

The template:

```yaml
data:
{{- $_ := required "platform is unset: pass platform/values.yaml" .Values.platform }}
{{- $all := dict }}
{{- range $k, $v := .Values.env }}
{{- $_ := set $all $k (tpl (toString $v) $) }}
{{- end }}
{{- range $k, $v := .Values.platformEnv }}
{{- $_ := set $all $k (tpl $v $) }}
{{- end }}
{{- range $k, $u := .Values.serviceUrls }}
{{- $_ := set $all $k (printf "http://%s.%s.svc%s" $u.service $.Release.Namespace ($u.path | default "")) }}
{{- end }}
{{- range $k, $v := $all }}
  {{ $k }}: {{ $v | quote }}
{{- end }}
```

Values pass through `tpl`, so a value can name another value. Hostnames are written once as `sso.{{ .Values.platform.domain }}`. The domain is `kamma.localhost` today, and a remote environment can set its own.

Sibling URLs come last and are built from the release namespace. An `.env.example` default can never send a request out of the environment. Remote URLs that should not be called point at `disabled.invalid`, a name that never resolves.

### Why `.env` still works

In source mode the ConfigMap loads through `envFrom`, and the mounted `.env` fills every key the ConfigMap leaves unset. Laravel's dotenv loader and the Go services never overwrite a variable that already exists. The cluster values for `DB_HOST` and friends always win, and you can edit the rest of `.env` freely.

### The exception: the entrypoint

Dotenv libraries read `.env` after the app process starts. Anything the container's shell reads before that has to sit in the chart's `env:` block. Two cases bit:

- supervisord interpolates `%(ENV_FOO)s` at launch. ps-api's queue settings had to move into the ConfigMap. `grep -o 'ENV_[A-Z_]*'` over the supervisord config finds them.
- tenancy-api and registry-api branch on `$ENV` in their entrypoint. Without `ENV: development` in the chart they took the production branch and ran a prebuilt `/app/main`. The hostPath mount had replaced it with a macOS build artefact, and the pod died with `Exec format error`.
