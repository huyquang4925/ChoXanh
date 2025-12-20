<?php
// Bật hiển thị lỗi để debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Lấy tham số page từ URL
$page = $_GET['page'] ?? 'home';

// Danh sách các trang được phép
$allowedPages = ['home', 'about', 'product', 'contact', 'news', 'category', 'register', 'login', 'product_sua'];

// Kiểm tra page có hợp lệ không
if (!in_array($page, $allowedPages)) {
    $page = 'home';
}

// Include header
require_once __DIR__ . '/partials/head.php';
?>

<div class="main-content-wrapper">
    <?php
    require_once __DIR__ . '/pages/' . $page . '.php';
    ?>
</div>

<?php
// Include footer
require_once __DIR__ . '/partials/footer.php';
?>