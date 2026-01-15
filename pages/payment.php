<?php
if (session_status() == PHP_SESSION_NONE) session_start();
include __DIR__ . '/../config/db.php';

if (!isset($_SESSION['user_id'])) {
    echo '<div class="container mt-4"><div class="alert alert-warning">Vui lòng đăng nhập để thanh toán.</div></div>';
    return;
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT ci.*, p.name, p.price 
        FROM cart_items ci 
        JOIN products p ON p.id = ci.product_id 
        WHERE ci.cart_id = (SELECT id FROM carts WHERE user_id = $user_id)";
$res = $conn->query($sql);

$items = [];
$total = 0;
while ($row = $res->fetch_assoc()) {
    $items[] = $row;
    $total += $row['price'] * $row['quantity'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phuongthuc = $_POST['phuongthuc'];
    $ngay = date('Y-m-d H:i:s');

    $conn->query("INSERT INTO orders(user_id,total_amount,status,created_at) 
                  VALUES($user_id,$total,'pending','$ngay')");
    $order_id = $conn->insert_id;

    foreach ($items as $it) {
        $pid = $it['product_id'];
        $qty = $it['quantity'];
        $price = $it['price'];
        $conn->query("INSERT INTO order_items(order_id,product_id,quantity,price) 
                      VALUES($order_id,$pid,$qty,$price)");
    }

    $conn->query("INSERT INTO payments(order_id,payment_method,payment_status,paid_at) 
                  VALUES($order_id,'$phuongthuc','pending','$ngay')");

    $conn->query("DELETE FROM cart_items WHERE cart_id = (SELECT id FROM carts WHERE user_id = $user_id)");

    echo "<div class='container mt-4'><div class='alert alert-success'>
          Đặt hàng thành công! Mã đơn: $order_id
          </div></div>";
    return;
}
?>

<div class="container mt-4">
    <h3>Thanh toán</h3>
    <table class="table">
        <thead>
            <tr><th>Sản phẩm</th><th>Giá</th><th>Số lượng</th><th>Thành tiền</th></tr>
        </thead>
        <tbody>
            <?php foreach ($items as $it): ?>
                <tr>
                    <td><?= htmlspecialchars($it['name']) ?></td>
                    <td><?= number_format($it['price'],0) ?>đ</td>
                    <td><?= $it['quantity'] ?></td>
                    <td><?= number_format($it['price']*$it['quantity'],0) ?>đ</td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <p><strong>Tổng: <?= number_format($total,0) ?>đ</strong></p>

    <form method="post" class="mt-3">
        <div class="mb-3">
            <label class="form-label">Phương thức thanh toán</label>

            <div class="row">
                <div class="col-md-6">
                    <label class="card p-3 mb-3 d-flex align-items-center">
                        <input type="radio" name="phuongthuc" value="cod" required class="visually-hidden">
                        <img src="images/cod.png" alt="COD" class="me-2" style="width:60px;height:40px;object-fit:contain;">
                        <span class="fw-bold">COD</span>
                    </label>
                </div>
                <div class="col-md-6">
                    <label class="card p-3 mb-3 d-flex align-items-center">
                        <input type="radio" name="phuongthuc" value="Banking" required class="visually-hidden">
                        <img src="images/banking.png" alt="Banking" class="me-2" style="width:60px;height:40px;object-fit:contain;">
                        <span class="fw-bold">Chuyển khoản</span>
                    </label>
                </div>
                <div class="col-md-6">
                    <label class="card p-3 mb-3 d-flex align-items-center">
                        <input type="radio" name="phuongthuc" value="MoMo" required class="visually-hidden">
                        <img src="images/momo-logo.png" alt="MoMo" class="me-2" style="width:60px;height:40px;object-fit:contain;">
                        <span class="fw-bold">MoMo</span>
                    </label>
                </div>
                <div class="col-md-6">
                    <label class="card p-3 mb-3 d-flex align-items-center">
                        <input type="radio" name="phuongthuc" value="VNpay" required class="visually-hidden">
                        <img src="images/vnpay-logo.png" alt="VNPay" class="me-2" style="width:60px;height:40px;object-fit:contain;">
                        <span class="fw-bold">VNPay</span>
                    </label>
                </div>
            </div>
        </div>

        <button class="btn btn-success">Xác nhận thanh toán</button>
    </form>
</div>