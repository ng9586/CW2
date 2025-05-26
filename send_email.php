<?php
// send_email.php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // 請先用 Composer 安裝 PHPMailer

require_once 'config.php';

function sendAppointmentReminder($pdo, $user_id, $appointment_date, $time_slot, $location) {
    // 讀取用戶電郵
    $stmt = $pdo->prepare("SELECT email FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
    if (!$user) return false;

    $to = $user['email'];

    $mail = new PHPMailer(true);
    try {
        //郵件伺服器設定
        $mail->isSMTP();
        $mail->Host = MAIL_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = MAIL_USERNAME;
        $mail->Password = MAIL_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom(MAIL_FROM, MAIL_FROM_NAME);
        $mail->addAddress($to);

        $mail->isHTML(true);
        $mail->Subject = '香港身分證預約提醒';
        $mail->Body = "
            <p>親愛的用戶，您好！</p>
            <p>您已成功預約香港身分證申請/更換服務，預約詳情如下：</p>
            <ul>
                <li>日期：$appointment_date</li>
                <li>時間段：$time_slot</li>
                <li>地點：$location</li>
            </ul>
            <p>請於預約當日攜帶相關證件前往辦理。</p>
            <p>謝謝！</p>
        ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("郵件發送失敗: {$mail->ErrorInfo}");
        return false;
    }
}
?>
