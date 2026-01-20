<?php
// index.php
include 'includes/header.php';
?>

<!-- <section class="hero-section" style="background-image: url('images/mens promo banner.jpg');">
    <div class="hero-content">
        <span class="hero-subtitle">Urban Edge</span>
        <h1 class="hero-title">Jackets for the Modern Man</h1>
        <a href="category.php?id=1" class="btn btn-primary">Discover Now</a>
    </div>
</section> -->


  <section class="hero-slider">
    <div class="slider-container">
        
        <div class="slide active"> <div class="slide-image-container">
                <img src="images/mens promo banner.jpg" alt="Men's Fashion" class="slide-banner-img">
                <div class="slide-overlay"></div>
            </div>
            <div class="slide-content-container">
                <!-- <div class="hero-content">
                    <span class="hero-subtitle">The Last Applause</span>
                    <h1 class="hero-title">Winter Collection 2025</h1>
                    <a href="category.php?id=1" class="btn btn-primary">Shop Now</a>
                </div> -->
            </div>
        </div>

        <div class="slide">
            <div class="slide-image-container">
                <img src="images/women promo banner.jpg" alt="Women's Fashion" class="slide-banner-img">
                <div class="slide-overlay"></div>
            </div>
            <div class="slide-content-container">
                <!-- <div class="hero-content">
                    <span class="hero-subtitle">New Arrivals</span>
                    <h1 class="hero-title">Elegant Styles for Her</h1>
                    <a href="category.php?id=2" class="btn btn-primary">Discover More</a>
                </div> -->
            </div>
        </div>

        <div class="slide">
            <div class="slide-image-container">
                <img src="images/bg hero banner.jpg" alt="Accessories" class="slide-banner-img">
                <div class="slide-overlay"></div>
            </div>
            <div class="slide-content-container">
                <!-- <div class="hero-content">
                    <span class="hero-subtitle">Premium Accessories</span>
                    <h1 class="hero-title">Complete Your Look</h1>
                    <a href="category.php?id=3" class="btn btn-primary">Shop Accessories</a>
                </div> -->
            </div>
        </div>


        <div class="slide">
            <div class="slide-image-container">
                <img src="images/bg women hero banner.jpg" alt="Accessories" class="slide-banner-img">
                <div class="slide-overlay"></div>
            </div>
            <div class="slide-content-container">
                <!-- <div class="hero-content">
                    <span class="hero-subtitle">Premium Accessories</span>
                    <h1 class="hero-title">Complete Your Look</h1>
                    <a href="category.php?id=3" class="btn btn-primary">Shop Accessories</a>
                </div> -->
            </div>
        </div>

    </div>

    <button class="slider-btn prev-btn" aria-label="Previous Slide"><i class="fa-solid fa-chevron-left"></i></button>
    <button class="slider-btn next-btn" aria-label="Next Slide"><i class="fa-solid fa-chevron-right"></i></button>

    <div class="slider-dots">
        <div class="dot active" data-slide="0"></div>
        <div class="dot" data-slide="1"></div>
        <div class="dot" data-slide="2"></div>
        <div class="dot" data-slide="3"></div>
    </div>
</section>


<section class="product-showcase-container">
    <h2 class="section-title">New Arrivals</h2>
    <div class="product-grid">
        <?php
        // --- FIX 1: ADDED 'old_price' TO SQL and 'WHERE old_price IS NULL' ---
        $sql = "SELECT product_id, name, price, old_price, image_url FROM products WHERE old_price IS NULL ORDER BY created_at DESC LIMIT 8";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                
                // --- FIX 2: DELETED ALL THE OLD DUPLICATE 'echo' CODE ---
                // Now we ONLY include the reusable card, which fixes the layout.
                include 'includes/product-card.php';
            }
        } else {
            echo "<p>No new arrivals found.</p>";
        }
        ?>
    </div>
</section>

<section class="promo-blocks-container">
    <div class="promo-block" style="background-image: url('images/bg hero banner.jpg'); ">
        <div class="promo-content">
            <!-- <span>Radiant Reverb</span>
            <h3>Where Dreams Meet Couture</h3> -->
            <a href="category.php?id=1" class="btn btn-secondary">Shop Men</a>
        </div>
    </div>

    <div class="promo-block" style="background-image: url('images/bg women hero banner.jpg');">
         <div class="promo-content">
            <!-- <span>Enchanting Styles</span>
            <h3>For Every Woman</h3> -->
            <a href="category.php?id=2" class="btn btn-secondary">Shop Women</a>
        </div>
    </div>
</section>

<section class="product-showcase-container">
    <h2 class="section-title">On Sale</h2>
    <div class="product-grid">
        <?php
        // --- FIX 3: CHANGED 'IS NULL' TO 'IS NOT NULL' TO PROPERLY FIND SALE ITEMS ---
        $sql = "SELECT product_id, name, price, old_price, image_url FROM products WHERE old_price IS NOT NULL ORDER BY created_at DESC LIMIT 8";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                // This was already correct
                include 'includes/product-card.php';
            }
        } else {
            echo "<p>No items currently on sale.</p>";
        }
        ?>
    </div>
</section>

<?php
include 'includes/footer.php';
?>