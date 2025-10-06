<?php
// Xserver SMTP設定
define('SMTP_HOST', 'sv1237.xserver.jp'); // XserverのSMTPサーバー
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'kyushu-hakataya@kyusyu-hakataya.com'); // あなたのメールアドレス
define('SMTP_PASSWORD', 'your-email-password'); // メールパスワード
define('SMTP_FROM_EMAIL', 'kyushu-hakataya@kyusyu-hakataya.com');
define('SMTP_FROM_NAME', '株式会社九州博多屋');

// reCAPTCHA設定
define('RECAPTCHA_SITE_KEY', 'your-recaptcha-site-key'); // Google reCAPTCHAで取得
define('RECAPTCHA_SECRET_KEY', 'your-recaptcha-secret-key'); // Google reCAPTCHAで取得

// 管理者メール
define('ADMIN_EMAIL', 'kyushu-hakataya@kyusyu-hakataya.com');

// ログファイル
define('LOG_FILE', 'contact_log.csv');
?>
