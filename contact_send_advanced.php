<?php
require_once 'vendor/autoload.php';
require_once 'config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// セッション開始
session_start();

// CSRF対策
if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die('CSRF token validation failed.');
}

// reCAPTCHA検証
$recaptcha_response = $_POST['g-recaptcha-response'] ?? '';
$recaptcha_secret = RECAPTCHA_SECRET_KEY;

$recaptcha_url = "https://www.google.com/recaptcha/api/siteverify";
$recaptcha_data = [
    'secret' => $recaptcha_secret,
    'response' => $recaptcha_response,
    'remoteip' => $_SERVER['REMOTE_ADDR']
];

$recaptcha_options = [
    'http' => [
        'header' => "Content-type: application/x-www-form-urlencoded\r\n",
        'method' => 'POST',
        'content' => http_build_query($recaptcha_data)
    ]
];

$recaptcha_context = stream_context_create($recaptcha_options);
$recaptcha_result = file_get_contents($recaptcha_url, false, $recaptcha_context);
$recaptcha_json = json_decode($recaptcha_result, true);

if (!$recaptcha_json['success'] || $recaptcha_json['score'] < 0.5) {
    die('reCAPTCHA verification failed.');
}

// 入力値の取得と検証
$name = htmlspecialchars($_POST['name'] ?? '', ENT_QUOTES, 'UTF-8');
$email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
$type = htmlspecialchars($_POST['type'] ?? '', ENT_QUOTES, 'UTF-8');
$message = htmlspecialchars($_POST['message'] ?? '', ENT_QUOTES, 'UTF-8');

if (!$email || empty($name) || empty($message)) {
    die('Invalid input data.');
}

// メール送信
try {
    $mail = new PHPMailer(true);
    
    // SMTP設定
    $mail->isSMTP();
    $mail->Host = SMTP_HOST;
    $mail->SMTPAuth = true;
    $mail->Username = SMTP_USERNAME;
    $mail->Password = SMTP_PASSWORD;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = SMTP_PORT;
    $mail->CharSet = 'UTF-8';
    
    // 管理者宛メール
    $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
    $mail->addAddress(ADMIN_EMAIL);
    $mail->addReplyTo($email, $name);
    
    $mail->isHTML(true);
    $mail->Subject = "【お問い合わせ】{$type} - {$name}様";
    
    $mail->Body = "
    <h2>お問い合わせ内容</h2>
    <p><strong>お名前:</strong> {$name}</p>
    <p><strong>メールアドレス:</strong> {$email}</p>
    <p><strong>お問い合わせ区分:</strong> {$type}</p>
    <p><strong>お問い合わせ内容:</strong></p>
    <p>{$message}</p>
    <hr>
    <p><small>送信日時: " . date('Y-m-d H:i:s') . "</small></p>
    ";
    
    $mail->send();
    
    // 自動返信メール
    $auto_reply = new PHPMailer(true);
    $auto_reply->isSMTP();
    $auto_reply->Host = SMTP_HOST;
    $auto_reply->SMTPAuth = true;
    $auto_reply->Username = SMTP_USERNAME;
    $auto_reply->Password = SMTP_PASSWORD;
    $auto_reply->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $auto_reply->Port = SMTP_PORT;
    $auto_reply->CharSet = 'UTF-8';
    
    $auto_reply->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
    $auto_reply->addAddress($email, $name);
    
    $auto_reply->isHTML(true);
    $auto_reply->Subject = "【自動返信】お問い合わせありがとうございます - 株式会社九州博多屋";
    
    $auto_reply->Body = "
    <h2>{$name}様</h2>
    <p>この度は、株式会社九州博多屋にお問い合わせいただき、誠にありがとうございます。</p>
    <p>以下の内容でお問い合わせを受け付けました。</p>
    <hr>
    <p><strong>お問い合わせ区分:</strong> {$type}</p>
    <p><strong>お問い合わせ内容:</strong></p>
    <p>{$message}</p>
    <hr>
    <p>内容を確認の上、担当者よりご連絡いたします。<br>
    お急ぎの場合は、お電話にてお問い合わせください。</p>
    <p>【お電話でのお問い合わせ】<br>
    本社　092-410-6454<br>
    松島店　092-612-0850<br>
    八田店　092-691-1898<br>
    古賀店　092-710-9132<br>
    鳥栖店　0942-83-8020<br>
    野芥店　092-863-1423</p>
    <p>※各店舗の電話は青果直通です</p>
    <hr>
    <p><small>株式会社九州博多屋<br>
    〒813-0019 福岡市東区みなと香椎3丁目1番1-211<br>
    TEL: 092-410-6454 / FAX: 092-410-6455</small></p>
    ";
    
    $auto_reply->send();
    
    // ログ記録
    $log_data = [
        date('Y-m-d H:i:s'),
        $name,
        $email,
        $type,
        substr($message, 0, 100) . '...',
        $_SERVER['REMOTE_ADDR']
    ];
    
    $log_file = fopen(LOG_FILE, 'a');
    if ($log_file) {
        fputcsv($log_file, $log_data);
        fclose($log_file);
    }
    
    // 成功ページへリダイレクト
    header('Location: contact.html?sent=1');
    exit;
    
} catch (Exception $e) {
    error_log("Mail sending failed: " . $e->getMessage());
    die('メール送信に失敗しました。しばらく時間をおいて再度お試しください。');
}
?>
