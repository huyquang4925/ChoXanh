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
    $name = $conn->real_escape_string(trim($_POST['name'] ?? ''));
    $description = $conn->real_escape_string(trim($_POST['description'] ?? ''));

    if ($name === '') {
        $message = 'Tên đối tác là bắt buộc.';
        $message_type = 'danger';
    } else {
        $sql = "INSERT INTO nhasanxuat (name, description) VALUES ('" . $name . "', '" . $description . "')";
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
            $message = 'Lỗi khi thêm đối tác: ' . $conn->error;
            $message_type = 'danger';
        }
    }
}
?>

<div class="container mt-4">
    <h3>Thêm Đối tác</h3>
    <?php if ($message): ?>
        <div class="alert alert-<?php echo htmlspecialchars($message_type); ?>"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <form method="POST" style="max-width:700px;">
        <div class="form-group">
            <label for="name">Tên</label>
            <input type="text" name="name" id="name" class="form-control" required value="<?php echo htmlspecialchars($_POST['name'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label for="description">Mô tả</label>
            <textarea name="description" id="description" class="form-control"><?php echo htmlspecialchars($_POST['description'] ?? '') ?></textarea>
        </div>

        <button class="btn btn-success">Thêm</button>
        <a href="index.php?page=admin_partners" class="btn btn-secondary">Hủy</a>
    </form>
</div>

<?php $conn->close(); ?>
