<?php
$isLoggedIn = false;
$username = '';
$role = '';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/db.php';

$sql = "SELECT id, name FROM categories";
$result = $conn->query($sql);

// Lưu tất cả categories vào mảng
$categories = [];
while ($row = $result->fetch_assoc()) {
    $categories[] = $row;
}

if (isset($_SESSION['user_id'])) {
    $isLoggedIn = true;
    $username = htmlspecialchars($_SESSION['username']);
    $role = htmlspecialchars($_SESSION['role']);
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop Tết - Chợ Xanh</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/head.css">
    <link rel="stylesheet" href="css/product.css">
    <link rel="stylesheet" href="css/auth.css">
    <link rel="stylesheet" href="css/profile.css">

</head>

<body>

    <!-- Header -->
    <header>
        <div class="header-container">
            <div class="header-top">
                <a href="index.php" class="logo-link" style="color: aliceblue !important; text-decoration: none !important;">
                    <div class="logo" style="color: inherit; text-decoration: none;">🎊 Shop Tết</div>
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

                <?php if ($isLoggedIn): ?>
                    <div class="user-menu-container">
                        <div class="user-avatar-wrapper" id="userAvatar">
                            <div class="user-avatar">
                                <i class="fa-solid fa-user"></i>
                            </div>
                            <i class="fas fa-chevron-down caret-icon"></i>
                        </div>

                        <div class="user-dropdown-menu" id="userDropdown">
                            <div class="user-info-header">
                                <div class="user-avatar small">
                                    <i class="fa-solid fa-user"></i>
                                </div>
                                <div class="user-text">
                                    <h6><?= $username ?></h6>
                                    <small><?= ucfirst($role) ?></small>
                                </div>
                            </div>
                            <hr>
                            <a href="index.php?page=profile">
                                <div class="icon-circle"><i class="fas fa-user-circle"></i></div>
                                <span>Thông tin cá nhân</span>
                            </a>
                            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                                <a href="index.php?page=admin_categories">
                                    <div class="icon-circle"><i class="fas fa-list"></i></div>
                                    <span>Quản lý Danh mục</span>
                                </a>
                                <a href="index.php?page=admin_partners">
                                    <div class="icon-circle"><i class="fas fa-handshake"></i></div>
                                    <span>Quản lý Đối tác</span>
                                </a>
                                <a href="index.php?page=admin_staff">
                                    <div class="icon-circle"><i class="fas fa-users-cog"></i></div>
                                    <span>Quản lý Nhân viên</span>
                                </a>
                                <a href="index.php?page=admin_orders">
                                    <div class="icon-circle"><i class="fas fa-receipt"></i></div>
                                    <span>Quản lý Đơn hàng</span>
                                </a>
                            <?php endif; ?>
                            <a href="index.php?page=settings">
                                <div class="icon-circle"><i class="fas fa-cog"></i></div>
                                <span>Cài đặt</span>
                            </a>
                            <a href="index.php?page=logout" class="logout-link">
                                <div class="icon-circle"><i class="fas fa-sign-out-alt"></i></div>
                                <span>Đăng xuất</span>
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                        <div class="login-form">
                            <a href="index.php?page=register" class="register-btn" style="color: aliceblue !important; text-decoration: none !important;">Đăng ký</a>
                            <a href="index.php?page=login" class="login-btn" style="color: aliceblue !important; text-decoration: none !important;">Đăng nhập</a>
                        </div>
                <?php endif; ?>

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

            <a href="index.php?page=cart" class="cart-button"><i class="fa-solid fa-cart-shopping"></i>Giỏ hàng</a>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const userAvatar = document.getElementById('userAvatar');
            const userDropdown = document.getElementById('userDropdown');

            if (userAvatar && userDropdown) {
                userAvatar.addEventListener('click', function () {
                    userDropdown.classList.toggle('show');
                });

                // Close the dropdown if the user clicks outside of it
                window.addEventListener('click', function (event) {
                    if (!userAvatar.contains(event.target) && !userDropdown.contains(event.target)) {
                        userDropdown.classList.remove('show');
                    }
                });
            }
        });
    </script>