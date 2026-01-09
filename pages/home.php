<?php
// Lấy danh mục
$limit = 9; 
$page = isset($_GET['p']) ? (int)$_GET['p'] : 1;
$page = max($page, 1);
$offset = ($page - 1) * $limit;
$sql_categories = "SELECT * FROM categories LIMIT 6";
$categories_result = $conn->query($sql_categories);

// Lấy sản phẩm
$sql_products = "
    SELECT p.*, c.name AS category_name
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.id
    ORDER BY p.id DESC
    LIMIT $limit OFFSET $offset
";

$products_result = $conn->query($sql_products);

// Lấy sản phẩm
$products = [];
if ($products_result) {
    while ($row = $products_result->fetch_assoc()) {
        $products[] = $row;
    }
}

$featured_products = $products; // Hiển thị sản phẩm theo trang

// Tính tổng số sản phẩm để phân trang
$total_result = $conn->query("SELECT COUNT(*) AS total FROM products");
$total_row = $total_result->fetch_assoc();
$total_products = $total_row['total'];

$total_pages = ceil($total_products / $limit);
?>

<link rel="stylesheet" href="css/home.css">

<div class="banner-slider">
    <div class="banner-slide active">
        <img src="images/mansion_test.jpg" alt="Máy lạnh giảm sốc">
    </div>
    <div class="banner-slide">
        <img src="images/mansion_test.jpg" alt="Máy lạnh LG Inverter">
    </div>
    <div class="banner-slide">
        <img src="images/mansion_test.jpg" alt="Mua Panasonic trúng xe điện">
    </div>
    
    <div class="banner-dots">
        <span class="dot active" onclick="currentSlide(0)"></span>
        <span class="dot" onclick="currentSlide(1)"></span>
        <span class="dot" onclick="currentSlide(2)"></span>
    </div>
</div>

<div class="home-container">
    <!-- Categories Section -->
    <h2 class="section-title">Danh mục nổi bật</h2>
    <div class="categories-grid">
        <?php 
        if ($categories_result):
            while($cat = $categories_result->fetch_assoc()): 
        ?>
            <a href="index.php?page=category&id=<?= $cat['id'] ?>" class="category-card">
                <div class="category-name" style="font-size: 16px;"><?= htmlspecialchars($cat['name']) ?></div>
            </a>
        <?php 
            endwhile;
        endif;
        ?>
    </div>

    <!-- Featured Products -->
    <h2 class="section-title">🔥 Sản phẩm nổi bật</h2>
    <div class="categories-wrapper">
        <div class="category-banner">
            <img src="images/fridge_test.jpg" alt="Banner Điện tử - Điện lạnh">
        </div>
        
        <div class="products-grid">
            <?php 
            if (count($featured_products) > 0):
                foreach($featured_products as $product): 
                    $discount = rand(20, 50);
                    $original_price = $product['price'];
                    $discounted_price = $original_price * (100 - $discount) / 100;
            ?>
                <div class="product-card" onclick="viewProduct(<?= $product['id'] ?>)">
                    <div class="product-badge">-<?= $discount ?>%</div>
                    
                    <?php if ($product['stock'] > 0): ?>
                        <div class="stock-badge <?= $product['stock'] < 10 ? 'low' : '' ?>">
                            Còn <?= $product['stock'] ?> SP
                        </div>
                    <?php endif; ?>

                    <div class="product-image">
                        <?php if (!empty($product['image'])): ?>
                            <img src="images/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                        <?php else: ?>
                            <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: #fff;">
                                Không có ảnh
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="product-info">
                        <div class="product-name"><?= htmlspecialchars($product['name']) ?></div>
                        <div class="product-price">
                            <span class="price-new"><?= number_format($discounted_price, 0, ',', '.') ?>đ</span>
                            <span class="price-old"><?= number_format($original_price, 0, ',', '.') ?>đ</span>
                        </div>
                        <form method="post" action="index.php?page=cart_add" style="display:inline;" onsubmit="event.stopPropagation();">
                            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                            <button class="add-to-cart-btn" type="submit">🛒 Thêm vào giỏ</button>
                        </form>
                    </div>
                </div>
            <?php 
                endforeach;
            else:
            ?>
                <div style="grid-column: 1/-1; text-align: center; padding: 40px; color: #7f8c8d;">
                    <p style="font-size: 18px;">Không có sản phẩm nào</p>
                </div>
            <?php
            endif;
            ?>
        </div>
    </div>

    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
        <div class="pagination">
            <!-- Previous Button -->
            <?php if ($page > 1): ?>
                <a href="index.php?page=home&p=<?= $page - 1 ?>">← Trước</a>
            <?php else: ?>
                <a href="#" class="disabled">← Trước</a>
            <?php endif; ?>

            <!-- Page Numbers -->
            <?php
            // Hiển thị tối đa 7 trang
            $range = 2; // Số trang hiển thị mỗi bên
            $start = max(1, $page - $range);
            $end = min($total_pages, $page + $range);

            // Trang đầu
            if ($start > 1): ?>
                <a href="index.php?page=home&p=1">1</a>
                <?php if ($start > 2): ?>
                    <span class="page-info">...</span>
                <?php endif; ?>
            <?php endif; ?>

            <!-- Các trang ở giữa -->
            <?php for ($i = $start; $i <= $end; $i++): ?>
                <a href="index.php?page=home&p=<?= $i ?>" class="<?= $i == $page ? 'active' : '' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>

            <!-- Trang cuối -->
            <?php if ($end < $total_pages): ?>
                <?php if ($end < $total_pages - 1): ?>
                    <span class="page-info">...</span>
                <?php endif; ?>
                <a href="index.php?page=home&p=<?= $total_pages ?>"><?= $total_pages ?></a>
            <?php endif; ?>

            <!-- Next Button -->
            <?php if ($page < $total_pages): ?>
                <a href="index.php?page=home&p=<?= $page + 1 ?>">Sau →</a>
            <?php else: ?>
                <a href="#" class="disabled">Sau →</a>
            <?php endif; ?>

            <!-- Page Info -->
            <span class="page-info">
                Trang <?= $page ?>/<?= $total_pages ?> (<?= $total_products ?> sản phẩm)
            </span>
        </div>
    <?php endif; ?>
</div>

<script>
    // Banner Slider
    let currentSlideIndex = 0;
    const slides = document.querySelectorAll('.banner-slide');
    const dots = document.querySelectorAll('.dot');

    function showSlide(index) {
        slides.forEach(slide => slide.classList.remove('active'));
        dots.forEach(dot => dot.classList.remove('active'));
        
        slides[index].classList.add('active');
        dots[index].classList.add('active');
    }

    function nextSlide() {
        currentSlideIndex = (currentSlideIndex + 1) % slides.length;
        showSlide(currentSlideIndex);
    }

    function currentSlide(index) {
        currentSlideIndex = index;
        showSlide(index);
    }

    // Auto slide every 5 seconds
    setInterval(nextSlide, 5000);

    // View Product Detail
    function viewProduct(productId) {
        window.location.href = 'index.php?page=product&id=' + productId;
    }

    // Scroll to top when changing page
    if (window.location.search.includes('p=')) {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
</script>