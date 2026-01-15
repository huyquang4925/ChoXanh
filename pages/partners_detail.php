<?php
if (session_status() == PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo '<div class="container mt-4">
            <div class="alert alert-danger">Không có quyền truy cập.</div>
          </div>';
    return;
}

include __DIR__ . '/../config/db.php';
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo '<div class="alert alert-danger">ID không hợp lệ</div>';
    return;
}

$partner_id = (int)$_GET['id'];


$stmt = $conn->prepare("SELECT name FROM nhasanxuat WHERE id=?");
$stmt->bind_param("i", $partner_id);
$stmt->execute();
$result = $stmt->get_result();

if (!$partner = $result->fetch_assoc()) {
    echo '<div class="alert alert-danger">Đối tác không tồn tại</div>';
    return;
}
$partner_name = $partner['name'];

// Lấy sản phẩm
$stmt = $conn->prepare("
    SELECT name, price, stock, image
    FROM products
    WHERE manufacturer_id=?
    ORDER BY id DESC
");
$stmt->bind_param("i", $partner_id);
$stmt->execute();
$products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<link rel="stylesheet" href="css/partners.css">
<div class="container">

    <div class="back-button">
        <a href="index.php?page=admin_partners" class="btn btn-secondary">
            ← Quay lại danh sách
        </a>
    </div>

    <div class="detail-box">
        <div class="detail-header">
            <h2>Sản phẩm của <?= htmlspecialchars($partner_name) ?></h2>
            <span><?= count($products) ?> sản phẩm</span>
        </div>

        <div class="detail-body">
            <?php if ($products): ?>
                <?php foreach ($products as $p):
                    $total = $p['price'] * $p['stock'];
                ?>
                    <div class="product-item">
                        <?php if ($p['image']): ?>
                            <img src="images/<?= htmlspecialchars($p['image']) ?>"
                                 class="product-image">
                        <?php else: ?>
                            <div class="product-image"
                                 style="display:flex;align-items:center;justify-content:center">
                                No Image
                            </div>
                        <?php endif; ?>

                        <div class="product-info">
                            <div class="product-name"><?= htmlspecialchars($p['name']) ?></div>
                            <div class="product-detail-item">
                                <strong>Giá:</strong>
                                <?= number_format($p['price']) ?>đ
                            </div>
                            <div class="product-detail-item">
                                <strong>Tồn kho:</strong> <?= $p['stock'] ?>
                            </div>
                            <div class="product-detail-item">
                                <strong>Giá trị:</strong>
                                <?= number_format($total) ?>đ
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-products">Chưa có sản phẩm</div>
            <?php endif; ?>
        </div>
    </div>
</div>