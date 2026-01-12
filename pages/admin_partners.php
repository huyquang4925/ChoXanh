<?php
if (session_status() == PHP_SESSION_NONE) session_start();

// Only admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo '<div class="container mt-4"><div class="alert alert-danger">Không có quyền truy cập.</div></div>';
    return;
}

// Lấy danh sách nhà sản xuất
$sql = "SELECT n.id, n.name, n.description, COUNT(p.id) as product_count
        FROM nhasanxuat n
        LEFT JOIN products p ON n.id = p.manufacturer_id
        GROUP BY n.id, n.name, n.description
        ORDER BY n.id DESC";
$res = $conn->query($sql);
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

/* Modal for Product Details */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.6);
    overflow: auto;
}

.modal.show {
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-content {
    background: #fff;
    border-radius: 12px;
    max-width: 900px;
    width: 90%;
    max-height: 80vh;
    overflow-y: auto;
}

.modal-header {
    padding: 20px 30px;
    border-bottom: 1px solid #ecf0f1;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #34495e;
    color: #fff;
    border-radius: 12px 12px 0 0;
}

.modal-title {
    font-size: 24px;
    font-weight: bold;
}

.close {
    font-size: 32px;
    cursor: pointer;
    color: #fff;
    line-height: 1;
}

.close:hover {
    color: #e74c3c;
}

.modal-body {
    padding: 30px;
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
</style>

<div class="container">
    <div class="page-header">
        <h1 class="page-title">Quản lý Đối tác</h1>
        <a href="index.php?page=partner_add" class="btn btn-success">+ Thêm Đối tác</a>
    </div>

    <!-- Table -->
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
                                    <button class="btn btn-sm btn-info" onclick="viewDetails(<?= $row['id'] ?>, '<?= htmlspecialchars($row['name'], ENT_QUOTES) ?>')">Chi tiết</button>
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
</div>

<!-- Modal for Product Details -->
<div id="productModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title" id="modalTitle">Chi tiết sản phẩm</h3>
            <span class="close" onclick="closeModal()">&times;</span>
        </div>
        <div class="modal-body" id="modalBody">
            <p style="text-align: center; color: #7f8c8d;">Đang tải...</p>
        </div>
    </div>
</div>

<?php
// Lấy tất cả sản phẩm để dùng cho modal (load sẵn vào JS)
$sql_all_products = "SELECT id, name, price, stock, image, description, manufacturer_id 
                     FROM products 
                     ORDER BY id DESC";
$products_result = $conn->query($sql_all_products);
$all_products = [];
while ($p = $products_result->fetch_assoc()) {
    $all_products[] = $p;
}
?>

<script>
// Load tất cả sản phẩm vào JS (không cần AJAX)
const allProducts = <?= json_encode($all_products) ?>;

// View Product Details
function viewDetails(manufacturerId, manufacturerName) {
    const modal = document.getElementById('productModal');
    const modalTitle = document.getElementById('modalTitle');
    const modalBody = document.getElementById('modalBody');
    
    modalTitle.textContent = `Sản phẩm của ${manufacturerName}`;
    
    // Lọc sản phẩm theo manufacturer_id
    const products = allProducts.filter(p => p.manufacturer_id == manufacturerId);
    
    if (products.length > 0) {
        let html = '';
        products.forEach(product => {
            const totalValue = parseFloat(product.price) * parseInt(product.stock);
            html += `
                <div class="product-item">
                    <img src="images/${product.image || 'placeholder.jpg'}" 
                         alt="${product.name}" 
                         class="product-image"
                         onerror="this.style.display='none'">
                    <div class="product-info">
                        <div class="product-name">${product.name}</div>
                        <div class="product-details">
                            <div class="product-detail-item">
                                <strong>Giá:</strong> ${parseFloat(product.price).toLocaleString('vi-VN')}đ
                            </div>
                            <div class="product-detail-item">
                                <strong>Tồn kho:</strong> ${product.stock}
                            </div>
                            <div class="product-detail-item">
                                <strong>Giá trị:</strong> ${totalValue.toLocaleString('vi-VN')}đ
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
        modalBody.innerHTML = html;
    } else {
        modalBody.innerHTML = '<div class="no-products">Chưa có sản phẩm nào.</div>';
    }
    
    modal.classList.add('show');
}

function closeModal() {
    document.getElementById('productModal').classList.remove('show');
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('productModal');
    if (event.target == modal) {
        closeModal();
    }
}
</script>