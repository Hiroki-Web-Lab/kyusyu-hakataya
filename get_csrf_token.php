<?php
// セッションCookieの属性（HTTPSのみ・JSから読めない・同一サイトのみ）
session_set_cookie_params(['path' => '/', 'secure' => true, 'httponly' => true, 'samesite' => 'Lax']);
session_start();

// CSRFトークンを生成
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// JSON形式でトークンを返す（キャッシュさせない）
header('Content-Type: application/json; charset=UTF-8');
header('Cache-Control: no-store');
echo json_encode(['csrf_token' => $_SESSION['csrf_token']]);
?>
