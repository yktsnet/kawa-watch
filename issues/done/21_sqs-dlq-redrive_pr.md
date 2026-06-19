## 変更内容
- デッドレターキュー（DLQ）に滞留したメッセージを自動で解析し、各メインキューへ再投入（Redrive）するロジックを `SqsQueueService.php` へ実装しました。メッセージボディ内のキー判定により、水位データは水位キュー、気象データは気象キューへ自動的に振り分けられます。
- 再投入APIを処理するコントローラーアクション `redriveDlq` を `VerificationController.php` へ実装し、ルート定義 `POST /admin/api/dlq-redrive` を `web.php` へ追加しました。
- 管理者検証パネル（Verification.jsx）のDLQキュー監視カード内に、「メッセージを再投入 (Redrive) する」ボタンを追加しました。ボタンはDLQに滞留メッセージがある時のみ活性化し、再投入が成功すると即座にメトリクスが更新されるようにしました。

## 静的確認結果
- PHP側のシンタックスエラーがないことを `php -l` を用いて確認しました。
- Viteによるアセットビルド（`vite build`）が正常に終了することを確認しました。
- 変更ファイル一覧:
  - `src/app/Services/SqsQueueService.php`
  - `src/app/Http/Controllers/Admin/VerificationController.php`
  - `src/routes/web.php`
  - `src/resources/js/Pages/Admin/Verification.jsx`

## 検証手順
1. SQSのDLQバケットにテストメッセージ（ダミーの水位データ `{ "station_code": "ST001", "observed_at": "2026-06-19 12:00:00", "level_m": 2.5 }` または気象データ）を投入し、検証パネルの「デッドレターキュー (DLQ)」に「滞留メッセージ: N 件」と表示され、再投入ボタンが活性化していることを確認します。
2. 「メッセージを再投入 (Redrive) する」ボタンをクリックし、ボタンが「再投入中...」のローディング状態になることを確認します。
3. 処理完了後、成功メッセージ（`N件のメッセージを再投入しました。`）が表示され、DLQの滞留件数が減少し、対応するメインキューの Pending 件数が増加することを確認します。
