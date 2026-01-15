<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$username = $_SESSION['username'] ?? '';
$role     = $_SESSION['role'] ?? '';

include __DIR__ . '/../config/db.php';

$deleteMessage = '';

// Xử lý xóa tin
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $deleteId = intval($_POST['delete_id']);

    if ($role === 'admin' || $username === 'test') {
        $stmt = $conn->prepare("DELETE FROM news WHERE id = ?");
        $stmt->bind_param("i", $deleteId);
        if ($stmt->execute()) {
            $deleteMessage = "✅ Đã xóa tin tức thành công.";
        } else {
            $deleteMessage = "❌ Xóa thất bại. Vui lòng thử lại.";
        }
        $stmt->close();
    } else {
        $deleteMessage = "⚠️ Bạn không có quyền xóa tin tức.";
    }
}

// Truy vấn danh sách tin
$sql = "SELECT id, title, content, image, created_at FROM news ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<div class="container mt-4">
    <h2>Tin tức mới nhất</h2>

    <?php if (!empty($deleteMessage)): ?>
        <div class="alert alert-info"><?= htmlspecialchars($deleteMessage) ?></div>
    <?php endif; ?>

    <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">
                        <?= htmlspecialchars($row['title']) ?>

                        <?php if ($role === 'admin' || $username === 'test'): ?>
                            <form method="POST" action="index.php?page=news" style="display:inline;">
                                <input type="hidden" name="delete_id" value="<?= $row['id'] ?>">
                                <button type="submit"
                                        class="btn btn-danger btn-sm"
                                        onclick="return confirm('Bạn có chắc muốn xóa tin này?')">
                                    Xóa
                                </button>
                            </form>
                        <?php endif; ?>
                    </h5>

                    <?php if (!empty($row['image'])): ?>
                        <img src="uploads/<?= htmlspecialchars($row['image']) ?>"
                             class="img-fluid mb-2"
                             style="max-width:300px;"
                             alt="Ảnh minh họa">
                    <?php endif; ?>

                    <p class="card-text"><?= nl2br(htmlspecialchars($row['content'])) ?></p>
                    <small class="text-muted">Đăng ngày: <?= $row['created_at'] ?></small>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>Chưa có tin tức nào.</p>
    <?php endif; ?>
</div>

<?php $conn->close(); ?>
