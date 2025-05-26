<?php
// register.php
session_start();
require_once 'db.php';
require_once 'functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];

    if (!$email) {
        $error = "請輸入有效電郵地址";
    } elseif ($password !== $password_confirm) {
        $error = "密碼與確認密碼不符";
    } elseif (strlen($password) < 8) {
        $error = "密碼至少8個字元";
    } else {
        // 檢查電郵是否已存在
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = "此電郵已被註冊";
        } else {
            // 密碼雜湊
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (email, password_hash) VALUES (?, ?)");
            $stmt->execute([$email, $password_hash]);
            $_SESSION['user_id'] = $pdo->lastInsertId();
            header('Location: profile.php');
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="zh-HK">
<head><meta charset="UTF-8"><title>註冊</title></head>
<body>
<h2>用戶註冊</h2>
<?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
<form method="POST" action="">
    電郵: <input type="email" name="email" required><br>
    密碼: <input type="password" name="password" required><br>
    確認密碼: <input type="password" name="password_confirm" required><br>
    <button type="submit">註冊</button>
</form>
<p>已有帳戶？<a href="login.php">登入</a></p>
</body>
</html>
