<?php
// Lấy danh mục
$sql_categories = "SELECT * FROM categories ";
$categories_result = $conn->query($sql_categories);

// Lấy sản phẩm ngẫu nhiên
$sql_products = "SELECT p.*, c.name as category_name 
                 FROM products p 
                 LEFT JOIN categories c ON p.category_id = c.id 
                 ORDER BY RAND() 
                 ";
$products_result = $conn->query($sql_products);

// Chia sản phẩm thành 2 phần
$products = [];
if ($products_result) {
    while ($row = $products_result->fetch_assoc()) {
        $products[] = $row;
    }
}

$featured_products = array_slice($products, 0, 9);
$new_products = array_slice($products, 9, 9);
?>

<style>
    .home-container {
        max-width: 1900px;
        margin: 0 auto;
        
        padding: 20px;
    }

    /* Banner Slider */
    .banner-slider {
        position: relative;
        width: 100%;
        height: 400px;
        border-radius: 12px;
        overflow: hidden;
        margin-bottom: 40px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        padding: 20px;
    }

    .banner-slide {
        display: none;
        width: 100%;
        height: 100%;
        position: relative;
    }

    .banner-slide.active {
        display: block;
        animation: fadeIn 0.5s ease-in;
    }

    .banner-slide img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    .banner-dots {
        position: absolute;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
        display: flex;
        gap: 10px;
        z-index: 10;
    }

    .dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.5);
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .dot.active {
        background: #fff;
        width: 30px;
        border-radius: 6px;
    }

    /* Section Title */
    .section-title {
        font-size: 32px;
        font-weight: bold;
        color: #2c3e50;
        margin-bottom: 30px;
        text-align: center;
        position: relative;
        padding-bottom: 15px;
    }

    .section-title::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 100px;
        height: 4px;
        background: linear-gradient(90deg, #e74c3c, #f39c12);
        border-radius: 2px;
    }

    /* Categories Grid */
    .categories-grid {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
        gap: 20px;
        margin-bottom: 50px;
    }

    .category-card {
        background: #fff;
        padding: 20px 15px;
        border-radius: 12px;
        text-align: center;
        text-decoration: none;
        color: #2c3e50;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 80px;
    }

    .category-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(231, 76, 60, 0.2);
        background: #e74c3c;
        color: #fff;
    }

    .category-name {
        font-size: 14px;
        font-weight: 600;
    }
    .categories-wrapper {
    display: grid;
    grid-template-columns: 2fr 8fr;
    gap: 20px;
    margin-bottom: 50px;
    }
    .category-banner {
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,.1);
}

    .category-banner img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }



    /* Products Grid */
    .products-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
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
        display: flex;
        flex-direction: row;
        padding: 20px;
        gap: 20px;
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

    .product-image {
        width: 200px;
        height: 200px;
        min-width: 200px;
        background: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        overflow: hidden;
    }

    .product-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .product-info {
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding: 0;
    }

    .product-name {
        font-size: 18px;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 10px;
        line-height: 1.4;
    }

    .product-details {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 8px;
        margin-bottom: 15px;
    }

    .product-detail-item {
        font-size: 14px;
        color: #7f8c8d;
    }

    .product-detail-item strong {
        color: #2c3e50;
        font-weight: 600;
    }

    .product-price {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 15px;
    }

    .price-new {
        font-size: 24px;
        font-weight: bold;
        color: #e74c3c;
    }

    .price-old {
        font-size: 14px;
        color: #95a5a6;
        text-decoration: line-through;
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

    /* Banner Mid */
    .banner-mid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
        margin: 50px 0;
    }

    .banner-mid-item {
        height: 200px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 24px;
        font-weight: bold;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .banner-mid-item:hover {
        transform: scale(1.02);
    }

    /* Responsive */
    @media (max-width: 1024px) {
        .categories-grid {
            grid-template-columns: repeat(4, 1fr);
        }
        .products-grid {
            grid-template-columns: 1fr;
        }
        .product-image {
            width: 150px;
            height: 150px;
            min-width: 150px;
        }
    }

    @media (max-width: 768px) {
        .banner-slider {
            height: 250px;
        }
        .categories-grid {
            grid-template-columns: repeat(3, 1fr);
        }
        .products-grid {
            grid-template-columns: 1fr;
        }
        .product-card {
            flex-direction: column;
        }
        .product-image {
            width: 100%;
            height: 200px;
        }
        .banner-mid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 480px) {
        .banner-slider {
            height: 200px;
        }
        .categories-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        .products-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

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
            for($i = 1; $i <= 8; $i++): 
        ?>
            <div class="product-card">
                <div class="product-badge">-<?= rand(20, 50) ?>%</div>
                <div class="product-image" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; display: flex; align-items: center; justify-content: center;">
                    Sản phẩm <?= $i ?>
                </div>
                <div class="product-info">
                    <div class="product-name">Tên sản phẩm <?= $i ?> - Mô tả ngắn</div>
                    <div class="product-price">
                        <span class="price-new"><?= number_format(rand(100000, 500000)) ?>đ</span>
                        <span class="price-old"><?= number_format(rand(600000, 1000000)) ?>đ</span>
                    </div>
                    <button class="add-to-cart-btn">🛒 Thêm vào giỏ</button>
                </div>
            </div>
        <?php 
            endfor;
        endif;
        ?>
    </div>

    </div>
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
</script>