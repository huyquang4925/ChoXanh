<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include __DIR__ . '/../config/db.php';

$user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
if ($user_id <= 0) {
    // If not logged in, show a simple message with a link to login
    ?>
    <div class="container" style="padding:24px;">
        <p>Đăng nhập để bắt đầu mua hàng — <a href="index.php?page=login">Đăng nhập</a></p>
    </div>
    <?php
    return;
}

$cart_id = 0;
$res = $conn->query("SELECT id FROM carts WHERE user_id = $user_id LIMIT 1");
if ($res && $res->num_rows > 0) {
    $cart_id = intval($res->fetch_assoc()['id']);
}

$items = [];
$total = 0.0;
if ($cart_id > 0) {
    $sql = "SELECT ci.id AS cart_item_id, ci.quantity, p.id AS product_id, p.name, p.price, p.image, p.stock
            FROM cart_items ci
            JOIN products p ON p.id = ci.product_id
            WHERE ci.cart_id = $cart_id";
    $res = $conn->query($sql);
    if ($res) {
        while ($row = $res->fetch_assoc()) {
            $row['subtotal'] = $row['quantity'] * $row['price'];
            $total += $row['subtotal'];
            $items[] = $row;
        }
    }
}
?>

<div class="container cart-page">
    <h2>Giỏ hàng của bạn</h2>

    <?php if (empty($items)): ?>
        <p>Giỏ hàng trống.</p>
    <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Sản phẩm</th>
                    <th>Giá</th>
                    <th>Số lượng</th>
                    <th>Thành tiền</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $it): ?>
                    <tr>
                        <td>
                            <img src="images/<?php echo htmlspecialchars($it['image']); ?>" alt="" style="width:60px;height:60px;object-fit:cover;margin-right:8px;">
                            <?php echo htmlspecialchars($it['name']); ?>
                        </td>
                        <td><?php echo number_format($it['price'],0); ?>đ</td>
                        <td>
                            <form method="post" action="index.php?page=cart_update" style="display:inline-block;">
                                <input type="hidden" name="cart_item_id" value="<?php echo intval($it['cart_item_id']); ?>">
                                <input type="number" name="quantity" value="<?php echo intval($it['quantity']); ?>" min="0" style="width:80px;display:inline-block;" />
                                <button class="btn btn-sm btn-primary" type="submit">Cập nhật</button>
                            </form>
                        </td>
                        <td><?php echo number_format($it['subtotal'],0); ?>đ</td>
                        <td>
                            <form method="post" action="index.php?page=cart_remove">
                                <input type="hidden" name="cart_item_id" value="<?php echo intval($it['cart_item_id']); ?>">
                                <button class="btn btn-sm btn-danger" type="submit">Xóa</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="cart-summary">
            <strong>Tổng: <?php echo number_format($total,0); ?>đ</strong>
        </div>

        <div style="margin-top:12px;">
            <a href="index.php?page=category&id=1" class="btn btn-secondary">Tiếp tục mua sắm</a>
            <a href="index.php?page=payment" class="btn btn-success ml-2">Thanh toán</a>
        </div>
    <?php endif; ?>
</div>

<?php $conn->close(); ?>
