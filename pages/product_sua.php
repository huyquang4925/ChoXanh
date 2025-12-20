<?php
include __DIR__ . '/../config/db.php';

$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$product = null;
$message = '';
$message_type = '';

// Lấy thông tin sản phẩm
if ($product_id > 0) {
    $sql_product = "SELECT * FROM products WHERE id = $product_id";
    $result_product = $conn->query($sql_product);

    if ($result_product->num_rows > 0) {
        $product = $result_product->fetch_assoc();
    } else {
        $message = 'Sản phẩm không tìm thấy.';
        $message_type = 'danger';
    }
} else {
    $message = 'ID sản phẩm không hợp lệ.';
    $message_type = 'danger';
}

// Lấy danh sách categories và manufacturers để hiển thị trong form
$categories = [];
$sql_categories = "SELECT id, name FROM categories";
$result_categories = $conn->query($sql_categories);
if ($result_categories && $result_categories->num_rows > 0) {
    while ($row = $result_categories->fetch_assoc()) {
        $categories[] = $row;
    }
}

$manufacturers = [];
$sql_manufacturers = "SELECT id, name FROM nhasanxuat";
$result_manufacturers = $conn->query($sql_manufacturers);
if ($result_manufacturers && $result_manufacturers->num_rows > 0) {
    while ($row = $result_manufacturers->fetch_assoc()) {
        $manufacturers[] = $row;
    }
}

// Xử lý khi submit form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $product) {
    $name = $conn->real_escape_string($_POST['name']);
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);
    $description = $conn->real_escape_string($_POST['description']);
    $manufacturer_id = intval($_POST['manufacturer_id']);
    $category_id = intval($_POST['category_id']);

    $image_name = $product['image'];
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $target_dir = __DIR__ . '/../images/';
        $new_image_name = basename($_FILES['image']['name']);
        $target_file = $target_dir . $new_image_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        $check = getimagesize($_FILES['image']['tmp_name']);
        if ($check !== false) {
            if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
                $message = "Chỉ cho phép JPG, JPEG, PNG & GIF.";
                $message_type = 'danger';
            } else {
                if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                    $image_name = $new_image_name;
                } else {
                    $message = "Có lỗi khi tải ảnh lên.";
                    $message_type = 'danger';
                }
            }
        } else {
            $message = "File không phải là ảnh.";
            $message_type = 'danger';
        }
    }
    //ko có lỗi thì cập nhật
    if ($message_type !== 'danger') {
        $sql_update = "UPDATE products SET
                       name = '$name',
                       price = $price,
                       stock = $stock,
                       description = '$description',
                       image = '$image_name',
                       manufacturer_id = $manufacturer_id,
                       category_id = $category_id
                       WHERE id = $product_id";

        if ($conn->query($sql_update) === TRUE) {
            header('Location: index.php?page=product&id=' . $product_id);
            exit();
        } else {
            $message = 'Lỗi khi cập nhật sản phẩm: ' . $conn->error;
            $message_type = 'danger';
        }
    }
}
?>

<div class="container mt-5">
    <h2>Sửa Sản Phẩm</h2>

    <?php if ($message): ?>
        <div class="alert alert-<?php echo $message_type; ?>" role="alert">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <?php if ($product): ?>
        <form action="index.php?page=product_sua&id=<?php echo $product_id; ?>" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name">Tên sản phẩm:</label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
            </div>
            <div class="form-group">
                <label for="price">Giá:</label>
                <input type="number" class="form-control" id="price" name="price" step="0.01" value="<?php echo htmlspecialchars($product['price']); ?>" required>
            </div>
            <div class="form-group">
                <label for="stock">Số lượng trong kho:</label>
                <input type="number" class="form-control" id="stock" name="stock" value="<?php echo htmlspecialchars($product['stock']); ?>" required>
            </div>
            <div class="form-group">
                <label for="description">Mô tả:</label>
                <textarea class="form-control" id="description" name="description" rows="5"><?php echo htmlspecialchars($product['description']); ?></textarea>
            </div>
            <div class="form-group">
                <label for="image">Ảnh sản phẩm:</label>
                <?php if ($product['image']): ?>
                   <img src="images/<?php echo htmlspecialchars($product['image']); ?>" alt="Product Image" style="max-width: 100px;">
                <?php endif; ?>
                <input type="file" class="form-control-file" id="image" name="image">
                <small class="form-text text-muted">Chỉ tải lên nếu bạn muốn thay đổi ảnh.</small>
            </div>
            <div class="form-group">
                <label for="manufacturer_id">Nhà sản xuất:</label>
                <select class="form-control" id="manufacturer_id" name="manufacturer_id" required>
                    <?php foreach ($manufacturers as $manufacturer): ?>
                        <option value="<?php echo htmlspecialchars($manufacturer['id']); ?>" <?php echo ($manufacturer['id'] == $product['manufacturer_id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($manufacturer['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="category_id">Danh mục:</label>
                <select class="form-control" id="category_id" name="category_id" required>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo htmlspecialchars($category['id']); ?>" <?php echo ($category['id'] == $product['category_id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($category['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Cập nhật sản phẩm</button>
            <a href="index.php?page=product&id=<?php echo $product_id; ?>" class="btn btn-secondary">Quay lại</a>
        </form>
    <?php elseif ($message_type === 'danger'): ?>
        <a href="index.php" class="btn btn-primary">Về trang chủ</a>
    <?php endif; ?>
</div>

<?php
include __DIR__ . '/../partials/footer.php';
$conn->close();
?>