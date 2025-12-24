<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include __DIR__ . '/../config/db.php';

$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;


// Lấy thông tin product để hiện tên/ảnh trước khi xóa
$sql = "SELECT * FROM products WHERE id = $product_id";
$res = $conn->query($sql);

$product = $res->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn->begin_transaction();
    try {
        $conn->query("DELETE FROM cart_items WHERE product_id = " . intval($product_id));

        $conn->query("DELETE FROM order_items WHERE product_id = " . intval($product_id));

        if (!empty($product['image'])) {
            $imagePath = __DIR__ . '/../images/' . $product['image'];
            if (file_exists($imagePath)) {
                @unlink($imagePath);
            }
        }

        // Xóa sản phẩm
        $sqlDelete = "DELETE FROM products WHERE id = " . intval($product_id);
        if ($conn->query($sqlDelete) !== TRUE) {
            throw new Exception($conn->error);
        }

        $conn->commit();
        echo "<script>window.location.href='index.php';</script>";
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        $error = 'Lỗi khi xóa sản phẩm: ' . $e->getMessage();
    }
}
?>

<div class="container mt-5">
    <h3>Xóa Sản Phẩm</h3>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <div class="card mb-3" style="max-width:600px;">
        <div class="row no-gutters">
            <div class="col-md-4">
                <?php if ($product['image']): ?>
                    <img src="images/<?php echo htmlspecialchars($product['image']); ?>" class="card-img" alt="<?php echo htmlspecialchars($product['name']); ?>">
                <?php endif; ?>
            </div>
            <div class="col-md-8">
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                    <p class="card-text">Giá: <?php echo htmlspecialchars(number_format($product['price'], 0)); ?>đ</p>
                    <p class="card-text">Bạn có chắc chắn muốn xóa sản phẩm này?</p>
                    <form method="POST" style="display:inline">
                        <button type="submit" class="btn btn-danger">Xóa</button>
                    </form>
                    <a href="index.php?page=product&id=<?php echo $product_id; ?>" class="btn btn-secondary">Hủy</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$conn->close();
?>