<?php
$host = 'localhost';
$db   = 'expense_db'; // database bạn đã tạo
$user = 'root';
$pass = ''; // nếu có mật khẩu MySQL thì nhập vào đây

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Kết nối thất bại: " . $e->getMessage());
}
?>
