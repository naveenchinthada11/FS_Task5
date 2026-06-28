<?php
session_start();

$host = 'localhost';
$db   = 'capstone';  // (or whatever you named it in step 2)
$user = 'root';
$pass = '';  // (XAMPP default is empty)

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}

function flash($type, $message)
{
    $_SESSION['flash'][$type] = $message;
}

function get_flash()
{
    $flash = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $flash;
}

function is_logged_in()
{
    return !empty($_SESSION['user_id']);
}

function current_user()
{
    global $pdo;
    if (!is_logged_in()) {
        return null;
    }

    $stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}

function require_login()
{
    if (!is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}

function require_admin()
{
    $user = current_user();
    if (!$user || $user['role'] !== 'admin') {
        header('Location: login.php');
        exit;
    }
}

function send_otp($email)
{
    global $pdo;

    $code = random_int(100000, 999999);
    $stmt = $pdo->prepare('DELETE FROM otps WHERE email = ?');
    $stmt->execute([$email]);

    $stmt = $pdo->prepare('INSERT INTO otps (email, code, expires_at) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 10 MINUTE))');
    $stmt->execute([$email, $code]);

    $subject = 'Capstone Portal OTP Verification';
    $message = "Your verification code is: $code\nThis code expires in 10 minutes.";
    $headers = 'From: no-reply@localhost';

    $sent = mail($email, $subject, $message, $headers);
    return ['code' => $code, 'sent' => $sent];
}
