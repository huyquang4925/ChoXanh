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
    <link rel="stylesheet" href="../css/head.css">
</head>
<body>

<!-- Header -->
<header>
    <div class="header-container">
        <div class="header-top">
            <div class="logo">🎊 Shop Tết</div>
            
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
                
                <!-- Nút tìm kiếm -->
                <li class="search-item">
                    <a href="index.php?page=search">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8"></circle>
                            <path d="m21 21-4.35-4.35"></path>
                        </svg>
                        Tìm kiếm
                    </a>
                </li>
            </ul>
        </div>
    </div>
</header>

<!-- Bottom Bar - Đăng ký & Giỏ hàng -->
<div class="bottom-bar">
    <div class="bottom-container">
        <!-- Register Section -->
        <div class="register-section">
            <div class="register-text">
                🎊 <span>Đăng ký ngay</span> để nhận ưu đãi đặc biệt!
            </div>
            <a href="index.php?page=register" class="register-btn">
                Đăng ký tại đây
            </a>
        </div>

        <!-- Cart Button -->
        <a href="index.php?page=cart" class="cart-button">
            <svg class="cart-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="9" cy="21" r="1"></circle>
                <circle cx="20" cy="21" r="1"></circle>
                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
            </svg>
            Giỏ hàng
            <span class="cart-badge">0</span>
        </a>
    </div>
</div>