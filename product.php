<?php
// product.php
include 'includes/header.php';

// Check if product ID is set
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<div class='container'><p>Product not found.</p></div>";
    include 'includes/footer.php';
    exit;
}

$product_id = (int)$_GET['id'];

// Fetch product details
$stmt = $conn->prepare("SELECT * FROM products WHERE product_id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
    $product = $result->fetch_assoc();
} else {
    echo "<div class='container'><p>Product not found.</p></div>";
    include 'includes/footer.php';
    exit;
}
$stmt->close();

// --- NEW CODE ---
// Fetch all images for this product from the new table
$images = [];
$stmt_images = $conn->prepare("SELECT image_url FROM product_images WHERE product_id = ? ORDER BY image_id");
$stmt_images->bind_param("i", $product_id);
$stmt_images->execute();
$images_result = $stmt_images->get_result();

while ($row = $images_result->fetch_assoc()) {
    $images[] = $row['image_url'];
}
$stmt_images->close();

// If no images are in the new table, use the main product image as a fallback
if (empty($images)) {
    $images[] = $product['image_url'];
}
// --- END OF NEW CODE ---
$is_in_wishlist_pdp = isset($wishlist_product_ids) && in_array($product_id, $wishlist_product_ids);
?>

<div class="container product-detail-container">
    <div class="product-detail-image">
        <img src="<?php echo htmlspecialchars($images[0]); // Show the first image by default ?>" 
             alt="<?php echo htmlspecialchars($product['name']); ?>" 
             class="main-product-image" 
             id="main-product-image">
        
        <div class="thumbnail-gallery">
             <?php
             // Loop through all images and create a thumbnail for each
             foreach ($images as $index => $img_url):
             ?>
                <img src="<?php echo htmlspecialchars($img_url); ?>" 
                     alt="Thumbnail <?php echo $index + 1; ?>" 
                     class="thumbnail-image <?php echo $index == 0 ? 'active' : ''; // Make first one active ?>">
             <?php endforeach; ?>
        </div>
    </div>
    
    <div class="product-detail-info">
        <h1 class="product-detail-title"><?php echo htmlspecialchars($product['name']); ?></h1>
        
        <?php
        // Check if product is on sale
        $is_on_sale_pdp = ($product['old_price'] !== null && $product['old_price'] > $product['price']);
        ?>
        
        <div class="product-detail-price">
            <?php if ($is_on_sale_pdp): ?>
                <span>Rs. <?php echo number_format($product['price'], 0); ?></span>
                <span class="old-price">Rs. <?php echo number_format($product['old_price'], 0); ?></span>
            <?php else: ?>
                <span>Rs. <?php echo number_format($product['price'], 0); ?></span>
            <?php endif; ?>
        </div>

        
        <div class="product-detail-description">
            <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
        </div>
        
        <form class="product-detail-form" id="add-to-cart-form">
            <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
            
            <div class="form-group">
                <label for="size">Size:</label>
                <select name="size" id="size" class="form-control">
                    <option value="S">S</option>
                    <option value="M">M</option>
                    <option value="L">L</option>
                    <option value="XL">XL</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="quantity">Quantity:</label>
                <input type="number" name="quantity" id="quantity" class="form-control" value="1" min="1" max="<?php echo $product['stock_quantity']; ?>">
            </div>
            
            <p>Stock: <?php echo $product['stock_quantity']; ?> available</p>

            <?php if ($product['stock_quantity'] > 0 && $product['stock_quantity'] < 10): ?>
                <p class="low-stock-warning">
                    <i class="fa-solid fa-fire"></i> Hurry! Only <?php echo $product['stock_quantity']; ?> left!
                </p>
            <?php endif; ?>
            <button type="submit" class="btn btn-primary btn-block btn-add-to-cart-pdp">Add to Cart</button>
        </form>
        
        <?php if ($is_in_wishlist_pdp): ?>
          <button class="btn btn-secondary btn-block btn-add-to-wishlist-pdp in-wishlist"    data-product-id="<?php echo $product_id; ?>">
          <i class="fa-solid fa-heart"></i> Added to Wishlist
       </button>
      <?php else: ?>
       <button class="btn btn-secondary btn-block btn-add-to-wishlist-pdp" data-product-id="<?php echo $product_id; ?>">
        <i class="fa-regular fa-heart"></i> Add to Wishlist
    </button>
<?php endif; ?>
    </div>
</div>

<?php
include 'includes/footer.php';
?>