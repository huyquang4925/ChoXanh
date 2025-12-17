<?php
require_once __DIR__ . '/../config/db.php';

$sql = "SELECT id, name FROM categories";
$result = $conn->query($sql);

// Lưu tất cả categories vào mảng
$categories = [];
while ($row = $result->fetch_assoc()) {
    $categories[] = $row;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop Tết - Chợ Xanh</title>
    <link rel="stylesheet" href="css/head.css">
</head>
<body>

<!-- Header -->
<header>
    <div class="header-container">
        <div class="header-top">
            <a href="index.php" class="logo-link"> <!-- Thêm thẻ <a> ở đây -->
                <div class="logo">🎊 Shop Tết</div>
            </a> 
            
            <ul class="menu">
                <?php 
                // Hiển thị 8 categories đầu tiên
                $visibleCount = 8;
                $totalCategories = count($categories);
                
                for ($i = 0; $i < min($visibleCount, $totalCategories); $i++): 
                    $cat = $categories[$i];
                ?>
                    <li>
                        <a href="index.php?page=category&id=<?= $cat['id'] ?>">
                            <?= htmlspecialchars($cat['name']) ?>
                        </a>
                    </li>
                <?php endfor; ?>       
            </ul>

            <!-- Auth Buttons -->
            <div class="login-form">
                <a href="index.php?page=register" class="register-btn">Đăng ký</a>
                <a href="index.php?page=login" class="login-btn">Đăng nhập</a>
            </div>
        </div>
    </div>
</header>

<div class="bottom-bar">
    <div class="bottom-container">
        <div class="ads-section">
            <div class="ads-text">
                <span>🔥 Siêu Sale Tết 2026 - Giảm đến <b>50%</b> tất cả sản phẩm! Mua ngay kẻo lỡ! 🔥</span>
            </div>
        </div>

        <a href="index.php?page=cart" class="cart-button"><i class="fa-solid fa-cart-shopping"></i>Giỏ hàng<span class="cart-badge">0</span>
        </a>
    </div>
</div>