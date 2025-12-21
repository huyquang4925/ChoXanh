<?php
if (session_status() == PHP_SESSION_NONE) session_start();
include __DIR__ . '/../config/db.php';

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    echo '<div class="container mt-4"><div class="alert alert-danger">ID không hợp lệ.</div></div>';
    exit();
}

$message = '';
$message_type = '';

$sql = "SELECT id, name FROM categories WHERE id = $id";
$res = $conn->query($sql);
if (!$res || $res->num_rows === 0) {
    echo '<div class="container mt-4"><div class="alert alert-danger">Danh mục không tồn tại.</div></div>';
    exit();
}
$cat = $res->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $conn->real_escape_string(trim($_POST['name'] ?? ''));
    if ($name === '') {
        $message = 'Tên danh mục là bắt buộc.';
        $message_type = 'danger';
    } else {
        $sqlu = "UPDATE categories SET name='" . $name . "' WHERE id = $id";
        if ($conn->query($sqlu) === TRUE) {
            $redirect = 'index.php?page=admin_categories';
            if (!headers_sent()) { header('Location: ' . $redirect); exit(); }
            else { echo '<script>window.location.href="' . $redirect . '";</script>'; exit(); }
        } else {
            $message = 'Lỗi khi cập nhật: ' . $conn->error;
            $message_type = 'danger';
        }
    }
}
?>

<div class="container mt-4">
    <h3>Sửa Danh mục</h3>
    <?php if ($message): ?>
        <div class="alert alert-<?php echo htmlspecialchars($message_type); ?>"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <form method="POST" style="max-width:600px;">
        <div class="form-group">
            <label for="name">Tên</label>
            <input type="text" name="name" id="name" class="form-control" required value="<?php echo htmlspecialchars($_POST['name'] ?? $cat['name']); ?>">
        </div>
        
        <button class="btn btn-primary">Lưu</button>
        <a href="index.php?page=admin_categories" class="btn btn-secondary">Hủy</a>
    </form>
</div>

<?php $conn->close(); ?>
