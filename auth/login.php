<?php
session_start();
require '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header("Location: ../expenses/index.php");
        exit;
    } else {
        $_SESSION['error'] = "Sai tên đăng nhập hoặc mật khẩu!";
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng nhập</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body class="container mt-5">
    <h2>Đăng nhập</h2>
    <?php if(isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>
    <form method="POST">
        <div class="mb-3">
            <label>Tên đăng nhập:</label>
            <input type="text" name="username" required class="form-control">
        </div>
        <div class="mb-3">
            <label>Mật khẩu:</label>
            <input type="password" name="password" required class="form-control">
        </div>
        <button type="submit" class="btn btn-success">Đăng nhập</button>
        <a href="register.php" class="btn btn-link">Đăng ký</a>
    </form>
</body>
</html>
