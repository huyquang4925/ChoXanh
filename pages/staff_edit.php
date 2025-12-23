<?php
if (session_status() == PHP_SESSION_NONE) session_start();
include __DIR__ . '/../config/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo '<div class="container mt-4"><div class="alert alert-danger">Không có quyền truy cập.</div></div>';
    return;
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) { echo '<div class="container mt-4"><div class="alert alert-danger">ID không hợp lệ.</div></div>'; return; }

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $conn->real_escape_string(trim($_POST['username'] ?? ''));
    $password = $conn->real_escape_string(trim($_POST['password'] ?? ''));
    $email = $conn->real_escape_string(trim($_POST['email'] ?? ''));
    $phone = $conn->real_escape_string(trim($_POST['phone'] ?? ''));
    $role = in_array($_POST['role'] ?? '', ['admin','customer']) ? $_POST['role'] : 'admin';

    if ($username === '') {
        $message = 'Username là bắt buộc.'; $message_type = 'danger';
    } else {
        $sql = "UPDATE users SET username='" . $username . "', email='" . $email . "', phone='" . $phone . "', role='" . $role . "'";
        if ($password !== '') { $sql .= ", password='" . $password . "'"; }
        $sql .= " WHERE id = $id";
        if ($conn->query($sql) === TRUE) { $redirect = 'index.php?page=admin_staff'; if (!headers_sent()){ header('Location: '.$redirect); exit; } echo '<script>window.location.href="'.$redirect.'";</script>'; exit; }
        else { $message = 'Lỗi khi cập nhật: '.$conn->error; $message_type = 'danger'; }
    }
}

$res = $conn->query("SELECT id, username, email, phone, role FROM users WHERE id = $id LIMIT 1");
if (!$res || $res->num_rows == 0) { echo '<div class="container mt-4"><div class="alert alert-danger">Người dùng không tồn tại.</div></div>'; return; }
$user = $res->fetch_assoc();
?>

<div class="container mt-4">
    <h3>Sửa Nhân viên</h3>
    <?php if ($message): ?>
        <div class="alert alert-<?php echo htmlspecialchars($message_type); ?>"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <form method="POST" style="max-width:700px;">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" name="username" id="username" class="form-control" required value="<?php echo htmlspecialchars($_POST['username'] ?? $user['username'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label for="password">Mật khẩu (để trống nếu không đổi)</label>
            <input type="text" name="password" id="password" class="form-control" value="">
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" class="form-control" value="<?php echo htmlspecialchars($_POST['email'] ?? $user['email'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label for="phone">Phone</label>
            <input type="text" name="phone" id="phone" class="form-control" value="<?php echo htmlspecialchars($_POST['phone'] ?? $user['phone'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label for="role">Role</label>
            <select name="role" id="role" class="form-control">
                <option value="admin" <?php echo (($user['role']=='admin') ? 'selected' : '') ?>>admin</option>
                <option value="customer" <?php echo (($user['role']=='customer') ? 'selected' : '') ?>>customer</option>
            </select>
        </div>

        <button class="btn btn-primary">Lưu</button>
        <a href="index.php?page=admin_staff" class="btn btn-secondary">Hủy</a>
    </form>
</div>

<?php $conn->close(); ?>
