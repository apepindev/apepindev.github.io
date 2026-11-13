---
extends: _layouts.post
section: content
title: "k3d on macOS: IPv6, Xdebug, database ports and reboots"
excerpt: "Five problems between macOS, OrbStack and a k3d cluster, and the line of config that fixed each one."
date: 2026-11-13
description: "Five problems between macOS, OrbStack and a k3d cluster, and the line of config that fixed each one."
featured: false
categories:
  - configuration
published: false
share: true
---

<!-- DRAFT. Source: dev-cluster repo, README and commits 77f62b9, 8153bce, b834190; session memory for the OrbStack numbers. Check every fact and add your own voice before publishing. -->

k3d runs Kubernetes nodes as Docker containers inside the OrbStack VM. A request from the Mac crosses macOS, the VM, the k3d load balancer container, Traefik and the pod. Each boundary caused one problem.

## 1. Bruno could not reach `*.localhost`

macOS resolves any `.localhost` name to loopback with no hosts file entry. So `bulk-import.kamma.localhost` works out of the box, and that is why the cluster uses the domain.

macOS returns `::1` first. k3d's load balancer is an nginx container that listens on IPv4 only. curl and browsers fall back to `127.0.0.1`. Node-based HTTP clients like Bruno do not, and the connection fails.

The fix overrides the nginx template inside the load balancer. k3d generates its config with confd, and the template file can be mounted over:

```yaml
# k3d-config.yaml
volumes:
  - volume: $PWD/k3d/lb-nginx.tmpl:/etc/confd/templates/nginx.tmpl
    nodeFilters: [loadbalancer]
```

The copied template adds one line per port:

```nginx
server {
  listen        {{ $port }};
  listen        [::]:{{ $port }};
  proxy_pass    {{ $upstream }};
}
```

Only the outer hop listens on IPv6. Traefik and the k3s service load balancer stay IPv4.

## 2. Xdebug dialled the wrong host

k3d adds `host.k3d.internal` to the cluster's CoreDNS so pods can reach the host. On OrbStack and Docker Desktop that name resolves to the VM's bridge gateway. PhpStorm listens on the Mac, one hop further out, so Xdebug got connection refused.

`host.docker.internal` is the name OrbStack reserves for the real macOS host:

```yaml
XDEBUG_MODE: "develop,debug"
XDEBUG_CONFIG: "client_host=host.docker.internal client_port=9003"
PHP_IDE_CONFIG: "serverName=bulk-import.{{ .Values.platform.domain }}"
```

A one-line check from inside the pod confirms the path while PhpStorm listens:

```sh
kubectl exec deploy/bulk-import -c app -- \
  bash -c '</dev/tcp/host.docker.internal/9003 && echo OPEN || echo closed'
```

## 3. Databases from the host without port-forward

The first version ran `kubectl port-forward` for Postgres and Valkey. Every cluster restart broke the forward, and TablePlus lost its connection. Tests run from PhpStorm needed the same databases.

Traefik can route raw TCP. The HelmChartConfig adds an entrypoint per database:

```yaml
ports:
  postgres:
    port: 5432
    exposedPort: 5432
    protocol: TCP
    expose:
      default: true
```

An `IngressRouteTCP` sends that entrypoint to the Service. Plain TCP carries no SNI, so `HostSNI(`*`)` matches everything and the port selects the database:

```yaml
apiVersion: traefik.io/v1alpha1
kind: IngressRouteTCP
metadata:
  name: pgsql
  namespace: kamma-shared
spec:
  entryPoints: [postgres]
  routes:
    - match: HostSNI(`*`)
      services:
        - name: pgsql
          port: 5432
```

With the port mapped on the k3d load balancer, `psql -h postgres.kamma.localhost` works whenever the cluster is up. The hostname is only a readable alias for loopback.

The load balancer binds host ports 5432, 3306 and 6379 unconditionally. A local Postgres already on 5432 blocks cluster creation. Change the host side of the mapping in `k3d-config.yaml` instead.

## 4. The agent node went NotReady after every reboot

The cluster started with one server and one agent. After a Mac reboot Docker started every container with a restart policy at once. The agent tried to join before the server was ready and stayed NotReady. Pods with local-path volumes on the agent stayed pending.

Three fixes landed in order:

1. `make cluster-restart` on 2026-06-09 did an ordered stop and start.
2. `make cluster-no-autostart` the same day set the restart policy to `no` on every cluster container. A reboot now leaves the cluster down until `make cluster-start`.
3. `agents: 0` removed the agent. The k3d server node runs workloads too, so the agent only added a second kubelet and containerd inside the VM. No agent means no join race.

```make
cluster-no-autostart:
	@ids="$$(docker ps -aq --filter 'label=k3d.cluster=kamma-local')"; \
	docker update --restart=no $$ids
```

`k3d cluster create` resets the policy to `unless-stopped`, so `cluster-create` calls this target straight after.

## 5. The VM ran out of memory

<!-- From memory, verify: numbers below come from a session note, not the repo. -->

With 9 services, their queue and scheduler containers, and the observability stack, the OrbStack VM hit its RAM cap. The VM swapped, OrbStack's helper process pegged the CPU and the whole Mac slowed down. The cap was 15.65 GiB on a 32 GB Mac.

Raising the cap to about 22 GiB and capping VM CPU fixed it. Both settings live in OrbStack, and changing them restarts the VM along with k3d and Tilt. So they stay out of the Tiltfile.

The Tiltfile gained buttons for the knobs that are safe at runtime instead:

- `Observability → 0` scales every observability Deployment and StatefulSet to 0. DaemonSets have no replica count, so it pins them to a node label that no node has.
- A per-service `scale replicas` button drops one service and its queue workers to 0.

```python
for ds in alloy node-exporter vector; do
  kubectl -n observability patch ds $ds -p \
    '{"spec":{"template":{"spec":{"nodeSelector":{"kamma.dev/paused":"true"}}}}}'
done
```

## Keeping state across rebuilds

Two more choices make `make cluster-delete && make cluster-create` cheap:

- The image registry is a standalone container. It used to be created inline with the cluster and died with it, so every rebuild re-pushed every image.
- Postgres, MariaDB and SeaweedFS data live in named Docker volumes mounted into the node. `k3d cluster delete` never removes Docker volumes, so databases and buckets survive.

`make data-volumes-nuke` is the one command that wipes them.
