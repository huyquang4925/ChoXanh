<?php
if (session_status() == PHP_SESSION_NONE) session_start();

// Only admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo '<div class="container mt-4"><div class="alert alert-danger">Không có quyền truy cập.</div></div>';
    return;
}

// Lấy ID để xem chi tiết (nếu có)
$detail_id = isset($_GET['detail']) ? intval($_GET['detail']) : 0;

// Lấy danh sách nhà sản xuất
$sql = "SELECT n.id, n.name, n.description, COUNT(p.id) as product_count
        FROM nhasanxuat n
        LEFT JOIN products p ON n.id = p.manufacturer_id
        GROUP BY n.id, n.name, n.description
        ORDER BY n.id DESC";
$res = $conn->query($sql);

// Nếu có detail_id, lấy sản phẩm của nhà sản xuất đó
$detail_products = [];
$detail_name = '';
if ($detail_id > 0) {
    $sql_detail = "SELECT name FROM nhasanxuat WHERE id = ?";
    $stmt = $conn->prepare($sql_detail);
    $stmt->bind_param('i', $detail_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $detail_name = $row['name'];
    }
    
    $sql_products = "SELECT id, name, price, stock, image, description 
                     FROM products 
                     WHERE manufacturer_id = ? 
                     ORDER BY id DESC";
    $stmt2 = $conn->prepare($sql_products);
    $stmt2->bind_param('i', $detail_id);
    $stmt2->execute();
    $products_result = $stmt2->get_result();
    while ($p = $products_result->fetch_assoc()) {
        $detail_products[] = $p;
    }
}
?>

<style>
.container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 20px;
}

.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
}

.page-title {
    font-size: 28px;
    font-weight: bold;
    color: #2c3e50;
}

.btn {
    padding: 10px 20px;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
    display: inline-block;
}

.btn-success {
    background: #27ae60;
    color: #fff;
}

.btn-success:hover {
    background: #229954;
}

.btn-primary {
    background: #3498db;
    color: #fff;
}

.btn-primary:hover {
    background: #2980b9;
}

.btn-info {
    background: #17a2b8;
    color: #fff;
}

.btn-info:hover {
    background: #138496;
}

.btn-danger {
    background: #e74c3c;
    color: #fff;
}

.btn-danger:hover {
    background: #c0392b;
}

.btn-secondary {
    background: #95a5a6;
    color: #fff;
}

.btn-secondary:hover {
    background: #7f8c8d;
}

.btn-sm {
    padding: 6px 12px;
    font-size: 14px;
}

.table-container {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    margin-bottom: 40px;
}

table {
    width: 100%;
    border-collapse: collapse;
}

thead {
    background: #34495e;
    color: #fff;
}

thead th {
    padding: 15px;
    text-align: left;
    font-weight: 600;
}

tbody tr {
    border-bottom: 1px solid #ecf0f1;
    transition: background 0.2s ease;
}

tbody tr:hover {
    background: #f8f9fa;
}

tbody td {
    padding: 15px;
}

.action-buttons {
    display: flex;
    gap: 8px;
}

.alert {
    padding: 15px 20px;
    border-radius: 8px;
    margin-bottom: 20px;
}

.alert-info {
    background: #d1ecf1;
    color: #0c5460;
    border: 1px solid #bee5eb;
}

.alert-danger {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

/* Detail Box */
.detail-box {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
    margin-bottom: 30px;
    overflow: hidden;
}

.detail-header {
    background: #34495e;
    color: #fff;
    padding: 20px 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.detail-title {
    font-size: 24px;
    font-weight: bold;
}

.detail-body {
    padding: 30px;
    max-height: 600px;
    overflow-y: auto;
}

.product-item {
    display: flex;
    gap: 20px;
    padding: 15px;
    border: 1px solid #ecf0f1;
    border-radius: 8px;
    margin-bottom: 15px;
    transition: all 0.3s ease;
}

.product-item:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.product-image {
    width: 100px;
    height: 100px;
    border-radius: 8px;
    object-fit: cover;
    background: #f8f9fa;
}

.product-info {
    flex: 1;
}

.product-name {
    font-size: 18px;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 8px;
}

.product-details {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
}

.product-detail-item {
    font-size: 14px;
    color: #7f8c8d;
}

.product-detail-item strong {
    color: #2c3e50;
}

.no-products {
    text-align: center;
    padding: 40px;
    color: #7f8c8d;
    font-size: 16px;
}

.back-button {
    margin-bottom: 20px;
}
</style>

<div class="container">
    
    <?php if ($detail_id > 0): ?>
        <!-- Detail View -->
        <div class="back-button">
            <a href="index.php?page=admin_partners" class="btn btn-secondary">← Quay lại danh sách</a>
        </div>
        
        <div class="detail-box">
            <div class="detail-header">
                <h2 class="detail-title">Sản phẩm của <?= htmlspecialchars($detail_name) ?></h2>
                <span style="font-size: 16px;"><?= count($detail_products) ?> sản phẩm</span>
            </div>
            <div class="detail-body">
                <?php if (count($detail_products) > 0): ?>
                    <?php foreach ($detail_products as $product): 
                        $totalValue = $product['price'] * $product['stock'];
                    ?>
                        <div class="product-item">
                            <?php if (!empty($product['image'])): ?>
                                <img src="images/<?= htmlspecialchars($product['image']) ?>" 
                                     alt="<?= htmlspecialchars($product['name']) ?>" 
                                     class="product-image">
                            <?php else: ?>
                                <div class="product-image" style="display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; font-size: 12px;">
                                    Không có ảnh
                                </div>
                            <?php endif; ?>
                            
                            <div class="product-info">
                                <div class="product-name"><?= htmlspecialchars($product['name']) ?></div>
                                <div class="product-details">
                                    <div class="product-detail-item">
                                        <strong>Giá:</strong> <?= number_format($product['price'], 0, ',', '.') ?>đ
                                    </div>
                                    <div class="product-detail-item">
                                        <strong>Tồn kho:</strong> <?= $product['stock'] ?>
                                    </div>
                                    <div class="product-detail-item">
                                        <strong>Giá trị:</strong> <?= number_format($totalValue, 0, ',', '.') ?>đ
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-products">Chưa có sản phẩm nào.</div>
                <?php endif; ?>
            </div>
        </div>
        
    <?php else: ?>
        <!-- List View -->
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
                                        <a href="index.php?page=admin_partners&detail=<?= $row['id'] ?>" class="btn btn-sm btn-info">Chi tiết</a>
                                        <a href="index.php?page=partner_edit&id=<?= $row['id'] ?>" class="btn btn-sm btn-primary">Sửa</a>
                                        <a href="index.php?page=partner_delete&id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Xác nhận xóa?')">Xóa</a>
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
    <?php endif; ?>
    
</div>