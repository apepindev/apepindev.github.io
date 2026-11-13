---
extends: _layouts.post
section: content
title: "Logs, traces and metrics for a local cluster"
excerpt: "Grafana, Loki, Tempo and Prometheus on a laptop, with Alloy as the one collector. Four reasons telemetry went missing, and dashboards that copy straight to production."
date: 2026-11-13
description: "Grafana, Loki, Tempo and Prometheus on a laptop, with Alloy as the one collector. Four reasons telemetry went missing, and dashboards that copy straight to production."
featured: false
categories:
  - configuration
published: false
share: true
---

<!-- DRAFT. Source: k8s/observability/ and commits 26444d3, 18152ec, f9b9994, d6c4dfb, 211a934, 9abdaf3; session memory for the Octane flush. Check every fact and add your own voice before publishing. -->

The observability stack went in four days after the first commit, on 2026-05-19. A request crosses up to five services, and without traces a slow one gives no clue where the time went.

## The shape

Everything runs in the `observability` namespace as single-binary deployments with filesystem storage:

- Alloy runs as a DaemonSet. It tails every container's logs and receives OTLP from the apps.
- Tempo stores traces. It also generates span metrics: request rate, errors and duration per service and route.
- Loki stores logs.
- Prometheus scrapes any pod annotated `prometheus.io/scrape: "true"`. A pod opts in with no central config edit.
- Grafana provisions all three datasources at startup.

Apps send to one endpoint, set once in `platform/values.yaml`:

```yaml
otlp:
  http: "http://alloy.observability:4318"
  grpc: "http://alloy.observability:4317"
```

Later additions: blackbox-exporter for uptime probes, Mailpit to catch mail, GlitchTip for errors, ClickHouse, and a load generator with per-service API keys.

## 1. Loki received nothing

Alloy's `loki.source.kubernetes` tails logs through the API server. Alloy started, found every pod and shipped zero lines. Its ClusterRole granted `pods`. Reading logs goes through the `pods/log` subresource, and RBAC treats it as a separate resource:

```yaml
rules:
  - apiGroups: [""]
    resources: [nodes, nodes/proxy, nodes/metrics, services, endpoints,
                namespaces, pods, pods/log, events]
    verbs: [get, list, watch]
```

One word fixed it on 2026-06-12.

## 2. Every service's logs in one bucket

Loki guesses `service_name` from the container name. Every Laravel service has containers called `app`, `queue` and `scheduler`. Nine services' logs landed in three streams.

Alloy now sets `service_name` from the pod's `app` label, for the `default` namespace only:

```alloy
rule {
  source_labels = ["__meta_kubernetes_namespace", "__meta_kubernetes_pod_label_app"]
  separator     = "/"
  regex         = "default/(.+)"
  replacement   = "$1"
  target_label  = "service_name"
}
```

The value matches the OTel `service.name` and the `service` label on span metrics. One dashboard variable filters logs, traces and metrics together. Infra pods in other namespaces keep Loki's own detection.

## 3. Octane dropped its traces

schemes runs on Laravel Octane. Its HTTP server spans never reached Tempo. Under PHP-FPM the process ends after each request, and the OTel SDK flushes on shutdown. An Octane worker lives for thousands of requests and never shuts down, so the batch exporter held spans it never sent.

A listener forces a flush of traces and logs:

```php
class FlushOpenTelemetry
{
    public function handle(object $event): void
    {
        $tracerProvider = Globals::tracerProvider();
        if ($tracerProvider instanceof TracerProvider) {
            $tracerProvider->forceFlush();
        }

        $loggerProvider = Globals::loggerProvider();
        if ($loggerProvider instanceof LoggerProvider) {
            $loggerProvider->forceFlush();
        }
    }
}
```

It runs after every request, task and tick:

```php
// config/octane.php
RequestTerminated::class => [FlushOpenTelemetry::class],
TaskTerminated::class    => [FlushOpenTelemetry::class],
TickTerminated::class    => [FlushOpenTelemetry::class],
```

<!-- From memory, verify: schemes also moved from raw auto-instrumentation to the shared kamma/sdk package. -->

ps-api also runs Octane and has no such listener. <!-- TODO: does ps-api get its traces another way, or is this a gap? -->


## 4. Metrics with the wrong service name

Span metrics group by `service.name`. api-v3 and ld-api reported names that did not match their Kubernetes `app` label, so their RED panels stayed empty. Setting `OTEL_SERVICE_NAME` in each chart's base `env:` lined them up on 2026-07-23.

ld-api also wrote plain-text logs. Loki could not read a level off them, so every line showed as unknown. JSON logs in dev fixed that, and the Monolog services derive a level from `level_name`.

## Dashboards that copy to production

Production runs the same Grafana dashboards. Both clusters provision their datasources with fixed uids, and dashboards reference the uid instead of a name:

```json
"datasource": { "type": "prometheus", "uid": "prometheus" }
```

The uids are `prometheus`, `loki` and `tempo` in both places.

Since 2026-10-09 the dashboard JSON and Prometheus rules are kept in sync with the helm-charts repo. A dashboard built locally against real traffic from the load generator moves to production by copying one file.

Dashboards load from a ConfigMap through Grafana's file provisioning. Grafana reads those files only at pod start, so `make grafana-reload` re-applies the ConfigMap and restarts Grafana.

## Turning it off

The stack is the largest steady RAM user in the cluster. A Tilt button scales it to zero when the laptop needs the memory back, covered in part 3.
