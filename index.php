<?php
// Bật hiển thị lỗi để debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Lấy tham số page từ URL
$page = $_GET['page'] ?? 'home';

// Danh sách các trang được phép
$allowedPages = ['home', 'about', 'product', 'contact', 'news', 'category', 'search'];

// Kiểm tra page có hợp lệ không
if (!in_array($page, $allowedPages)) {
    $page = 'home';
}

// Include header
require_once __DIR__ . '/partials/head.php';

// Include nội dung trang
require_once __DIR__ . '/pages/' . $page . '.php';

// Include footer
require_once __DIR__ . '/partials/footer.php';