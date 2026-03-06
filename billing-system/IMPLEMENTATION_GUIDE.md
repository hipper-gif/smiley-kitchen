# Smiley配食システム - 新しいディレクトリ構成 実装ガイド

## 📁 新しいディレクトリ構成

```
/Smiley/meal-delivery/
├── common/                       # 共通ファイル（新規作成）
│   ├── bootstrap.php            ✅ 作成済み
│   ├── config/
│   │   └── database.php         ✅ コピー済み
│   └── classes/
│       ├── SecurityHelper.php   ✅ コピー済み
│       └── AuthManager.php      ✅ コピー済み
│
├── billing-system/              # 既存システム（変更なし）
│   └── ... (すべてそのまま)
│
└── order/                       # 新規注文システム（利用者側）
    ├── index.php               # ランディングページ
    ├── signup.php              # 初回登録
    ├── login.php              # ログイン
    ├── pages/
    │   └── dashboard.php      # 注文ダッシュボード（今後）
    └── api/
        ├── signup_api.php     # 企業登録API
        ├── login_api.php      # ログインAPI
        └── logout_api.php     # ログアウトAPI
```

## 🚀 サーバーへのアップロード手順

### 1. GitHubから最新コードを取得

ブランチ: `claude/order-system-phase1-017U6tq3xc6x3wXUNH8Ck5Am`

以下のファイルをダウンロード:

**共通ファイル:**
```
https://github.com/hipper-gif/billing-system/tree/claude/order-system-phase1-017U6tq3xc6x3wXUNH8Ck5Am/common
```

**注文システムファイル:**
```
https://github.com/hipper-gif/billing-system/tree/claude/order-system-phase1-017U6tq3xc6x3wXUNH8Ck5Am/order
```

### 2. サーバー上のディレクトリ構成

Xserverの `public_html/Smiley/meal-delivery/smiley-kitchen/` に以下をアップロード:

```
public_html/
└── Smiley/
    └── meal-delivery/
        └── smiley-kitchen/
            ├── common/              ← 新規フォルダ作成
            │   ├── bootstrap.php
            │   ├── config/
            │   └── classes/
            │
            ├── billing-system/      ← 既存
            │   └── ...
            │
            └── order/               ← 新規フォルダ作成
            ├── index.php
            ├── signup.php
            ├── login.php
            ├── pages/
            └── api/
```

### 3. データベースマイグレーション

phpMyAdminで以下のSQLを実行（既に実行済み）:
```sql
-- companiesテーブルとusersテーブルの拡張は完了済み
```

### 4. URL構成

**お客様向け（新規）:**
- ランディングページ: `https://twinklemark.xsrv.jp/Smiley/meal-delivery/smiley-kitchen/order/index.php`
- 企業登録: `https://twinklemark.xsrv.jp/Smiley/meal-delivery/smiley-kitchen/order/signup.php`
- ログイン: `https://twinklemark.xsrv.jp/Smiley/meal-delivery/smiley-kitchen/order/login.php`

**管理者向け（既存・変更なし）:**
- ダッシュボード: `https://twinklemark.xsrv.jp/Smiley/meal-delivery/smiley-kitchen/billing-system/index.php`

## ✅ 実装のメリット

1. **既存システム保護**
   - billing-system/ は一切変更なし
   - 既存機能に影響ゼロ

2. **新規システム独立**
   - order/ で独立開発
   - テストが容易

3. **データ連携**
   - 同一データベース使用
   - 共通クラス使用（common/）
   - リアルタイム同期

4. **保守性向上**
   - 共通ファイルの一元管理
   - 修正が両方に反映

## 📝 次のステップ

1. ✅ 共通ディレクトリ作成
2. ✅ bootstrap.php作成
3. 🔄 GitHubにプッシュ（進行中）
4. ⏳ サーバーにアップロード
5. ⏳ 動作確認
