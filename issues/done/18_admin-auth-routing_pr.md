## 変更内容
- `DatabaseSeeder.php` にデモ用管理者アカウント（`admin@example.com` / `password`）を自動生成する Seeding 処理を追加しました。
- `User` モデル（Authenticatable）と `users` テーブルのマイグレーションファイルを新規作成しました。
- ログイン、認証（セッション）、ログアウトを制御する `LoginController.php` を新規作成しました。
- Tailwind v4 を用いたログイン画面 `Login.jsx` を新規作成しました。デモ用アカウント情報を画面上に明示しています。
- ログイン後にリダイレクトされる管理者専用の仮検証画面 `Verification.jsx` を新規作成しました。
- `web.php` を編集し、ゲスト用のログインルートと、`auth` ミドルウェアで保護された `/admin/*` のルートグループを追加しました。
- 一般ダッシュボード `Dashboard.jsx` に、ログイン状態に応じて「管理者ログイン（Admin Login）」と「検証モード（Verification Mode）」が動的に切り替わるナビゲーションリンクを追加しました。
- 環境変数 `AWS_SQS_DLQ_QUEUE_URL` を `.env`, `.env.example`, `.env.docker` に追加しました。

## 静的確認結果
- 作成・変更した全ての PHP ファイル（コントローラー、モデル、マイグレーション、シード、ルート）に対し、`php -l` を実行し構文エラーがないことを確認しました。
- フロントエンドアセット（React/Inertia/Tailwind）について、`npm run build` を実行してエラーなく正常に Vite ビルド（本番ビルド）が通ることを検証しました。

## 検証手順
user様は以下の手順で動作をご確認いただけます。

1. **データベースのマイグレーションとシード実行**：
   ```bash
   # Docker環境の場合
   docker compose exec app php artisan migrate
   docker compose exec app php artisan db:seed --class=DatabaseSeeder

   # WSLローカル環境の場合
   php artisan migrate
   php artisan db:seed
   ```
2. **アセットのビルド（必要に応じて）**：
   ```bash
   npm run build
   ```
3. **動作確認**：
   - ブラウザでトップ画面（`/`）を開き、ヘッダーに「Admin Login」のボタンが表示されていることを確認します。
   - 「Admin Login」をクリックしてログイン画面（`/login`）へ遷移します。
   - 画面に表示されているデモアカウント（`admin@example.com` / `password`）を入力してログインします。
   - ログイン成功後、管理者専用画面（`/admin/verification`）へ正常にリダイレクトされ、一般ダッシュボードに戻った際もヘッダーが「Verification Mode」に切り替わっていることを確認します。
   - ログアウトが正常に機能し、一般画面に戻ることを確認します。
   - 未ログイン状態で直接 `/admin/verification` にアクセスした際、自動的に `/login` へリダイレクトされる（ミドルウェア保護）ことを確認します。
