<?php
include  __DIR__ . '/../config/db.php';

$error = '';
$success = '';

// Handle Registration
if (isset($_POST['register'])) {
    $username = $_POST['reg_username'];
    $email = $_POST['reg_email'];
    $password = $_POST['reg_password'];
    $confirm_password = $_POST['reg_confirm_password'];

    if ($password !== $confirm_password) {
        $error = "Mật khẩu xác nhận không khớp.";
    } else {
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "Tên đăng nhập hoặc email đã tồn tại.";
        } else {
            $plain_password = $password;
            $role = 'customer';

            $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $username, $email, $plain_password, $role);

            if ($stmt->execute()) {
                $success = "Đăng ký thành công! Bạn có thể đăng nhập ngay bây giờ.";
                header("Location: index.php?page=login");
                exit();
            } else {
                $error = "Đăng ký thất bại: " . $conn->error;
            }
        }
        $stmt->close();
    }
}
?>

<div class="auth-box">
    <h2>Đăng ký</h2>
    <?php if ($error) : ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>
    <?php if ($success) : ?>
        <p style="color: green;"><?php echo $success; ?></p>
    <?php endif; ?>
    <form action="index.php?page=register" method="POST">
        <label for="reg_username">Tên đăng nhập:</label><br>
        <input type="text" id="reg_username" name="reg_username" required><br><br>

        <label for="reg_email">Email:</label><br>
        <input type="email" id="reg_email" name="reg_email" required><br><br>

        <label for="reg_password">Mật khẩu:</label><br>
        <input type="password" id="reg_password" name="reg_password" required><br><br>

        <label for="reg_confirm_password">Xác nhận mật khẩu:</label><br>
        <input type="password" id="reg_confirm_password" name="reg_confirm_password" required><br><br>

        <button type="submit" name="register">Đăng ký</button>
    </form>
    <p>Đã có tài khoản? <a href="index.php?page=login">Đăng nhập</a>
</div>