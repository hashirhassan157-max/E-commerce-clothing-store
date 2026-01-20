<?php
// cart.php
include 'includes/header.php';

$cart_items = [];
$subtotal = 0;

if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    // Get product IDs from cart
    $product_ids = implode(',', array_keys($_SESSION['cart']));
    
    // Fetch product details from DB
    $sql = "SELECT product_id, name, price, image_url, stock_quantity FROM products WHERE product_id IN ($product_ids)";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $product_id = $row['product_id'];
            $quantity = $_SESSION['cart'][$product_id];
            $row['quantity'] = $quantity;
            $row['item_total'] = $row['price'] * $quantity;
            $subtotal += $row['item_total'];
            $cart_items[] = $row;
        }
    }
}
?>

<div class="container cart-container">
    <h1 class="page-title">Your Shopping Cart</h1>
    
    <?php if (empty($cart_items)): ?>
        <div class="empty-page-message">
            <p>Your cart is currently empty.</p>
            <a href="index.php" class="btn btn-primary">Start Shopping</a>
        </div>
    <?php else: ?>
        <div class="cart-items-list">
            <?php foreach ($cart_items as $item): ?>
            <div class="cart-item">
                <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="cart-item-image">
                <div class="cart-item-info">
                    <h3><a href="product.php?id=<?php echo $item['product_id']; ?>"><?php echo htmlspecialchars($item['name']); ?></a></h3>
                    <p>Rs. <?php echo number_format($item['price'], 0); ?></p>
                    <form action="api/cart_actions.php" method="POST" class="cart-update-form">
                        <label for="quantity-<?php echo $item['product_id']; ?>">Quantity:</label>
                        <input type="number" name="quantity" id="quantity-<?php echo $item['product_id']; ?>" 
                               class="form-control quantity-input" 
                               value="<?php echo $item['quantity']; ?>" 
                               min="1" max="<?php echo $item['stock_quantity']; ?>">
                        <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>">
                        <input type="hidden" name="action" value="update">
                        <button type="submit" class="btn btn-secondary btn-sm">Update</button>
                    </form>
                </div>
                <div class="cart-item-actions">
                    <p class="cart-item-total-price">Rs. <?php echo number_format($item['item_total'], 0); ?></p>
                    <form action="api/cart_actions.php" method="POST">
                        <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>">
                        <input type="hidden" name="action" value="remove">
                        <button type="submit" class="btn btn-icon" aria-label="Remove item"><i class="fa-solid fa-trash"></i></button>
                    </form>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="cart-summary">
            <h2>Order Summary</h2>
            <p>Subtotal: <span class="subtotal-price">Rs. <?php echo number_format($subtotal, 0); ?></span></p>
            <p>Shipping & taxes calculated at checkout.</p>
            <a href="checkout.php" class="btn btn-primary btn-block">Proceed to Checkout</a>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>