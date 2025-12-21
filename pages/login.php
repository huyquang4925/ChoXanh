<?php
include  __DIR__ . '/../config/db.php';

$error = '';
$success = '';

// Handle Login
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        if ($password === $user['password']) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            
            echo "<script>window.location.href='index.php?page=home';</script>";
            exit();
        } else {
            $error = "Sai mật khẩu.";
        }
    } else {
        $error = "Tên đăng nhập hoặc email không tồn tại.";
    }
    $stmt->close();
}
?>

<div class="auth-box">
    <h2>Đăng nhập</h2>
    <?php if ($error) : ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>
    <?php if ($success) : ?>
        <p style="color: green;"><?php echo $success; ?></p>
    <?php endif; ?>
    <form action="index.php?page=login" method="POST">
        <label for="username">Tên đăng nhập hoặc Email:</label><br>
        <input type="text" id="username" name="username" required><br><br>

        <label for="password">Mật khẩu:</label><br>
        <input type="password" id="password" name="password" required><br><br>

        <button type="submit" name="login">Đăng nhập</button>
    </form>
    <p>Bạn chưa có tài khoản? <a href="index.php?page=register">Đăng ký ngay</a>
</div>