<?php
// config.php

// 資料庫設定
define('DB_HOST', 'localhost');
define('DB_NAME', 'hk_id_appointment');
define('DB_USER', 'your_db_user');
define('DB_PASS', 'your_db_password');

// OpenSSL 加密金鑰 (32 bytes for AES-256)
define('ENCRYPTION_KEY', 'your-32-byte-long-random-key-here-123456');

// 用於加密的初始化向量長度
define('IV_LENGTH', openssl_cipher_iv_length('aes-256-cbc'));

// 郵件設定 (用於 PHPMailer)
define('MAIL_HOST', 'smtp.example.com');
define('MAIL_USERNAME', 'your_email@example.com');
define('MAIL_PASSWORD', 'your_email_password');
define('MAIL_FROM', 'your_email@example.com');
define('MAIL_FROM_NAME', 'HK ID Appointment System');
?>
