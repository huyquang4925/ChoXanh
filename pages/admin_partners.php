<?php
if (session_status() == PHP_SESSION_NONE) session_start();
include __DIR__ . '/../config/db.php';

// only admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo '<div class="container mt-4"><div class="alert alert-danger">Không có quyền truy cập.</div></div>';
    return;
}

$sql = "SELECT id, name, description FROM nhasanxuat ORDER BY id DESC";
$res = $conn->query($sql);
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Quản lý Đối tác</h3>
        <a href="index.php?page=partner_add" class="btn btn-success">Thêm Đối tác</a>
    </div>

    <?php if ($res && $res->num_rows > 0): ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên</th>
                    <th>Mô tả</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $res->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['description']); ?></td>
                        <td>
                            <a href="index.php?page=partner_edit&id=<?php echo $row['id']; ?>" class="btn btn-sm btn-primary">Sửa</a>
                            <a href="index.php?page=partner_delete&id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger">Xóa</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-info">Chưa có đối tác nào.</div>
    <?php endif; ?>
</div>

<?php $conn->close(); ?>
