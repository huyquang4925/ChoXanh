<?php
if (session_status() == PHP_SESSION_NONE) session_start();
include __DIR__ . '/../config/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo '<div class="container mt-4"><div class="alert alert-danger">Không có quyền truy cập.</div></div>';
    return;
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    echo '<div class="container mt-4"><div class="alert alert-danger">ID không hợp lệ.</div></div>';
    return;
}

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $conn->real_escape_string(trim($_POST['name'] ?? ''));
    $description = $conn->real_escape_string(trim($_POST['description'] ?? ''));

    if ($name === '') {
        $message = 'Tên đối tác là bắt buộc.';
        $message_type = 'danger';
    } else {
        $sql = "UPDATE nhasanxuat SET name='" . $name . "', description='" . $description . "' WHERE id = $id";
        if ($conn->query($sql) === TRUE) {
            $redirect = 'index.php?page=admin_partners';
            if (!headers_sent()) {
                header('Location: ' . $redirect);
                exit();
            } else {
                echo '<script>window.location.href="' . $redirect . '";</script>';
                exit();
            }
        } else {
            $message = 'Lỗi khi cập nhật: ' . $conn->error;
            $message_type = 'danger';
        }
    }
}

$sql = "SELECT id, name, description FROM nhasanxuat WHERE id = $id LIMIT 1";
$res = $conn->query($sql);
if (!$res || $res->num_rows == 0) {
    echo '<div class="container mt-4"><div class="alert alert-danger">Đối tác không tồn tại.</div></div>';
    return;
}
$partner = $res->fetch_assoc();
?>

<div class="container mt-4">
    <h3>Sửa Đối tác</h3>
    <?php if ($message): ?>
        <div class="alert alert-<?php echo htmlspecialchars($message_type); ?>"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <form method="POST" style="max-width:700px;">
        <div class="form-group">
            <label for="name">Tên</label>
            <input type="text" name="name" id="name" class="form-control" required value="<?php echo htmlspecialchars($_POST['name'] ?? $partner['name']) ?>">
        </div>
        <div class="form-group">
            <label for="description">Mô tả</label>
            <textarea name="description" id="description" class="form-control"><?php echo htmlspecialchars($_POST['description'] ?? $partner['description']) ?></textarea>
        </div>

        <button class="btn btn-primary">Lưu</button>
        <a href="index.php?page=admin_partners" class="btn btn-secondary">Hủy</a>
    </form>
</div>

<?php $conn->close(); ?>
