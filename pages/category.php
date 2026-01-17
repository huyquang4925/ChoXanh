<?php
$id = $_GET['id'] ?? 0;
$search = $_GET['search'] ?? '';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

// Lấy thông tin danh mục
$sql = "SELECT name FROM categories WHERE id = $id";
$result = $conn->query($sql);
$category = $result->fetch_assoc();

// Lấy sản phẩm theo category_id
$sql_products = "SELECT p.*, c.name as category_name 
                 FROM products p 
                 LEFT JOIN categories c ON p.category_id = c.id 
                 WHERE p.category_id = $id";

// Thêm điều kiện tìm kiếm nếu có
if (!empty($search)) {
    $search_safe = $conn->real_escape_string($search);
    $sql_products .= " AND (p.name LIKE '%$search_safe%' OR p.description LIKE '%$search_safe%')";
}


$products_result = $conn->query($sql_products);
?>

<link rel="stylesheet" href="css/category.css">

<div class="category-container">
    
    <!-- Category Header -->
    <div class="category-header">
        <div class="category-breadcrumb">
            <a href="index.php">Trang chủ</a> / 
            <span><?= htmlspecialchars($category['name'] ?? 'Không tồn tại') ?></span>
        </div>
        <div style="display:flex; align-items:center; gap:12px;">
            <h1 class="category-title"><?= htmlspecialchars($category['name'] ?? 'Không tồn tại') ?></h1>
            <?php if ($isAdmin): ?>
                <a href="index.php?page=product_add&category_id=<?php echo intval($id); ?>" class="btn btn-success" style="font-size:14px; padding:6px 10px;">Thêm sản phẩm</a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Search Toolbar -->
    <div class="search-toolbar" id="searchToolbar">
        <form action="" method="GET" class="search-form">
            <input type="hidden" name="page" value="category">
            <input type="hidden" name="id" value="<?= $id ?>">
            
            <div class="search-input-wrapper">
                <input 
                    type="text" 
                    name="search" 
                    class="search-input" 
                    placeholder="Tìm kiếm sản phẩm trong danh mục này..." 
                    value="<?= htmlspecialchars($search) ?>"
                >
                <button type="submit" class="search-btn">🔍 Tìm</button>
            </div>

            <div class="filter-buttons">
                <button type="button" class="filter-btn active" onclick="sortProducts('default')">Mặc định</button>
                <button type="button" class="filter-btn" onclick="sortProducts('price-asc')">Giá tăng dần</button>
                <button type="button" class="filter-btn" onclick="sortProducts('price-desc')">Giá giảm dần</button>
                <button type="button" class="filter-btn" onclick="sortProducts('newest')">Mới nhất</button>
            </div>
        </form>
    </div>

    <?php if ($products_result && $products_result->num_rows > 0): ?>
        <!-- Products Count -->
        <div class="products-count">
            Tìm thấy <strong><?= $products_result->num_rows ?></strong> sản phẩm
            <?php if (!empty($search)): ?>
                với từ khóa "<strong><?= htmlspecialchars($search) ?></strong>"
            <?php endif; ?>
        </div>

        <!-- Products Grid -->
        <div class="products-grid" id="productsGrid">
            <?php while ($product = $products_result->fetch_assoc()): ?>
                <div class="product-card" onclick="viewProduct(<?= $product['id'] ?>)">
                    
                    <?php if ($product['stock'] > 0): ?>
                        <div class="stock-badge <?= $product['stock'] < 10 ? 'low' : '' ?>">
                            Còn <?= $product['stock'] ?> SP
                        </div>
                    <?php else: ?>
                        <div class="stock-badge out">Hết hàng</div>
                    <?php endif; ?>

                   <div class="product-image">
                        <?php if (!empty($product['image'])): ?>
                            <img src="images/<?= htmlspecialchars($product['image']) ?>" 
                                alt="<?= htmlspecialchars($product['name']) ?>">
                        <?php else: ?>
                            <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                                        width: 100%; height: 100%; 
                                        display: flex; align-items: center; justify-content: center; 
                                        color: #fff;">
                                Không có ảnh
                            </div>
                        <?php endif; ?>
                    </div>


                    <div class="product-info">
                        <div class="product-name"><?= htmlspecialchars($product['name']) ?></div>
                        <div class="product-price">
                            <span class="price-new"><?= number_format($product['price'], 0, ',', '.') ?>đ</span>
                        </div>
                        <form method="post" action="index.php?page=cart_add" style="display:inline;" onsubmit="event.stopPropagation();">
                            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                            <button class="add-to-cart-btn" type="submit">Thêm vào giỏ</button>
                        </form>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

    <?php else: ?>
        <!-- Empty State -->
        <div class="empty-state">
            <div class="empty-state-icon">📦</div>
            <h3>Không tìm thấy sản phẩm</h3>
            <p>
                <?php if (!empty($search)): ?>
                    Không có sản phẩm nào phù hợp với từ khóa "<?= htmlspecialchars($search) ?>"
                <?php else: ?>
                    Danh mục này chưa có sản phẩm nào.
                <?php endif; ?>
            </p>
        </div>
    <?php endif; ?>

</div>

<script>
    // Sticky Toolbar on Scroll
    window.addEventListener('scroll', function() {
        const toolbar = document.getElementById('searchToolbar');
        if (window.scrollY > 100) {
            toolbar.classList.add('scrolled');
        } else {
            toolbar.classList.remove('scrolled');
        }
    });

    // Sort Products
    function sortProducts(type) {
        // Remove active class from all buttons
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        
        // Add active class to clicked button
        event.target.classList.add('active');

        const grid = document.getElementById('productsGrid');
        const products = Array.from(grid.children);

        products.sort((a, b) => {
            const priceA = parseInt(a.querySelector('.price-new').textContent.replace(/\D/g, ''));
            const priceB = parseInt(b.querySelector('.price-new').textContent.replace(/\D/g, ''));

            switch(type) {
                case 'price-asc':
                    return priceA - priceB;
                case 'price-desc':
                    return priceB - priceA;
                case 'newest':
                    // Would need timestamp data for real implementation
                    return 0;
                default:
                    return 0;
            }
        });

        // Clear and re-append sorted products
        grid.innerHTML = '';
        products.forEach(product => grid.appendChild(product));
    }

    // View Product Detail
    function viewProduct(productId) {
        window.location.href = 'index.php?page=product&id=' + productId;
    }
</script>