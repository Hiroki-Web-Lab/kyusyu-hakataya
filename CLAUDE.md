# 九州博多屋HP

kyusyu-hakataya.com の会社HP（静的 HTML/CSS/JS + PHP フォーム）。GitHub `Hiroki-Web-Lab/kyusyu-hakataya` で Git 管理。
正本はこのリポジトリ。Dropbox 側の旧フォルダは編集しない。

## Git 運用ルール（全端末共通）

手順の詳細は Vault の `devsession` スキル **§8 軽量経路 lite**（`{VAULT}/.claude/skills/devsession/SKILL.md`・projects.conf の `hakataya-hp`）に定義。作業開始時は `bash {VAULT}/.claude/skills/devsession/scripts/session-start.sh hakataya-hp`、完了時は commit/push 後に `session-finish.sh hakataya-hp` で exit 0 を確認する。clone がない端末では勝手に clone せず、clone してよいか確認してから始める。

- **作業開始時**: `git pull --ff-only --autostash` を実行する（`.claude/settings.json` の SessionStart hook で自動実行される。失敗していたら手動で実行し、競合があれば解消してから作業する）
- **作業終了時**: 依頼された作業が完了したら、必ず以下を実行して GitHub に反映する。この操作はユーザーから事前に承認済みなので、確認せず実行してよい
  ```bash
  git add -A && git commit -m "<変更内容を日本語で簡潔に>" && git push
  ```
- コミットメッセージは変更内容が分かる1行にする（例: `index.html: 営業時間を更新`）
- push が拒否された場合（他端末の変更が先にある）は `git pull --rebase` してから再 push する
- `git push --force` や `git reset --hard` は使わない
- 改行コードは `.gitattributes`（`* text=auto eol=lf`）で LF に統一している。Windows で編集しても commit 時に LF に戻る

## ログ設定

`{VAULT}` は Vault の `_System/devsession/machines.conf` を `hostname` で引いて確定する（main-win: `D:\Hiroki\Hiroki_Obsidian_Main` / macbook・macmini: `/Users/hiroki/Hiroki_Obsidian_Main` / office-win: `C:\Users\Hiroki\Hiroki_Obsidian_Main`）。

- `DAILY_PATH`: `{VAULT}/01_Daily/YYYY/YYYY-MM/YYYY-MM-DD.md`（当日日付で置換）。Daily 本文には直接追記せず `{VAULT}/01_Daily/YYYY/YYYY-MM/_logs/` のセッションログに書く
- `LOG_PATH`: `{VAULT}/03_Projects/九州博多屋/` 配下に `YYYY-MM-DD_九州博多屋HP_<作業名>_作業ログ.md` を作成
- セッション番号は同フォルダの会社HP／九州博多屋HP の最新ログから採番（直近: セッション4・2026-09-14 Git 移行）

## プレビュー

`.claude/launch.json` の `static-server`（8090・このリポジトリ）と `lp-server`（8091・`../良菜果LP`）。
