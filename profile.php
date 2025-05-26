<?php
// profile.php
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

// 讀取現有資料
$stmt = $pdo->prepare("SELECT * FROM user_profiles WHERE user_id = ?");
$stmt->execute([$user_id]);
$profile = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $chinese_name = sanitizeInput($_POST['chinese_name']);
    $english_name = sanitizeInput($_POST['english_name']);
    $gender = $_POST['gender'];
    $birth_date = $_POST['birth_date'];
    $address = sanitizeInput($_POST['address']);
    $birth_place = sanitizeInput($_POST['birth_place']);
    $occupation = sanitizeInput($_POST['occupation']);

    // 基本驗證
    if (empty($chinese_name) || empty($english_name) || !in_array($gender, ['M','F','O']) || empty($birth_date)) {
        $error = "請填寫所有必填欄位";
    } else {
        // 加密敏感資料
        $enc_chinese_name = encryptData($chinese_name);
        $enc_english_name = encryptData($english_name);
        $enc_address = encryptData($address);
        $enc_birth_place = encryptData($birth_place);
        $enc_occupation = encryptData($occupation);

        if ($profile) {
            // 更新
            $stmt = $pdo->prepare("UPDATE user_profiles SET chinese_name=?, english_name=?, gender=?, birth_date=?, address=?, birth_place=?, occupation=? WHERE user_id=?");
            $stmt->execute([$enc_chinese_name, $enc_english_name, $gender, $birth_date, $enc_address, $enc_birth_place, $enc_occupation, $user_id]);
        } else {
            // 新增
            $stmt = $pdo->prepare("INSERT INTO user_profiles (user_id, chinese_name, english_name, gender, birth_date, address, birth_place, occupation) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$user_id, $enc_chinese_name, $enc_english_name, $gender, $birth_date, $enc_address, $enc_birth_place, $enc_occupation]);
        }
        $success = "個人資料已保存";
        // 重新讀取
        $stmt = $pdo->prepare("SELECT * FROM user_profiles WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $profile = $stmt->fetch();
    }
}

// 解密資料供顯示
function decryptOrEmpty($data) {
    return $data ? decryptData($data) : '';
}

?>

<!DOCTYPE html>
<html lang="zh-HK">
<head><meta charset="UTF-8"><title>個人資料</title></head>
<body>
<h2>填寫個人資料</h2>
<?php
if ($error) echo "<p style='color:red;'>$error</p>";
if ($success) echo "<p style='color:green;'>$success</p>";
?>
<form method="POST" action="">
    中文姓名: <input type="text" name="chinese_name" required value="<?= decryptOrEmpty($profile['chinese_name'] ?? '') ?>"><br>
    英文姓名: <input type="text" name="english_name" required value="<?= decryptOrEmpty($profile['english_name'] ?? '') ?>"><br>
    性別:
    <select name="gender" required>
        <option value="M" <?= (isset($profile['gender']) && $profile['gender']=='M')?'selected':'' ?>>男</option>
        <option value="F" <?= (isset($profile['gender']) && $profile['gender']=='F')?'selected':'' ?>>女</option>
        <option value="O" <?= (isset($profile['gender']) && $profile['gender']=='O')?'selected':'' ?>>其他</option>
    </select><br>
    出生日期: <input type="date" name="birth_date" required value="<?= $profile['birth_date'] ?? '' ?>"><br>
    地址: <input type="text" name="address" value="<?= decryptOrEmpty($profile['address'] ?? '') ?>"><br>
    出生地: <input type="text" name="birth_place" value="<?= decryptOrEmpty($profile['birth_place'] ?? '') ?>"><br>
    職業: <input type="text" name="occupation" value="<?= decryptOrEmpty($profile['occupation'] ?? '') ?>"><br>
    <button type="submit">保存</button>
</form>

<p><a href="appointment.php">預約申請/更換身分證</a></p>
<p><a href="logout.php">登出</a></p>
</body>
</html>
