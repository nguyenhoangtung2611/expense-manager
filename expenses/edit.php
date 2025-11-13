<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = $_GET['id'];

// Lấy dữ liệu chi tiêu
$stmt = $pdo->prepare("SELECT * FROM expenses WHERE id = ? AND user_id = ?");
$stmt->execute([$id, $_SESSION['user_id']]);
$expense = $stmt->fetch();

if (!$expense) {
    header("Location: index.php");
    exit;
}

// Xử lý form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $amount = $_POST['amount'];
    $category = trim($_POST['category']);
    $description = trim($_POST['description']);
    $expense_date = $_POST['expense_date'];

    $stmt = $pdo->prepare("UPDATE expenses SET title=?, amount=?, category=?, description=?, expense_date=? WHERE id=? AND user_id=?");
    $stmt->execute([$title, $amount, $category, $description, $expense_date, $id, $_SESSION['user_id']]);

    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa chi tiêu</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body class="container mt-5">
    <h2>Sửa chi tiêu</h2>
    <form method="POST">
        <div class="mb-3">
            <label>Tiêu đề:</label>
            <input type="text" name="title" value="<?= htmlspecialchars($expense['title']) ?>" required class="form-control">
        </div>
        <div class="mb-3">
            <label>Số tiền:</label>
            <input type="number" name="amount" step="0.01" value="<?= $expense['amount'] ?>" required class="form-control">
        </div>
        <div class="mb-3">
            <label>Danh mục:</label>
            <input type="text" name="category" value="<?= htmlspecialchars($expense['category']) ?>" class="form-control">
        </div>
        <div class="mb-3">
            <label>Mô tả:</label>
            <textarea name="description" class="form-control"><?= htmlspecialchars($expense['description']) ?></textarea>
        </div>
        <div class="mb-3">
            <label>Ngày chi:</label>
            <input type="date" name="expense_date" value="<?= $expense['expense_date'] ?>" class="form-control">
        </div>
        <button type="submit" class="btn btn-success">Lưu</button>
        <a href="index.php" class="btn btn-secondary">Quay lại</a>
    </form>
</body>
</html>
