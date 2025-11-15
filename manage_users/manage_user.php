<?php
session_start();
require '../config/db.php';

// Chỉ admin được truy cập
if (!isset($_SESSION['user_id']) || $_SESSION['username'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit;
}

// Lấy danh sách user (KHÔNG có role)
$stmt = $pdo->query("SELECT id, username, email, created_at FROM users ORDER BY id ASC");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include '../includes/header.php'; ?>

<div class="container mt-4">
    <h2 class="mb-3">Quản lý người dùng</h2>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Tên đăng nhập</th>
                <th>Email</th>
                <th>Ngày tạo</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?= $user['id'] ?></td>
                <td><?= htmlspecialchars($user['username']) ?></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td><?= $user['created_at'] ?></td>
                <td>
                    <a href="edit_user.php?id=<?= $user['id'] ?>" class="btn btn-warning btn-sm">Sửa</a>
                    <a href="delete_user.php?id=<?= $user['id'] ?>" 
                       class="btn btn-danger btn-sm"
                       onclick="return confirm('Xoá người dùng này?');">Xoá</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <a href="add_user.php" class="btn btn-success">Thêm người dùng</a>
</div>

<?php include '../includes/footer.php'; ?>
