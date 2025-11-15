<?php
session_start();
require '../config/db.php';

// Kiểm tra quyền admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    try {
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
        $stmt->execute();

        header("Location: manage_users.php");
        exit;
    } catch (PDOException $e) {
        echo "Lỗi khi thêm người dùng: " . $e->getMessage();
    }
}
?>

<?php include '../includes/header.php'; ?>
<div class="container mt-4">
    <h2>Thêm người dùng</h2>
    <form method="POST">
        <div class="mb-3">
            <label>Tên đăng nhập</label>
            <input type="text" name="username" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Mật khẩu</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Lưu</button>
        <a href="manage_users.php" class="btn btn-secondary">Hủy</a>
    </form>
</div>
<?php include '../includes/footer.php'; ?>
