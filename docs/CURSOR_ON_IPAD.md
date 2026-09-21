# iPadでCursorを使う方法

CursorはデスクトップアプリケーションのためiPadネイティブアプリはありませんが、以下の方法でiPadからCursorを使用できます。

## 方法1: リモートデスクトップ（推奨）

MacやWindows PCにCursorをインストールし、iPadからリモート接続する方法です。

### おすすめアプリ

| アプリ名 | 対応OS | 特徴 |
|---------|--------|------|
| **Jump Desktop** | Mac/Windows | 高品質、低遅延、Fluid技術対応 |
| **Screens 5** | Mac | Mac専用、美しいUI |
| **Microsoft Remote Desktop** | Windows | 無料、安定動作 |
| **Parsec** | Mac/Windows | ゲーミング向け低遅延 |

### 設定手順（Jump Desktop の例）

1. **PC側の準備**
   - Jump Desktop Connectをインストール
   - アカウントを作成してログイン
   - Cursorをインストール

2. **iPad側の準備**
   - App StoreからJump Desktopをインストール
   - 同じアカウントでログイン
   - PCに接続

3. **使用開始**
   - iPadからPCに接続
   - Cursorを起動して開発開始

### メリット
- ✅ フル機能のCursorが使える
- ✅ すべての拡張機能が利用可能
- ✅ AI機能（Copilot++、Chat）が完全動作

### デメリット
- ❌ インターネット接続が必要
- ❌ 常にPCを起動しておく必要がある
- ❌ ネットワーク遅延の影響を受ける

---

## 方法2: クラウド開発環境

クラウド上の開発環境をiPadのブラウザからアクセスする方法です。

### GitHub Codespaces

GitHubが提供するクラウド開発環境。VS Codeベースなので、Cursorに近い体験が得られます。

```
https://github.com/codespaces
```

**設定手順:**
1. GitHubにログイン
2. リポジトリページで「Code」→「Codespaces」タブ
3. 「Create codespace on main」をクリック
4. ブラウザでVS Codeが起動

### Gitpod

オープンソースのクラウド開発環境。

```
https://gitpod.io
```

**使い方:**
- GitHubリポジトリURLの前に `gitpod.io/#` を追加
- 例: `gitpod.io/#https://github.com/username/repo`

### メリット
- ✅ PCが不要
- ✅ どこからでもアクセス可能
- ✅ 環境構築が簡単

### デメリット
- ❌ CursorのAI機能は使えない（通常のVS Code機能のみ）
- ❌ 無料枠に制限がある
- ❌ オフライン作業不可

---

## 方法3: code-server（自前サーバー）

自分のサーバーでVS Codeをホストし、ブラウザからアクセスする方法です。

### インストール手順

```bash
# code-serverをインストール
curl -fsSL https://code-server.dev/install.sh | sh

# 起動
code-server

# または systemd で自動起動
sudo systemctl enable --now code-server@$USER
```

### 設定ファイル（~/.config/code-server/config.yaml）

```yaml
bind-addr: 0.0.0.0:8080
auth: password
password: your-secure-password
cert: false
```

### HTTPS化（推奨）

```bash
# Let's Encryptで証明書取得
sudo certbot certonly --standalone -d your-domain.com

# code-server設定を更新
cert: /etc/letsencrypt/live/your-domain.com/fullchain.pem
cert-key: /etc/letsencrypt/live/your-domain.com/privkey.pem
```

### メリット
- ✅ 完全なコントロール
- ✅ カスタマイズ自由
- ✅ 無料（サーバー代のみ）

### デメリット
- ❌ サーバー管理が必要
- ❌ セキュリティ設定が必要
- ❌ CursorのAI機能は使えない

---

## 方法4: Blink Shell + Mosh（ターミナル作業向け）

iPadでプロ向けターミナルアプリを使い、リモートサーバーでVim/Neovimを使う方法です。

### おすすめアプリ

- **Blink Shell** - 最高品質のターミナル、Mosh対応
- **Termius** - 美しいUI、複数サーバー管理
- **a]Shell** - 無料、基本機能充実

### 設定例（Blink Shell）

```bash
# サーバー側でMoshをインストール
sudo apt install mosh

# iPad から接続
mosh user@your-server.com
```

### Neovimセットアップ（Cursorライクな体験）

```bash
# Neovimインストール
sudo apt install neovim

# プラグインマネージャー（vim-plug）
sh -c 'curl -fLo "${XDG_DATA_HOME:-$HOME/.local/share}"/nvim/site/autoload/plug.vim --create-dirs \
       https://raw.githubusercontent.com/junegunn/vim-plug/master/plug.vim'
```

---

## iPad使用時のおすすめアクセサリ

### キーボード
- **Magic Keyboard for iPad** - トラックパッド付き、最高の打鍵感
- **Logitech Combo Touch** - コスパ良好、保護ケース兼用
- **Smart Keyboard Folio** - 薄型軽量

### スタンド
- **MOFT Float** - 角度調整自由
- **Twelve South Compass Pro** - 安定性抜群

---

## このプロジェクトでの推奨設定

このウェブサイトプロジェクト（株式会社九州博多屋）を iPad で開発する場合:

### 1. ローカル開発（リモートデスクトップ経由）

```bash
# PC側でプロジェクトを開く
cd /path/to/kyushu-hakataya
cursor .

# Live Serverを起動してプレビュー
# Cursor拡張機能「Live Server」をインストール
```

### 2. GitHub Codespaces での開発

1. このリポジトリをGitHubにプッシュ
2. Codespacesを起動
3. ターミナルで以下を実行:

```bash
# PHPビルトインサーバーを起動
php -S localhost:8000

# または Python の場合
python -m http.server 8000
```

### 3. ファイル構成

```
/workspace
├── index.html          # メインページ
├── contact.html        # お問い合わせページ
├── style.css           # スタイルシート
├── script.js           # JavaScript
├── config.php          # 設定ファイル
├── contact_send.php    # お問い合わせ処理
└── img/                # 画像フォルダ
```

---

## よくある質問

### Q: iPadだけで本格的な開発はできますか？
A: 可能ですが、リモートデスクトップやクラウド環境が必要です。ネイティブでの開発には限界があります。

### Q: CursorのAI機能をiPadで使えますか？
A: リモートデスクトップ経由なら完全に使えます。Codespaces等では使えません。

### Q: オフラインで作業できますか？
A: リモート接続が必要なため、基本的にオンライン環境が必要です。

### Q: M1/M2 iPadとIntel iPadで違いはありますか？
A: 処理能力の違いはありますが、リモート接続の場合は大きな差は出ません。

---

## まとめ

| 方法 | 難易度 | コスト | Cursor AI | オフライン |
|------|--------|--------|-----------|-----------|
| リモートデスクトップ | ★★☆ | 中 | ✅ | ❌ |
| GitHub Codespaces | ★☆☆ | 低〜中 | ❌ | ❌ |
| code-server | ★★★ | 低 | ❌ | ❌ |
| Blink + Mosh | ★★★ | 低 | ❌ | ❌ |

**おすすめ**: CursorのAI機能を活用したい場合は **リモートデスクトップ** が最適です。

---

最終更新: 2024年12月
