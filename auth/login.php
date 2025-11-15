<?php
session_start();
include __DIR__ . '/../config/db.php';

if (isset($_POST['login'])) {

    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Admin cứng
    if ($username === "admin" && $password === "123") {
        $_SESSION['user_id'] = 0;
        $_SESSION['username'] = "admin";
        header("Location: ../manage_users/manage_user.php");
        exit();
    }

    // User thường
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];

        header("Location: ../expenses/index.php");
        exit();
    } else {
        $error = "Sai tên đăng nhập hoặc mật khẩu!";
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng nhập</title>
    <style>
        /* Reset cơ bản */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background: #111; /* nền tối kiểu Riot */
            color: #fff;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .login-container {
            background: #1e1e1e;
            padding: 40px 30px;
            border-radius: 8px;
            width: 320px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.4);
        }
        .login-container h1 {
            text-align: center;
            margin-bottom: 24px;
            font-size: 24px;
        }
        .login-container .input-group {
            margin-bottom: 18px;
        }
        .login-container input[type="text"],
        .login-container input[type="password"] {
            width: 100%;
            padding: 12px 14px;
            border: none;
            border-radius: 4px;
            background: #2e2e2e;
            color: #fff;
            font-size: 14px;
        }
        .login-container input[type="text"]::placeholder,
        .login-container input[type="password"]::placeholder {
            color: #aaa;
        }
        .login-container button {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 4px;
            background: #6380ff; /* màu kiểu “action” */
            color: #fff;
            font-size: 16px;
            cursor: pointer;
        }
        .login-container button:hover {
            background: #4a63d1;
        }
        .error {
            color: #ff5c5c;
            text-align: center;
            margin-bottom: 12px;
        }
        .footer-text {
            margin-top: 16px;
            text-align: center;
            font-size: 12px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>Đăng nhập</h1>
        <?php if(isset($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="post">
            <div class="input-group">
                <input type="text" name="username" placeholder="Tên đăng nhập" required>
            </div>
            <div class="input-group">
                <input type="password" name="password" placeholder="Mật khẩu" required>
            </div>
            <button type="submit" name="login">Đăng nhập</button>
        </form>
        <div class="footer-text">
            &copy; Nguyễn Hoàng Tùng 2205HTTB051
        </div>
    </div>
</body>
</html>
