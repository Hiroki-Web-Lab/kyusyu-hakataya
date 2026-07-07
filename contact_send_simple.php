<?php
// セッション開始
session_start();

// ハニーポット（スパム対策：人間には見えない欄に入力があればボットと判定）
if (!empty($_POST['website'] ?? '')) {
    http_response_code(200);
    echo json_encode(['success' => true, 'message' => 'お問い合わせを受け付けました。']);
    exit;
}

// 基本的な入力値の取得と検証
$name = trim($_POST['name'] ?? '');
$kana = trim($_POST['kana'] ?? '');
$email = trim($_POST['email'] ?? '');
$tel = trim($_POST['tel'] ?? '');
$type = trim($_POST['type'] ?? '');
$message = trim($_POST['message'] ?? '');

// バリデーション
if (empty($name) || empty($email) || empty($message) || empty($type)) {
    http_response_code(400);
    echo json_encode(['error' => 'すべての必須項目を入力してください。']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['error' => '有効なメールアドレスを入力してください。']);
    exit;
}

// 区分のラベル
$labels = [
    'b2b' => '業務用取引',
    'recruit-full' => '採用応募（正社員）',
    'recruit-part' => '採用応募（パート・アルバイト）',
    'store' => '商品・店舗について',
    'other' => 'その他'
];
$typeLabel = $labels[$type] ?? $type;

// 管理者宛メール
$to = 'kyushu-hakataya@kyusyu-hakataya.com';
$subject = "【お問い合わせ】{$typeLabel} - {$name}様";
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

$headers = "From: {$email}\r\n";
$headers .= "Reply-To: {$email}\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

// メール送信
if (mail($to, $subject, $body, $headers)) {
    // 成功レスポンス
    http_response_code(200);
    echo json_encode(['success' => true, 'message' => 'お問い合わせを受け付けました。']);
} else {
    // エラーレスポンス
    http_response_code(500);
    echo json_encode(['error' => '送信に失敗しました。時間をおいて再度お試しください。']);
}
?>
