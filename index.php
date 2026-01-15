<?php
// Bật hiển thị lỗi để debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Lấy tham số page từ URL
$page = $_GET['page'] ?? 'home';

// Danh sách các trang được phép
$allowedPages = ['home', 'about_us', 'product', 'contact',
 'news', 'category', 'register', 'login', 'product_sua',
  'logout', 'product_del', 'product_add', 'admin_categories',
   'category_add', 'category_edit', 'category_delete', 'admin_partners',
    'partner_add', 'partner_edit', 'partner_delete', 'admin_staff',
     'staff_add', 'staff_edit', 'staff_delete', 'admin_orders', 'order_view',
      'cart', 'cart_add', 'cart_update', 'cart_remove','partners_detail', 'profile',
       'settings', 'edit_profile', 'lichsu', 'payment'];

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