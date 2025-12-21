<?php
if (session_status() == PHP_SESSION_NONE) session_start();
include __DIR__ . '/../config/db.php';

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    echo '<div class="container mt-4"><div class="alert alert-danger">ID không hợp lệ.</div></div>';
    exit();
}

$sql = "SELECT id, name FROM categories WHERE id = $id";
$res = $conn->query($sql);
if (!$res || $res->num_rows === 0) {
    echo '<div class="container mt-4"><div class="alert alert-danger">Danh mục không tồn tại.</div></div>';
    exit();
}
$cat = $res->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sqld = "DELETE FROM categories WHERE id = $id";
    if ($conn->query($sqld) === TRUE) {
        $redirect = 'index.php?page=admin_categories';
        if (!headers_sent()) { header('Location: ' . $redirect); exit(); }
        else { echo '<script>window.location.href="' . $redirect . '";</script>'; exit(); }
    } else {
        $error = 'Lỗi khi xóa: ' . $conn->error;
    }
}
?>

<div class="container mt-4">
    <h3>Xóa Danh mục</h3>
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <div class="card" style="max-width:600px;">
        <div class="card-body">
            <h5 class="card-title"><?php echo htmlspecialchars($cat['name']); ?></h5>
            <p>Bạn có chắc chắn muốn xóa danh mục này?</p>
            <form method="POST" style="display:inline">
                <button class="btn btn-danger">Xóa</button>
            </form>
            <a href="index.php?page=admin_categories" class="btn btn-secondary">Hủy</a>
        </div>
    </div>
</div>

<?php $conn->close(); ?>
