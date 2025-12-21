<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include __DIR__ . '/../config/db.php';

$message = '';
$message_type = '';

// Lấy danh sách categories
$categories = [];
$sql_categories = "SELECT id, name FROM categories";
$result_categories = $conn->query($sql_categories);
if ($result_categories && $result_categories->num_rows > 0) {
    while ($row = $result_categories->fetch_assoc()) {
        $categories[] = $row;
    }
}

// Lấy danh sách nhà sản xuất
$nhaSanXuats = [];
$sql_nhaSanXuats = "SELECT id, name FROM nhasanxuat";
$result_nhaSanXuats = $conn->query($sql_nhaSanXuats);
if ($result_nhaSanXuats && $result_nhaSanXuats->num_rows > 0) {
    while ($row = $result_nhaSanXuats->fetch_assoc()) {
        $nhaSanXuats[] = $row;
    }
}

// Prefill category if passed from category listing
$prefill_category = intval($_POST['category_id'] ?? $_GET['category_id'] ?? 0);
$prefill_manufacturer = intval($_POST['nhaSanXuat_id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $conn->real_escape_string(trim($_POST['name'] ?? ''));
    $price = intval($_POST['price'] ?? 0);
    $stock = intval($_POST['stock'] ?? 0);
    $description = $conn->real_escape_string(trim($_POST['description'] ?? ''));
    $nhaSanXuat_id = intval($_POST['nhaSanXuat_id'] ?? 0);
    $category_id = intval($_POST['category_id'] ?? $_GET['category_id'] ?? 0);
    $prefill_category = $category_id;
    $prefill_manufacturer = $nhaSanXuat_id;

    if ($name === '' || $price <= 0) {
        $message = 'Tên và giá sản phẩm là bắt buộc (giá > 0).';
        $message_type = 'danger';
    } else {
        $image_name = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $tmp = $_FILES['image']['tmp_name'];
            $orig = $_FILES['image']['name'];
            $ext = pathinfo($orig, PATHINFO_EXTENSION);
            $image_name = time() . '_' . bin2hex(random_bytes(4)) . ($ext ? '.' . $ext : '');
            $dest = __DIR__ . '/../images/' . $image_name;
            if (!move_uploaded_file($tmp, $dest)) {
                $message = 'Không thể lưu file ảnh.';
                $message_type = 'danger';
            }
        }

        if ($message_type !== 'danger') {
            // products table uses 'manufacturer_id' as foreign key to nhasanxuat
            $sql = "INSERT INTO products (name, price, stock, description, image, category_id, manufacturer_id) VALUES ('" . $name . "', " . $price . ", " . $stock . ", '" . $description . "', '" . $image_name . "', " . $category_id . ", " . $nhaSanXuat_id . ")";
            if ($conn->query($sql) === TRUE) {
                $newId = $conn->insert_id;
                $redirectUrl = 'index.php?page=product&id=' . $newId;
                if (!headers_sent()) {
                    header('Location: ' . $redirectUrl);
                    exit();
                } else {
                    echo '<script>window.location.href="' . $redirectUrl . '";</script>';
                    echo '<noscript><meta http-equiv="refresh" content="0;url=' . htmlspecialchars($redirectUrl) . '"></noscript>';
                    exit();
                }
            } else {
                $message = 'Lỗi khi thêm sản phẩm: ' . $conn->error;
                $message_type = 'danger';
            }
        }
    }
}
?>

<div class="container mt-5">
    <h2>Thêm Sản Phẩm</h2>

    <?php if ($message): ?>
        <div class="alert alert-<?php echo htmlspecialchars($message_type ?: 'info'); ?>"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" style="max-width:800px;">
        <div class="form-group">
            <label for="name">Tên</label>
            <input type="text" name="name" id="name" class="form-control" required value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label for="price">Giá</label>
            <input type="number" name="price" id="price" class="form-control" required min="0" value="<?php echo htmlspecialchars($_POST['price'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label for="stock">Số lượng</label>
            <input type="number" name="stock" id="stock" class="form-control" value="<?php echo htmlspecialchars($_POST['stock'] ?? '0'); ?>" min="0">
        </div>
        <div class="form-group">
            <label for="category_id">Danh mục</label>
            <select name="category_id" id="category_id" class="form-control">
                <option value="0">-- Chọn --</option>
                <?php foreach ($categories as $c): ?>
                    <option value="<?php echo $c['id']; ?>" <?php echo ($c['id'] == $prefill_category) ? 'selected' : ''; ?>><?php echo htmlspecialchars($c['name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="nhaSanXuat_id">Nhà sản xuất</label>
            <select name="nhaSanXuat_id" id="nhaSanXuat_id" class="form-control">
                <option value="0">-- Chọn --</option>
                <?php foreach ($nhaSanXuats as $n): ?>
                    <option value="<?php echo $n['id']; ?>" <?php echo ($n['id'] == $prefill_manufacturer) ? 'selected' : ''; ?>><?php echo htmlspecialchars($n['name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="description">Mô tả</label>
            <textarea name="description" id="description" class="form-control" rows="5"><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
        </div>
        <div class="form-group">
            <label for="image">Ảnh</label>
            <input type="file" name="image" id="image" class="form-control-file" accept="image/*">
        </div>
        <button type="submit" class="btn btn-success">Thêm</button>
        <?php
        $returnUrl = 'index.php';
        if (!empty($_GET['return'])) {
            $returnUrl = $_GET['return'];
        } elseif (!empty($_SERVER['HTTP_REFERER'])) {
            $ref = $_SERVER['HTTP_REFERER'];
            $p = parse_url($ref);
            $returnUrl = ($p['path'] ?? '') . (isset($p['query']) ? '?' . $p['query'] : '');
            if ($returnUrl === '') $returnUrl = 'index.php';
        }
        ?>
        <a href="<?php echo htmlspecialchars($returnUrl); ?>" class="btn btn-secondary" onclick="event.preventDefault(); history.back();">Hủy</a>
    </form>
</div>

<?php
$conn->close();
?>
