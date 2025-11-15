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

// Lấy thông tin người dùng
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch();

if (!$user) {
    die("Người dùng không tồn tại!");
}

// Xử lý cập nhật email
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];

    try {
        $stmt = $pdo->prepare("UPDATE users SET email = :email WHERE id = :id");
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        header("Location: manage_users.php");
        exit;
    } catch (PDOException $e) {
        echo "Lỗi khi cập nhật người dùng: " . $e->getMessage();
    }
}
?>

<?php include '../includes/header.php'; ?>
<div class="container mt-4">
    <h2>Sửa người dùng</h2>
    <form method="POST">
        <div class="mb-3">
            <label>Tên đăng nhập</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($user['username']) ?>" disabled>
        </div>
        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Cập nhật</button>
        <a href="manage_users.php" class="btn btn-secondary">Hủy</a>
    </form>
</div>
<?php include '../includes/footer.php'; ?>
