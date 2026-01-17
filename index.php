<?php
/**
 * MVC Application Entry Point
 * Chợ Xanh - Shop Tết
 */

// Bật hiển thị lỗi để debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

<<<<<<< HEAD
// Start session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
=======
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
       'settings', 'edit_profile', 'lichsu', 'payment', "profile_edit", "profile_password",'review_add','news_add','review_del','news'];

// Kiểm tra page có hợp lệ không
if (!in_array($page, $allowedPages)) {
    $page = 'home';
>>>>>>> 3a7af47ca808f834449330ebed37e9c17ba47a43
}

// Load core files
require_once __DIR__ . '/core/Database.php';
require_once __DIR__ . '/core/Model.php';
require_once __DIR__ . '/core/Controller.php';
require_once __DIR__ . '/core/App.php';

// Include header
require_once __DIR__ . '/partials/head.php';
?>

<div class="main-content-wrapper">
    <?php
    // Initialize the application (Router)
    $app = new App();
    ?>
</div>

<?php
// Include footer
require_once __DIR__ . '/partials/footer.php';
?>