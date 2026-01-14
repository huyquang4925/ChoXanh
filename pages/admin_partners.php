<?php
if (session_status() == PHP_SESSION_NONE) session_start();


if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo '<div class="container mt-4">
            <div class="alert alert-danger">Không có quyền truy cập.</div>
          </div>';
    return;
}
include __DIR__ . '/../config/db.php';

$sql = "SELECT n.id, n.name, n.description, COUNT(p.id) as product_count
        FROM nhasanxuat n
        LEFT JOIN products p ON n.id = p.manufacturer_id
        GROUP BY n.id, n.name, n.description
        ORDER BY n.id DESC";
$res = $conn->query($sql);
?>
<link rel="stylesheet" href="css/partners.css">
<div class="container">
    <div class="page-header">
        <h1 class="page-title">Quản lý Đối tác</h1>
        <a href="index.php?page=partner_add" class="btn btn-success">+ Thêm Đối tác</a>
    </div>

    <div class="table-container">
        <?php if ($res && $res->num_rows > 0): ?>
            <table>
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên</th>
                    <th>Mô tả</th>
                    <th>Số sản phẩm</th>
                    <th>Hành động</th>
                </tr>
                </thead>
                <tbody>
                <?php while ($row = $res->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= htmlspecialchars($row['description']) ?></td>
                        <td><?= $row['product_count'] ?></td>
                        <td>
                            <div class="action-buttons">
                                <a href="index.php?page=partners_detail&id=<?= $row['id'] ?>"
                                   class="btn btn-sm btn-info">
                                   Chi tiết
                                </a>
                                <a href="index.php?page=partner_edit&id=<?= $row['id'] ?>"
                                   class="btn btn-sm btn-primary">Sửa</a>
                                <a href="index.php?page=partner_delete&id=<?= $row['id'] ?>"
                                   class="btn btn-sm btn-danger"
                                   onclick="return confirm('Xác nhận xóa?')">Xóa</a>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="alert alert-info">Chưa có đối tác nào.</div>
        <?php endif; ?>
    </div>
</div>
