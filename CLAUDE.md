# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## プロジェクト概要

PHP 8.3 + nginx + MySQL 8.0 を使用した Laravel 13 Docker 環境。Udemy フルスタックエンジニア学習用リポジトリ。

## 技術スタック

- **PHP**: 8.3-fpm / Laravel 13
- **Web Server**: nginx 1.25-alpine
- **Database**: MySQL 8.0（本番は Aurora MySQL）
- **フロントエンド**: Tailwind CSS v4 + Vite 8 + Node.js 24
- **テスト**: PHPUnit 12

## よく使うコマンド

コンテナ内で実行するコマンドは `docker-compose exec app` を経由する。

```bash
# 開発環境
docker-compose up -d
docker-compose exec app bash
docker-compose down          # 停止
docker-compose down -v       # 停止 + データ削除

# Laravel
docker-compose exec app composer install
docker-compose exec app php artisan migrate
docker-compose exec app php artisan key:generate

# フロントエンド（コンテナ内 or ローカル）
npm install
npm run dev    # Vite 開発サーバー（ポート 5173）
npm run build  # 本番ビルド

# テスト
docker-compose exec app php artisan test
docker-compose exec app php artisan test --filter=テスト名  # 単体テスト実行

# コードフォーマット（Laravel Pint）
docker-compose exec app ./vendor/bin/pint

# 本番環境起動
docker-compose -f docker-compose.yml -f docker-compose.prod.yml up -d
```

## アーキテクチャ

```
/
├── infra/              # Docker インフラ設定
│   ├── mysql/          # MySQL 8.0（utf8mb4_ja 照合順序、スロークエリログ有効）
│   ├── nginx/          # リバースプロキシ → app:9000 (FastCGI)
│   └── php/            # PHP-FPM + Composer + Node.js ビルド環境
├── src/                # Laravel 13 アプリケーション本体
│   ├── app/            # Controller / Model / Provider
│   ├── database/       # Migration / Factory / Seeder
│   ├── resources/      # Blade テンプレート / CSS / JS
│   ├── routes/         # web.php / console.php
│   └── tests/          # Feature / Unit テスト
├── docker-compose.yml      # 開発環境
└── docker-compose.prod.yml # 本番環境（Aurora MySQL 対応）
```

### 重要な設定

- **Vite HMR**: Docker 外部からアクセスするため `server.host: "0.0.0.0"` かつ `hmr.host: "localhost"` が必要（`vite.config.js` 参照）
- **ポート**: HTTP=80, Vite=5173, MySQL=3380（外部）
- **ワーキングディレクトリ**: コンテナ内は `/data`（`src/` にマウント）
- **ユーザー**: コンテナ内 `appuser` (UID:1000)
- **本番 DB**: `docker-compose.prod.yml` で `DB_HOST`/`DB_PORT` を外部環境変数から注入
