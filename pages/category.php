<?php
$id = $_GET['id'] ?? 0;
$search = $_GET['search'] ?? '';

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

$sql_products .= " ORDER BY p.created_at DESC";
$products_result = $conn->query($sql_products);
?>

<style>
    .category-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }

    /* Category Header */
    .category-header {
        margin-bottom: 20px;
    }

    .category-title {
        font-size: 32px;
        font-weight: bold;
        color: #2c3e50;
        margin-bottom: 10px;
    }

    .category-breadcrumb {
        color: #7f8c8d;
        font-size: 14px;
    }

    .category-breadcrumb a {
        color: #3498db;
        text-decoration: none;
    }

    .category-breadcrumb a:hover {
        text-decoration: underline;
    }

    /* Search Toolbar - Sticky */
    .search-toolbar {
        background: #fff;
        padding: 15px 20px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        margin-bottom: 30px;
        position: sticky;
        top: 70px;
        z-index: 100;
        transition: all 0.3s ease;
    }

    .search-toolbar.scrolled {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .search-form {
        display: flex;
        gap: 15px;
        align-items: center;
        flex-wrap: wrap;
    }

    .search-input-wrapper {
        flex: 1;
        min-width: 250px;
        position: relative;
    }

    .search-input {
        width: 100%;
        padding: 12px 45px 12px 20px;
        border: 2px solid #e0e0e0;
        border-radius: 25px;
        font-size: 15px;
        transition: all 0.3s ease;
    }

    .search-input:focus {
        outline: none;
        border-color: #e74c3c;
        box-shadow: 0 0 0 3px rgba(231, 76, 60, 0.1);
    }

    .search-btn {
        position: absolute;
        right: 5px;
        top: 50%;
        transform: translateY(-50%);
        background: #e74c3c;
        color: #fff;
        border: none;
        padding: 8px 20px;
        border-radius: 20px;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .search-btn:hover {
        background: #c0392b;
    }

    .filter-buttons {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .filter-btn {
        padding: 8px 20px;
        border: 2px solid #e0e0e0;
        background: #fff;
        border-radius: 20px;
        cursor: pointer;
        font-size: 14px;
        transition: all 0.3s ease;
    }

    .filter-btn:hover, .filter-btn.active {
        border-color: #e74c3c;
        background: #e74c3c;
        color: #fff;
    }

    /* Products Count */
    .products-count {
        margin-bottom: 20px;
        color: #7f8c8d;
        font-size: 15px;
    }

    .products-count strong {
        color: #2c3e50;
    }

    /* Products Grid */
    .products-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-bottom: 50px;
    }

    .product-card {
        background: #fff;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        position: relative;
        cursor: pointer;
    }

    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    }

    .product-badge {
        position: absolute;
        top: 10px;
        left: 10px;
        background: #e74c3c;
        color: #fff;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        z-index: 1;
    }

    .stock-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        background: #27ae60;
        color: #fff;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        z-index: 1;
    }

    .stock-badge.low {
        background: #f39c12;
    }

    .stock-badge.out {
        background: #95a5a6;
    }

    .product-image {
        width: 100%;
        height: 250px;
        object-fit: cover;
        background: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        color: #7f8c8d;
    }

    .product-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .product-info {
        padding: 15px;
    }

    .product-name {
        font-size: 16px;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 10px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        min-height: 48px;
    }

    .product-price {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 15px;
    }

    .price-new {
        font-size: 20px;
        font-weight: bold;
        color: #e74c3c;
    }

    .add-to-cart-btn {
        width: 100%;
        background: #e74c3c;
        color: #fff;
        border: none;
        padding: 12px;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .add-to-cart-btn:hover {
        background: #c0392b;
        transform: scale(1.02);
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
    }

    .empty-state-icon {
        font-size: 64px;
        margin-bottom: 20px;
    }

    .empty-state h3 {
        font-size: 24px;
        color: #2c3e50;
        margin-bottom: 10px;
    }

    .empty-state p {
        color: #7f8c8d;
        font-size: 16px;
    }

    /* Responsive */
    @media (max-width: 1024px) {
        .products-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    @media (max-width: 768px) {
        .search-toolbar {
            top: 60px;
        }

        .products-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .filter-buttons {
            width: 100%;
        }

        .filter-btn {
            flex: 1;
        }
    }

    @media (max-width: 480px) {
        .products-grid {
            grid-template-columns: 1fr;
        }

        .category-title {
            font-size: 24px;
        }
    }
</style>

<div class="category-container">
    
    <!-- Category Header -->
    <div class="category-header">
        <div class="category-breadcrumb">
            <a href="index.php">Trang chủ</a> / 
            <span><?= htmlspecialchars($category['name'] ?? 'Không tồn tại') ?></span>
        </div>
        <h1 class="category-title"><?= htmlspecialchars($category['name'] ?? 'Không tồn tại') ?></h1>
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
                <button type="submit" class="search-btn">
                    🔍 Tìm
                </button>
            </div>

            <div class="filter-buttons">
                <button type="button" class="filter-btn active" onclick="sortProducts('default')">
                    Mặc định
                </button>
                <button type="button" class="filter-btn" onclick="sortProducts('price-asc')">
                    Giá tăng dần
                </button>
                <button type="button" class="filter-btn" onclick="sortProducts('price-desc')">
                    Giá giảm dần
                </button>
                <button type="button" class="filter-btn" onclick="sortProducts('newest')">
                    Mới nhất
                </button>
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
            <?php while ($product = $products_result->fetch_assoc()): 
                $discount = rand(20, 50);
                $discounted_price = $product['price'] * (100 - $discount) / 100;
            ?>
                <div class="product-card" onclick="viewProduct(<?= $product['id'] ?>)">
                    <div class="product-badge">-<?= $discount ?>%</div>
                    
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
                            <span class="price-new"><?= number_format($discounted_price, 0, ',', '.') ?>đ</span>
                            <span style="font-size: 14px; color: #95a5a6; text-decoration: line-through;">
                                <?= number_format($product['price'], 0, ',', '.') ?>đ
                            </span>
                        </div>
                        <button class="add-to-cart-btn" onclick="event.stopPropagation(); addToCart(<?= $product['id'] ?>)">
                            🛒 Thêm vào giỏ
                        </button>
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

    // Add to Cart
    function addToCart(productId) {
        // TODO: Implement add to cart logic
        alert('Đã thêm sản phẩm ' + productId + ' vào giỏ hàng!');
    }
</script>