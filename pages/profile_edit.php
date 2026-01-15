<?php
require_once __DIR__ . '/../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?page=login");
    exit;
}

$id = $_SESSION['user_id'];
$message = "";

// Lấy thông tin hiện tại
$stmt = $conn->prepare("SELECT email, phone FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Xử lý submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);

    $stmt = $conn->prepare("UPDATE users SET email = ?, phone = ? WHERE id = ?");
    $stmt->bind_param("ssi", $email, $phone, $id);
    $stmt->execute();

    $message = "Cập nhật thông tin thành công!";
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

.profile-wrapper h2 {
    margin-bottom: 20px;
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

.btn-primary {
    background: #2563eb;
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
</style>

<div class="profile-wrapper">
    <h2>Cập nhật thông tin cá nhân</h2>

    <?php if ($message): ?>
        <div class="alert-success"><?= $message ?></div>
    <?php endif; ?>

    <form method="post">
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
        </div>

        <div class="form-group">
            <label>Số điện thoại</label>
            <input type="text" name="phone" value="<?= htmlspecialchars($user['phone']) ?>">
        </div>

        <button class="btn-primary">Lưu thay đổi</button>
        <a href="index.php?page=profile" style="margin-left:10px;">Quay lại</a>
    </form>
</div>
