<?php
session_start();
require '../config/db.php';

// Kiểm tra quyền admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit;
}

// Kiểm tra ID hợp lệ
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID không hợp lệ!");
}

$id = (int) $_GET['id'];

try {
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    header("Location: manage_users.php");
    exit;
} catch (PDOException $e) {
    echo "Lỗi khi xóa người dùng: " . $e->getMessage();
}
?>
