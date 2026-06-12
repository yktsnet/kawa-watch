## LocalStackの導入とTerraformの適用
id: 10
skill: pr-workflow
branch-slug: localstack-setup
github_issue:
status: open
type: feat
対象: docker-compose.yml (変更), terraform/provider.tf (新規), terraform/main.tf (変更), src/.env.docker (変更), src/config/queue.php (確認/変更必要なら)
内容: ローカルでのAWSエミュレーション環境としてLocalStackを導入し、Terraformを使用してSQSキューとS3バケットをLocalStack上にプロビジョニングし、Laravelアプリケーションから接続可能にする。
確認: docker-compose.ymlおよびTerraform定義のシンタックスチェック、Laravel接続設定の整合性目視確認。

---

## 実装仕様

### 1. docker-compose.yml への LocalStack 追加
- ローカル環境で SQS と S3 をエミュレートするため、`docker-compose.yml` に `localstack` サービスを追加する。
- 無料の Community版 (`localstack/localstack`) を使用し、ポート `4566` をホストへ転送する。
- 起動時に必要なAWS環境変数（`AWS_DEFAULT_REGION=ap-northeast-1`）を設定する。

### 2. Terraform の LocalStack 連携設定 (`terraform/provider.tf` 新規作成)
- AWSプロバイダーのエンドポイントを LocalStack (`http://localhost:4566`) に向ける設定を行う。
- ローカル実行時に本番AWSリソースを誤って操作しないよう、`provider "aws"` ブロック内の `endpoints` 設定で `sqs`, `s3`, `iam` 等のエンドポイントを `http://localhost:4566`（またはコンテナ内なら `http://localstack:4566`）にオーバーライドする。
- AWSのダミー認証情報 (`access_key="test"`, `secret_key="test"`, `skip_credentials_validation=true` など) を設定。

### 3. SQSキュー・S3バケットのプロビジョニング (`terraform/main.tf` 調整)
- すでに `main.tf` に定義されている SQS キュー（水位・気象イベント用）および S3 バケット（CSVアーカイブ用）が LocalStack 上に問題なく作成されるように確認・微調整する。
- 本番用の複雑なECSやALBなどのリソースが LocalStack Community版でエラーになる場合、ダミーリソースとして定義をスキップするか、またはローカル検証用のフラグ等でSQSとS3のみを作成するように `terraform/main.tf` を調整する。

### 4. Laravelの設定変更 (`src/.env.docker` の変更)
- AWS SQS および S3 のエンドポイントを LocalStack に向ける環境変数を設定。
  - `AWS_ENDPOINT=http://localstack:4566` （コンテナ間通信の場合）
  - `SQS_PREFIX=http://localstack:4566/000000000000` (LocalStackのデフォルトアカウントID)
  - `AWS_USE_PATH_STYLE_ENDPOINT=true` (S3のパススタイルエンドポイントを有効化)
- `src/config/queue.php` および `src/config/filesystems.php` で `AWS_ENDPOINT` などの環境変数を適切に読み込んでいるか確認・調整する。
