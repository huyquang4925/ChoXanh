<?php
if (session_status() == PHP_SESSION_NONE) session_start();
include __DIR__ . '/../config/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo '<div class="container mt-4"><div class="alert alert-danger">Không có quyền truy cập.</div></div>';
    return;
}

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $conn->real_escape_string(trim($_POST['username'] ?? ''));
    $password = $conn->real_escape_string(trim($_POST['password'] ?? ''));
    $email = $conn->real_escape_string(trim($_POST['email'] ?? ''));
    $phone = $conn->real_escape_string(trim($_POST['phone'] ?? ''));
    $role = in_array($_POST['role'] ?? '', ['admin','customer']) ? $_POST['role'] : 'admin';

    if ($username === '' || $password === '') {
        $message = 'Username và mật khẩu là bắt buộc.';
        $message_type = 'danger';
    } else {
        // simple insertion (consistent with existing scheme)
        $sql = "INSERT INTO users (username, password, email, phone, role) VALUES ('" . $username . "', '" . $password . "', '" . $email . "', '" . $phone . "', '" . $role . "')";
        if ($conn->query($sql) === TRUE) {
            $redirect = 'index.php?page=admin_staff';
            if (!headers_sent()) { header('Location: '.$redirect); exit; }
            echo '<script>window.location.href="' . $redirect . '";</script>'; exit;
        } else {
            $message = 'Lỗi khi thêm nhân viên: ' . $conn->error;
            $message_type = 'danger';
        }
    }
}
?>

<div class="container mt-4">
    <h3>Thêm Nhân viên</h3>
    <?php if ($message): ?>
        <div class="alert alert-<?php echo htmlspecialchars($message_type); ?>"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <form method="POST" style="max-width:700px;">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" name="username" id="username" class="form-control" required value="<?php echo htmlspecialchars($_POST['username'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label for="password">Mật khẩu</label>
            <input type="text" name="password" id="password" class="form-control" required value="<?php echo htmlspecialchars($_POST['password'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" class="form-control" value="<?php echo htmlspecialchars($_POST['email'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label for="phone">Phone</label>
            <input type="text" name="phone" id="phone" class="form-control" value="<?php echo htmlspecialchars($_POST['phone'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label for="role">Role</label>
            <select name="role" id="role" class="form-control">
                <option value="admin">admin</option>
                <option value="customer">customer</option>
            </select>
        </div>

        <button class="btn btn-success">Thêm</button>
        <a href="index.php?page=admin_staff" class="btn btn-secondary">Hủy</a>
    </form>
</div>

<?php $conn->close(); ?>
