<?php
mb_internal_encoding('UTF-8');

header('Content-Type: application/json; charset=UTF-8');

// POST以外は拒否（セッションは開始しない）
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => '不正なリクエストです。']);
    exit;
}

// セッション開始（Cookieは HTTPSのみ・JSから読めない・同一サイトのみ）
session_set_cookie_params(['path' => '/', 'secure' => true, 'httponly' => true, 'samesite' => 'Lax']);
session_start();

// 同一サイトからの送信かを確認（CSRF対策）
$allowedHosts = ['kyusyu-hakataya.com', 'www.kyusyu-hakataya.com'];
$source = $_SERVER['HTTP_ORIGIN'] ?? ($_SERVER['HTTP_REFERER'] ?? '');
$sourceHost = $source !== '' ? parse_url($source, PHP_URL_HOST) : '';
if (!in_array($sourceHost, $allowedHosts, true)) {
    http_response_code(403);
    echo json_encode(['error' => '不正なリクエストです。']);
    exit;
}

// CSRFトークンの検証
$token = $_POST['csrf_token'] ?? '';
if (empty($_SESSION['csrf_token']) || !is_string($token)
    || !hash_equals($_SESSION['csrf_token'], $token)) {
    http_response_code(403);
    echo json_encode(['error' => 'ページの有効期限が切れました。再読み込みのうえ、再度お試しください。']);
    exit;
}

// 連続送信の制限（同一セッション・同一IPから60秒に1通まで）
$rateFile = sys_get_temp_dir() . '/kyusyu_hakataya_contact_' . hash('sha256', $_SERVER['REMOTE_ADDR'] ?? 'unknown');
if ((isset($_SESSION['last_contact_sent']) && (time() - $_SESSION['last_contact_sent']) < 60)
    || (is_file($rateFile) && (time() - filemtime($rateFile)) < 60)) {
    http_response_code(429);
    echo json_encode(['error' => '送信間隔が短すぎます。しばらく時間をおいて再度お試しください。']);
    exit;
}

// ハニーポット（スパム対策：人間には見えない欄に入力があればボットと判定）
if (!empty($_POST['website'] ?? '')) {
    http_response_code(200);
    echo json_encode(['success' => true, 'message' => 'お問い合わせを受け付けました。']);
    exit;
}

// 基本的な入力値の取得（配列などの文字列以外は空として扱う）
$input = function (string $key): string {
    $value = $_POST[$key] ?? '';
    return is_string($value) ? trim($value) : '';
};
$name = $input('name');
$kana = $input('kana');
$email = $input('email');
$tel = $input('tel');
$type = $input('type');
$message = $input('message');

// メールヘッダー・件名に入る値から改行を除去（ヘッダーインジェクション対策）
$name = str_replace(["\r", "\n"], '', $name);
$kana = str_replace(["\r", "\n"], '', $kana);
$email = str_replace(["\r", "\n"], '', $email);
$tel = str_replace(["\r", "\n"], '', $tel);

// バリデーション
if ($name === '' || $email === '' || $message === '' || $type === '') {
    http_response_code(400);
    echo json_encode(['error' => 'すべての必須項目を入力してください。']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['error' => '有効なメールアドレスを入力してください。']);
    exit;
}

// 入力長の制限（迷惑投稿対策）
if (mb_strlen($name) > 100 || mb_strlen($kana) > 100
    || mb_strlen($email) > 254 || mb_strlen($tel) > 50 || mb_strlen($message) > 5000) {
    http_response_code(400);
    echo json_encode(['error' => '入力内容が長すぎます。']);
    exit;
}

// 区分のラベル（一覧にない値は受け付けない）
$labels = [
    'b2b' => '業務用取引',
    'recruit-full' => '採用応募（正社員）',
    'recruit-part' => '採用応募（パート・アルバイト）',
    'store' => '商品・店舗について',
    'other' => 'その他'
];
if (!isset($labels[$type])) {
    http_response_code(400);
    echo json_encode(['error' => 'お問い合わせ区分を正しく選択してください。']);
    exit;
}
$typeLabel = $labels[$type];

// 管理者宛メール
$to = 'kyushu-hakataya@kyusyu-hakataya.com';
// 件名は日本語を含むため MIME エンコードする
$subject = mb_encode_mimeheader("【お問い合わせ】{$typeLabel} - {$name}様", 'UTF-8', 'B');
$body = "お問い合わせ内容\n\n";
$body .= "お名前: {$name}\n";
if ($kana !== '') {
    $body .= "ふりがな: {$kana}\n";
}
$body .= "メールアドレス: {$email}\n";
if ($tel !== '') {
    $body .= "電話番号: {$tel}\n";
}
$body .= "お問い合わせ区分: {$typeLabel}\n";
$body .= "お問い合わせ内容:\n{$message}\n\n";
$body .= "送信日時: " . date('Y-m-d H:i:s') . "\n";
$body .= "送信元IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown') . "\n";

// Fromは自ドメイン（なりすまし扱い・迷惑メール判定の回避）、Reply-Toに送信者を設定
$headers = "From: noreply@kyusyu-hakataya.com\r\n";
$headers .= "Reply-To: {$email}\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

// メール送信（第5引数でエンベロープ送信者を明示）
if (mail($to, $subject, $body, $headers, '-f noreply@kyusyu-hakataya.com')) {
    // 成功レスポンス
    $_SESSION['last_contact_sent'] = time();
    @touch($rateFile);
    http_response_code(200);
    echo json_encode(['success' => true, 'message' => 'お問い合わせを受け付けました。']);
} else {
    // エラーレスポンス
    http_response_code(500);
    echo json_encode(['error' => '送信に失敗しました。時間をおいて再度お試しください。']);
}
