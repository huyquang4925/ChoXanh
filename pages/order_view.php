<?php
if (session_status() == PHP_SESSION_NONE) session_start();
include __DIR__ . '/../config/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo '<div class="container mt-4"><div class="alert alert-danger">Không có quyền truy cập.</div></div>';
    return;
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) { echo '<div class="container mt-4"><div class="alert alert-danger">ID không hợp lệ.</div></div>'; return; }

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status'])) {
    $status = $conn->real_escape_string($_POST['status']);
    $conn->query("UPDATE orders SET status = '$status' WHERE id = $id");
}

$res = $conn->query("SELECT o.*, u.username, u.email FROM orders o LEFT JOIN users u ON u.id = o.user_id WHERE o.id = $id LIMIT 1");
if (!$res || $res->num_rows == 0) { echo '<div class="container mt-4"><div class="alert alert-danger">Đơn hàng không tồn tại.</div></div>'; return; }
$order = $res->fetch_assoc();

$items = [];
$r2 = $conn->query("SELECT oi.*, p.name FROM order_items oi LEFT JOIN products p ON p.id = oi.product_id WHERE oi.order_id = $id");
if ($r2) { while ($row = $r2->fetch_assoc()) $items[] = $row; }
?>

<div class="container mt-4">
    <h3>Chi tiết Đơn hàng #<?php echo $order['id']; ?></h3>
    <p>Khách: <?php echo htmlspecialchars($order['username'] ?? 'Khách'); ?> — <?php echo htmlspecialchars($order['email'] ?? ''); ?></p>
    <p>Ngày: <?php echo htmlspecialchars($order['created_at']); ?></p>
    <p>Trạng thái hiện tại: <strong><?php echo htmlspecialchars($order['status']); ?></strong></p>

    <form method="post" style="max-width:400px;">
        <div class="form-group">
            <label for="status">Cập nhật trạng thái</label>
            <select name="status" id="status" class="form-control">
                <?php foreach (['pending','paid','shipping','completed','cancelled'] as $s): ?>
                    <option value="<?php echo $s; ?>" <?php echo ($order['status']==$s)?'selected':''; ?>><?php echo $s; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button class="btn btn-primary">Cập nhật</button>
        <a href="index.php?page=admin_orders" class="btn btn-secondary">Quay lại</a>
    </form>

    <h4 class="mt-4">Sản phẩm</h4>
    <?php if (count($items) > 0): ?>
        <table class="table">
            <thead><tr><th>Sản phẩm</th><th>Giá</th><th>Số lượng</th></tr></thead>
            <tbody>
                <?php foreach ($items as $it): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($it['name']); ?></td>
                        <td><?php echo number_format($it['price'],0); ?>đ</td>
                        <td><?php echo intval($it['quantity']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Không có sản phẩm.</p>
    <?php endif; ?>

    <p class="mt-3"><strong>Tổng: <?php echo number_format($order['total_amount'],0); ?>đ</strong></p>
</div>

<?php $conn->close(); ?>
