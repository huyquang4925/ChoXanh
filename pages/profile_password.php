<?php
require_once __DIR__ . '/../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?page=login");
    exit;
}

$id = $_SESSION['user_id'];
$message = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old = $_POST['old_password'];
    $new = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    if ($new !== $confirm) {
        $error = "Mật khẩu xác nhận không khớp";
    } else {
        $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();

        // ⚠️ Nếu mật khẩu cũ đang lưu dạng plaintext
        if ($old !== $user['password']) {
            $error = "Mật khẩu cũ không đúng";
        } else {
            // Có thể nâng cấp sang password_hash nếu muốn
            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->bind_param("si", $new, $id);
            $stmt->execute();

            $message = "Đổi mật khẩu thành công";
        }
    }
}
?>

<style>
.profile-wrapper {
    max-width: 700px;
    margin: 40px auto;
    background: #fff;
    border-radius: 12px;
    padding: 30px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.08);
}

.form-group {
    margin-bottom: 18px;
}

.form-group label {
    font-weight: 500;
    display: block;
    margin-bottom: 6px;
}

.form-group input {
    width: 100%;
    padding: 10px;
    border-radius: 8px;
    border: 1px solid #ddd;
}

.btn-warning {
    background: #f59e0b;
    color: #fff;
    border: none;
    padding: 10px 18px;
    border-radius: 8px;
    cursor: pointer;
}

.alert-success {
    background: #dcfce7;
    color: #166534;
    padding: 10px;
    border-radius: 8px;
    margin-bottom: 15px;
}

.alert-error {
    background: #fee2e2;
    color: #991b1b;
    padding: 10px;
    border-radius: 8px;
    margin-bottom: 15px;
}
</style>

<div class="profile-wrapper">
    <h2>Đổi mật khẩu</h2>

    <?php if ($message): ?>
        <div class="alert-success"><?= $message ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert-error"><?= $error ?></div>
    <?php endif; ?>

    <form method="post">
        <div class="form-group">
            <label>Mật khẩu cũ</label>
            <input type="password" name="old_password" required>
        </div>

        <div class="form-group">
            <label>Mật khẩu mới</label>
            <input type="password" name="new_password" required>
        </div>

        <div class="form-group">
            <label>Xác nhận mật khẩu mới</label>
            <input type="password" name="confirm_password" required>
        </div>

        <button class="btn-warning">Đổi mật khẩu</button>
        <a href="index.php?page=profile" style="margin-left:10px;">Quay lại</a>
    </form>
</div>
