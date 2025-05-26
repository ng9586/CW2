<?php
// appointment.php
session_start();
require_once 'db.php';
require_once 'functions.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

// 假設可選日期、時間段、地點（可改成從DB讀取）
$available_dates = [];
for ($i=1; $i<=30; $i++) {
    $available_dates[] = date('Y-m-d', strtotime("+$i days"));
}
$time_slots = ['09:00-11:00', '11:00-13:00', '14:00-16:00', '16:00-18:00'];
$locations = ['中環', '旺角', '灣仔', '九龍城'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $appointment_date = $_POST['appointment_date'];
    $time_slot = $_POST['time_slot'];
    $location = sanitizeInput($_POST['location']);

    if (!in_array($appointment_date, $available_dates)) {
        $error = "選擇的日期不可用";
    } elseif (!in_array($time_slot, $time_slots)) {
        $error = "選擇的時間段不可用";
    } elseif (!in_array($location, $locations)) {
        $error = "選擇的地點不可用";
    } else {
        // 檢查是否已預約
        $stmt = $pdo->prepare("SELECT * FROM appointments WHERE user_id = ?");
        $stmt->execute([$user_id]);
        if ($stmt->fetch()) {
            // 更新預約
            $stmt = $pdo->prepare("UPDATE appointments SET appointment_date=?, time_slot=?, location=? WHERE user_id=?");
            $stmt->execute([$appointment_date, $time_slot, $location, $user_id]);
        } else {
            // 新增預約
            $stmt = $pdo->prepare("INSERT INTO appointments (user_id, appointment_date, time_slot, location) VALUES (?, ?, ?, ?)");
            $stmt->execute([$user_id, $appointment_date, $time_slot, $location]);
        }
        $success = "預約成功！系統會在預約前2天發送提醒電郵。";

        // 發送郵件提醒（模擬，實際應用用排程或cron）
        require 'send_email.php';
        sendAppointmentReminder($pdo, $user_id, $appointment_date, $time_slot, $location);
    }
}

// 讀取現有預約
$stmt = $pdo->prepare("SELECT * FROM appointments WHERE user_id = ?");
$stmt->execute([$user_id]);
$appointment = $stmt->fetch();

?>

<!DOCTYPE html>
<html lang="zh-HK">
<head><meta charset="UTF-8"><title>預約申請/更換身分證</title></head>
<body>
<h2>預約申請/更換香港身分證</h2>
<?php
if ($error) echo "<p style='color:red;'>$error</p>";
if ($success) echo "<p style='color:green;'>$success</p>";
?>
<form method="POST" action="">
    預約日期:
    <select name="appointment_date" required>
        <?php foreach ($available_dates as $date): ?>
            <option value="<?= $date ?>" <?= ($appointment && $appointment['appointment_date'] == $date) ? 'selected' : '' ?>><?= $date ?></option>
        <?php endforeach; ?>
    </select><br>
    時間段:
    <select name="time_slot" required>
        <?php foreach ($time_slots as $slot): ?>
            <option value="<?= $slot ?>" <?= ($appointment && $appointment['time_slot'] == $slot) ? 'selected' : '' ?>><?= $slot ?></option>
        <?php endforeach; ?>
    </select><br>
    地點:
    <select name="location" required>
        <?php foreach ($locations as $loc): ?>
            <option value="<?= $loc ?>" <?= ($appointment && $appointment['location'] == $loc) ? 'selected' : '' ?>><?= $loc ?></option>
        <?php endforeach; ?>
    </select><br>
    <button type="submit">確認預約</button>
</form>

<p><a href="profile.php">返回個人資料</a></p>
<p><a href="logout.php">登出</a></p>
</body>
</html>
