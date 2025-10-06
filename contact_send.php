<?php
mb_language('Japanese');
mb_internal_encoding('UTF-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo 'Method Not Allowed';
    exit;
}

$name    = trim($_POST['name'] ?? '');
$email   = trim($_POST['email'] ?? '');
$type    = trim($_POST['type'] ?? '');
$message = trim($_POST['message'] ?? '');

if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $message === '') {
    http_response_code(400);
    echo '入力内容に不備があります。';
    exit;
}

$labels = [ 'b2b' => '業務用取引', 'recruit' => '採用', 'other' => 'その他' ];
$typeLabel = $labels[$type] ?? $type;

$to      = 'kyushu-hakataya@kyusyu-hakataya.com';
$subject = "お問い合わせ（{$typeLabel}）：{$name}";
$body    = "お名前: {$name}\nメール: {$email}\n区分: {$typeLabel}\n\n内容:\n{$message}\n";

$headers = [];
$headers[] = 'From: Kyusyu Hakataya <no-reply@kyusyu-hakataya.com>';
$headers[] = 'Reply-To: ' . $email;
$headers[] = 'Content-Type: text/plain; charset=UTF-8';

$ok = mb_send_mail($to, $subject, $body, implode("\r\n", $headers));

if ($ok) {
    header('Location: /contact.html?sent=1');
    exit;
}

http_response_code(500);
echo '送信に失敗しました。時間をおいて再度お試しください。';


