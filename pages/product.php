<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include __DIR__ . '/../config/db.php';

$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$product = null;
if ($product_id > 0) {
    $sql = "SELECT * FROM products WHERE id = $product_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
    }
}

$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
?>

<div class="container product-detail">
    <?php if ($product): ?>
        <div class="row">
            <div class="col-md-6">
                <img src="images/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="img-fluid">
            </div>
            <div class="col-md-6">
                <h1><?php echo htmlspecialchars($product['name']); ?></h1>
                <p class="price"><?php echo htmlspecialchars(number_format($product['price'], 0)); ?>đ</p>
                <p>Số lượng tồn kho: <?php echo htmlspecialchars($product['stock']); ?></p>
                <p><?php echo htmlspecialchars($product['description']); ?></p>
                <form method="post" action="index.php?page=cart_add" style="display:inline-block; margin-right:8px;">
                    <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                    <button class="btn btn-primary" type="submit">Cho vào giỏ hàng</button>
                </form>

                <?php if ($isAdmin): ?>
                    <a href="index.php?page=product_sua&id=<?php echo $product_id; ?>" class="btn btn-secondary ml-2">Sửa sản phẩm</a>
                    <a href="index.php?page=product_del&id=<?php echo $product_id; ?>" class="btn btn-danger ml-2">Xóa sản phẩm</a>
                <?php endif; ?>
            </div>
        </div>
    <?php else: ?>
        <p>Product not found.</p>
    <?php endif; ?>
</div>

<?php
$conn->close();
?>