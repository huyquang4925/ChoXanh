<?php
if (session_status() == PHP_SESSION_NONE) session_start();
include __DIR__ . '/../config/db.php';

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $conn->real_escape_string(trim($_POST['name'] ?? ''));

    if ($name === '') {
        $message = 'Tên danh mục là bắt buộc.';
        $message_type = 'danger';
    } else {
        $sql = "INSERT INTO categories (name) VALUES ('" . $name . "')";
        if ($conn->query($sql) === TRUE) {
            $newId = $conn->insert_id;
            $redirect = 'index.php?page=admin_categories';
            if (!headers_sent()) {
                header('Location: ' . $redirect);
                exit();
            } else {
                echo '<script>window.location.href="' . $redirect . '";</script>';
                exit();
            }
        } else {
            $message = 'Lỗi khi thêm danh mục: ' . $conn->error;
            $message_type = 'danger';
        }
    }
}
?>

<div class="container mt-4">
    <h3>Thêm Danh mục</h3>
    <?php if ($message): ?>
        <div class="alert alert-<?php echo htmlspecialchars($message_type); ?>"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <form method="POST" style="max-width:600px;">
        <div class="form-group">
            <label for="name">Tên</label>
            <input type="text" name="name" id="name" class="form-control" required value="<?php echo htmlspecialchars($_POST['name'] ?? '') ?>">
        </div>
        
        <button class="btn btn-success">Thêm</button>
        <a href="index.php?page=admin_categories" class="btn btn-secondary">Hủy</a>
    </form>
</div>

<?php $conn->close(); ?>
