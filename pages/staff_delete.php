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
    $sql = "DELETE FROM users WHERE id = $id";
    if ($conn->query($sql) === TRUE) { $redirect = 'index.php?page=admin_staff'; if (!headers_sent()){ header('Location: '.$redirect); exit; } echo '<script>window.location.href="'.$redirect.'";</script>'; exit; }
    else { $message = 'Lỗi khi xóa: '.$conn->error; $message_type = 'danger'; }
}

$res = $conn->query("SELECT id, username FROM users WHERE id = $id LIMIT 1");
if (!$res || $res->num_rows == 0) { echo '<div class="container mt-4"><div class="alert alert-danger">Người dùng không tồn tại.</div></div>'; return; }
$user = $res->fetch_assoc();
?>

<div class="container mt-4">
    <h3>Xóa Nhân viên</h3>
    <?php if ($message): ?>
        <div class="alert alert-<?php echo htmlspecialchars($message_type); ?>"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <p>Bạn có chắc muốn xóa nhân viên: <strong><?php echo htmlspecialchars($user['username']); ?></strong> ?</p>

    <form method="POST">
        <button class="btn btn-danger">Xóa</button>
        <a href="index.php?page=admin_staff" class="btn btn-secondary">Hủy</a>
    </form>
</div>

<?php $conn->close(); ?>
