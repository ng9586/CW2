<?php
// login.php
session_start();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'];

    if ($email && $password) {
        $stmt = $pdo->prepare("SELECT id, password_hash FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            header('Location: profile.php');
            exit;
        } else {
            $error = "電郵或密碼錯誤";
        }
    } else {
        $error = "請輸入電郵和密碼";
    }
}
?>

<!DOCTYPE html>
<html lang="zh-HK">
<head><meta charset="UTF-8"><title>登入</title></head>
<body>
<h2>用戶登入</h2>
<?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
<form method="POST" action="">
    電郵: <input type="email" name="email" required><br>
    密碼: <input type="password" name="password" required><br>
    <button type="submit">登入</button>
</form>
<p>沒有帳戶？<a href="register.php">註冊</a></p>
</body>
</html>
