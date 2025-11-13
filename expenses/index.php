<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Lấy danh sách danh mục để lọc
$stmtCat = $pdo->prepare("SELECT DISTINCT category FROM expenses WHERE user_id=?");
$stmtCat->execute([$user_id]);
$categories = $stmtCat->fetchAll(PDO::FETCH_COLUMN);
// Lấy dữ liệu tổng chi theo danh mục
$chartStmt = $pdo->prepare("SELECT category, SUM(amount) as total FROM expenses WHERE user_id=? GROUP BY category");
$chartStmt->execute([$user_id]);
$chartData = $chartStmt->fetchAll(PDO::FETCH_ASSOC);

// Chuyển dữ liệu sang mảng JSON cho Chart.js
$categoriesChart = json_encode(array_column($chartData, 'category'));
$totalsChart = json_encode(array_column($chartData, 'total'));
// Xử lý lọc
$filter_category = $_GET['category'] ?? '';
$order = $_GET['order'] ?? 'DESC';

$query = "SELECT * FROM expenses WHERE user_id=?";
$params = [$user_id];

if ($filter_category) {
    $query .= " AND category=?";
    $params[] = $filter_category;
}

$query .= " ORDER BY expense_date $order, created_at $order";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$expenses = $stmt->fetchAll();

// Tính tổng chi tiêu
$totalStmt = $pdo->prepare("SELECT SUM(amount) FROM expenses WHERE user_id=?");
$totalStmt->execute([$user_id]);
$total = $totalStmt->fetchColumn();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Chi tiêu</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<body class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Chào, <?= $_SESSION['username']; ?>!</h2>
        <a href="../auth/logout.php" class="btn btn-danger">Đăng xuất</a>
    </div>

    <div class="mb-3">
        <a href="add.php" class="btn btn-primary">Thêm chi tiêu mới</a>
    </div>

    <!-- Lọc theo danh mục và sắp xếp -->
    <form method="GET" class="row g-2 mb-3">
        <div class="col-auto">
            <select name="category" class="form-select">
                <option value="">-- Tất cả danh mục --</option>
                <?php foreach($categories as $cat): ?>
                    <option value="<?= htmlspecialchars($cat) ?>" <?= $filter_category==$cat?'selected':'' ?>><?= htmlspecialchars($cat) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-auto">
            <select name="order" class="form-select">
                <option value="DESC" <?= $order=='DESC'?'selected':'' ?>>Ngày mới → cũ</option>
                <option value="ASC" <?= $order=='ASC'?'selected':'' ?>>Ngày cũ → mới</option>
            </select>
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-success">Lọc</button>
            <a href="index.php" class="btn btn-secondary">Xóa lọc</a>
        </div>
    </form>

    <!-- Tổng chi tiêu -->
    <div class="mb-3">
        <strong>Tổng chi tiêu: <?= number_format($total, 2) ?> ₫</strong>
    </div>
<div class="mb-5">
    <h4>Thống kê chi tiêu theo danh mục</h4>
    <canvas id="expenseChart" width="400" height="200"></canvas>
</div>

<script>
const ctx = document.getElementById('expenseChart').getContext('2d');
const expenseChart = new Chart(ctx, {
    type: 'pie', // có thể đổi thành 'bar' nếu muốn cột
    data: {
        labels: <?= $categoriesChart ?>,
        datasets: [{
            label: 'Tổng chi tiêu theo danh mục',
            data: <?= $totalsChart ?>,
            backgroundColor: [
                'rgba(255, 99, 132, 0.7)',
                'rgba(54, 162, 235, 0.7)',
                'rgba(255, 206, 86, 0.7)',
                'rgba(75, 192, 192, 0.7)',
                'rgba(153, 102, 255, 0.7)',
                'rgba(255, 159, 64, 0.7)'
            ],
            borderColor: [
                'rgba(255, 99, 132, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(255, 159, 64, 1)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        responsive: true
    }
});
</script>
    <!-- Bảng chi tiêu -->
    <table class="table table-bordered table-striped table-hover">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Tiêu đề</th>
                <th>Số tiền</th>
                <th>Danh mục</th>
                <th>Mô tả</th>
                <th>Ngày chi</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php if($expenses): ?>
                <?php foreach($expenses as $index => $exp): ?>
                    <tr>
                        <td><?= $index+1 ?></td>
                        <td><?= htmlspecialchars($exp['title']) ?></td>
                        <td><?= number_format($exp['amount'], 2) ?> ₫</td>
                        <td><?= htmlspecialchars($exp['category']) ?></td>
                        <td><?= htmlspecialchars($exp['description']) ?></td>
                        <td><?= $exp['expense_date'] ?></td>
                        <td>
                            <a href="edit.php?id=<?= $exp['id'] ?>" class="btn btn-warning btn-sm">Sửa</a>
                            <a href="delete.php?id=<?= $exp['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc muốn xóa?')">Xóa</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="7" class="text-center">Chưa có chi tiêu nào.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
