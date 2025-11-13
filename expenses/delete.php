<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = $_GET['id'];

// Xóa chi tiêu
$stmt = $pdo->prepare("DELETE FROM expenses WHERE id=? AND user_id=?");
$stmt->execute([$id, $_SESSION['user_id']]);

header("Location: index.php");
exit;
