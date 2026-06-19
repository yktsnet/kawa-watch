## 変更内容
- ダッシュボード右上の「Verification Mode」ボタンを、青紫色の「Alert History」と区別しやすいよう、ニュートラルなダークグレー系アウトライン（`border-slate-600 text-slate-700 bg-white hover:bg-slate-50`）に変更しました。
- 検証画面（Verification.jsx）のカード幅がヘッダーの右端と完全に揃うよう、メインのグリッドおよび各カラムのカード要素に `w-full` を明示しました。

## 静的確認結果
- アセットビルド（`vite build`）が正常にビルドできることを確認しました。
- 変更ファイル一覧:
  - `src/resources/js/Pages/Dashboard.jsx`
  - `src/resources/js/Pages/Admin/Verification.jsx`

## 検証手順
1. 一般ダッシュボード画面を開き、右上の「Verification Mode」ボタンが青系ではなく、グレーアウトライン（背景白、枠線グレー）で表示されていることを確認します（「Alert History」ボタンの青紫色と明確に区別できていること）。
2. 「Verification Mode」ボタンをクリックして管理者検証パネルに遷移し、画面幅を縮小・拡大した際、3つのカラムカード（負荷シミュレーター、SQSキュー監視、DB書き込み）の右端が、ヘッダーのアクションボタン（ログアウトボタン）の右端と垂直に揃っていることを確認します。
