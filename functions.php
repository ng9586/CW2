<?php
// functions.php
require_once 'config.php';

function encryptData($plaintext) {
    $iv = openssl_random_pseudo_bytes(IV_LENGTH);
    $ciphertext = openssl_encrypt($plaintext, 'aes-256-cbc', ENCRYPTION_KEY, 0, $iv);
    // 儲存格式：iv + ciphertext (base64)
    return base64_encode($iv . $ciphertext);
}

function decryptData($encrypted) {
    $data = base64_decode($encrypted);
    $iv = substr($data, 0, IV_LENGTH);
    $ciphertext = substr($data, IV_LENGTH);
    return openssl_decrypt($ciphertext, 'aes-256-cbc', ENCRYPTION_KEY, 0, $iv);
}

function sanitizeInput($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}
?>
