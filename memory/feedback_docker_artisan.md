---
name: Docker artisan コマンドの実行方法
description: artisanコマンドはdocker-compose exec appを経由して実行する
type: feedback
---

artisanコマンドは `docker-compose exec app php artisan ...` で実行する。`docker exec <container-name>` は使わない。

**Why:** コンテナのワーキングディレクトリが `/data` に設定されており、docker-compose経由でないと正しく動作しない。

**How to apply:** artisanを使う場面では必ず `docker-compose exec app php artisan ...` の形式を使う。
