<style>
    /* Container cho nội dung */
    .home-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }

    /* Banner Full Width */
    .banner-wrapper {
        width: 100vw;
        position: relative;
        margin-top: 6px;
        margin-left: calc(50% - 50vw);
        margin-right: calc(50% - 50vw);

        margin-bottom: 40px;
    }

    /* Banner Slider */
    .banner-slider {
        position: relative;
        width: 100%;
        height: 420px;
        overflow: hidden;
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

    /* Dots */
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

    /* Responsive */
    @media (max-width: 768px) {
        .banner-slider {
            height: 260px;
        }
    }

    @media (max-width: 480px) {
        .banner-slider {
            height: 200px;
        }
    }
</style>

<!-- Banner Full Width -->
<div class="banner-wrapper">
    <div class="banner-slider">
        <div class="banner-slide active">
            <img src="images/ad1.png" alt="Máy lạnh giảm sốc">
        </div>
        <div class="banner-slide">
            <img src="images/ad2.png" alt="Máy lạnh LG Inverter">
        </div>
        <div class="banner-slide">
            <img src="images/ad3.png" alt="Mua Panasonic trúng xe điện">
        </div>

        <div class="banner-dots">
            <span class="dot active" onclick="currentSlide(0)"></span>
            <span class="dot" onclick="currentSlide(1)"></span>
            <span class="dot" onclick="currentSlide(2)"></span>
        </div>
    </div>
</div>

<!-- Nội dung trang -->
<div class="home-container">
    <h2>Trang chủ</h2>
    <p>Nội dung trang chủ</p>
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
</script>
