## 管理者認証機能の実装とルーティングの保護
id: 18
skill: pr-workflow
branch-slug: admin-auth-routing
github_issue:
status: close
type: feat
対象: src/database/seeders/DatabaseSeeder.php (変更), src/app/Http/Controllers/Auth/LoginController.php (新規), src/resources/js/Pages/Auth/Login.jsx (新規), src/routes/web.php (変更)
内容: 一般公開ダッシュボードと管理者専用エリアを分離し、セッションベースの認証を導入する。デモ用の管理者アカウントをデータベースに Seeding し、ログイン画面 (Login.jsx) およびログイン/ログアウトを司る LoginController を新規作成する。また、/admin/ 以下のルートを Laravel の auth ミドルウェアで保護し、未ログイン時は /login にリダイレクトするようルーティングを定義する。
確認: 
  - 構文エラーがなく、正常にアセットビルドが通ること。
  - `php -l` による新規作成/変更された PHP ファイルのシンタックスチェックをパスすること。
  - 【超重要】PR作成前に、必ずPR本文と同じ内容を `issues/done/18_admin-auth-routing_pr.md` に新規ファイルとして書き出し、実装コードと同一のコミットに含めること。この控えファイルの作成を絶対に失念しないこと。

---

## 実装仕様

### 1. デモ用管理者アカウントの Seeding
- **`src/database/seeders/DatabaseSeeder.php`** [MODIFY]:
  - `User` モデル（標準で `name`, `email`, `password` 等を持つもの）に対して、デモ用の管理者ユーザーを追加します。
  - 例: `name: Admin`, `email: admin@example.com`, `password: Hash::make('password')`。既に同じメールアドレスが存在する場合は追加しないように考慮します（`updateOrCreate` など）。

### 2. ログインコントローラーの作成
- **`src/app/Http/Controllers/Auth/LoginController.php`** [NEW]:
  - ログイン画面の表示 (`showLoginForm`)、ログイン認証処理 (`login`)、ログアウト処理 (`logout`) を実装します。
  - `showLoginForm` は `Inertia::render('Auth/Login')` を返します。
  - `login` では、`Auth::attempt($credentials, $remember)` を用いて認証を行い、成功時は `/admin/verification`（検証モード画面 ※次Issueで作成するが、ルートのみ定義）へリダイレクトし、失敗時はエラーメッセージを伴って戻します。
  - `logout` では、`Auth::logout()`、`$request->session()->invalidate()`、および `$request->session()->regenerateToken()` を行ってトップページへリダイレクトします。

### 3. ログイン画面の作成
- **`src/resources/js/Pages/Auth/Login.jsx`** [NEW]:
  - React + Inertia 用のログインフォーム画面を実装します。
  - Tailwind v4 を使用してシンプルかつ美麗なログイン画面をスタイリングします。
  - 評価者がすぐにログインできるように、デモ用アカウント情報（`admin@example.com` / `password`）をフォームの近くにプレースホルダーや注記として明記します。
  - 認証エラー発生時は、コントローラーから渡されるエラーメッセージを画面上に赤文字などで分かりやすく表示します。

### 4. ルーティングの定義と保護
- **`src/routes/web.php`** [MODIFY]:
  - `/login` に対する GET/POST ルート（`LoginController` のアクション）を定義します。これらはゲスト（未ログイン）のみアクセス可能（`guest` ミドルウェア）とします。
  - `/logout` に対する POST ルート（または GET/POST ルート）を定義します。
  - `/admin/` 以下の管理者専用ルートグループを定義し、Laravel 標準の `auth` ミドルウェアを適用して保護します。
  - 一時的に、次Issueで実装する検証画面のダミーとなる GET `/admin/verification` を定義し、ログイン成功時にここへアクセスできることを確認可能にします。
