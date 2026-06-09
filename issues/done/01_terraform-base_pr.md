## 変更内容
PLAN.mdのPhase 1に基づく、VPC、RDS、SQS、S3の基本インフラの定義を行いました。
- VPC作成 (10.0.0.0/16) および Public/Private/Isolated サブネットの配置 (ap-northeast-1a, 1c)
- RDS MySQL 8.0 (db.t4g.micro) の構築 (マルチAZ無効)
- SQSメインキュー、DLQの構築
- S3バケット作成とパブリックアクセスブロックの有効化
- ECS用プレースホルダーSecurity Groupの作成

## 静的確認結果
- `terraform fmt` によるフォーマットを実行し修正済みです。
- `tflint` を実行し、問題がないことを確認しました。

対象ファイル:
- `terraform/main.tf` (新規)
- `terraform/variables.tf` (新規)
- `terraform/outputs.tf` (新規)

## 検証手順
以下の手順で Terraform のコード内容を確認してください。
1. `cd terraform`
2. `terraform init` (AWSプロファイルが設定されていること)
3. `terraform plan` を実行し、想定されたリソースが作成されることを確認してください。
   (※ 今回の Issue スコープでは、plan/applyの実行はスキップしています)
