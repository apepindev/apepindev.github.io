---
extends: _layouts.post
section: content
title: "SeaweedFS for local S3, and the buckets it does not create"
excerpt: "Switching local object storage to SeaweedFS, and a one-shot Compose service that creates buckets."
date: 2026-11-12
description: "Switching local object storage to SeaweedFS, and a one-shot Compose service that creates buckets."
featured: false
categories:
  - configuration
published: false
share: true
---

<!-- DRAFT. Source: steadyer repo. Check every fact and add your own voice before publishing. -->

## Why change

<!-- TODO: your reason for leaving MinIO. -->

The switch landed on 2026-10-02 and touched 7 files.

## The catch

SeaweedFS does not create a bucket on first write. An upload to a missing bucket fails with `NoSuchBucket`.

## The fix

A one-shot `seaweedfs-buckets` service in `compose.yaml` runs the AWS CLI once SeaweedFS is healthy:

```sh
for bucket in screenshots feedback reports brand; do
  aws --endpoint-url http://seaweedfs:9000 s3api head-bucket --bucket "$bucket" 2>/dev/null \
    || aws --endpoint-url http://seaweedfs:9000 s3api create-bucket --bucket "$bucket"
done
```

The app service depends on it with `condition: service_completed_successfully`, so the app never starts before the buckets exist. It runs on every `docker compose up` and skips buckets that already exist.

## What I learnt

Adding a new filesystem disk now has a second step: add its bucket to that list.
