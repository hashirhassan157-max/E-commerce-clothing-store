<?php
// includes/product-card.php
$is_on_sale = ($row['old_price'] !== null && $row['old_price'] > $row['price']);

// --- NEW: Check if this product is in the wishlist array from header.php ---
$is_in_wishlist = isset($wishlist_product_ids) && in_array($row['product_id'], $wishlist_product_ids);
?>

<div class="product-card">
    
    <?php if ($is_on_sale): ?>
        <div class="sale-tag">SALE</div>
    <?php endif; ?>

    <a href="product.php?id=<?php echo $row['product_id']; ?>" class="product-image-link">
        <img src="<?php echo htmlspecialchars($row['image_url']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>" class="product-image">
    </a>
    <div class="product-info">
        <h3 class="product-name"><a href="product.php?id=<?php echo $row['product_id']; ?>"><?php echo htmlspecialchars($row['name']); ?></a></h3>
        
        <p class="product-price">
            <?php if ($is_on_sale): ?>
                <span>Rs. <?php echo number_format($row['price'], 0); ?></span>
                <span class="old-price">Rs. <?php echo number_format($row['old_price'], 0); ?></span>
            <?php else: ?>
                <span>Rs. <?php echo number_format($row['price'], 0); ?></span>
            <?php endif; ?>
        </p>

        <div class="product-actions">
            <button class="btn btn-icon btn-add-to-cart" data-product-id="<?php echo $row['product_id']; ?>" aria-label="Add to Cart">
                <i class="fa-solid fa-cart-plus"></i>
            </button>
            
            <?php if ($is_in_wishlist): ?>
                <button class="btn btn-icon btn-add-to-wishlist in-wishlist" data-product-id="<?php echo $row['product_id']; ?>" aria-label="Remove from Wishlist">
                    <i class="fa-solid fa-heart"></i>
                </button>
            <?php else: ?>
                <button class="btn btn-icon btn-add-to-wishlist" data-product-id="<?php echo $row['product_id']; ?>" aria-label="Add to Wishlist">
                    <i class="fa-regular fa-heart"></i>
                </button>
            <?php endif; ?>
            </div>
    </div>
</div>