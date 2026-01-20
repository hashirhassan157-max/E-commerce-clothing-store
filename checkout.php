<?php
// checkout.php
include 'includes/header.php';

// Must be logged in to check out
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    $_SESSION['checkout_redirect'] = true; // Flag to redirect after login
    header("location: login.php");
    exit;
}

// If cart is empty, redirect
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("location: cart.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$cart_items = [];
$subtotal = 0;

// Fetch cart items and calculate subtotal (same as cart.php)
$product_ids = implode(',', array_keys($_SESSION['cart']));
$sql = "SELECT product_id, name, price FROM products WHERE product_id IN ($product_ids)";
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
} else {
    // Should not happen if cart session is set, but good to check
    header("location: cart.php");
    exit;
}

// Fetch user's default address
$address = null;
$stmt = $conn->prepare("SELECT * FROM addresses WHERE user_id = ? AND is_default = 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$address_result = $stmt->get_result();
if ($address_result->num_rows > 0) {
    $address = $address_result->fetch_assoc();
}
$stmt->close();
?>

<div class="container checkout-container">
    <div class="checkout-form">
        <h1 class="page-title">Checkout</h1>
        <form action="order_process.php" method="POST" id="checkout-form">
        
            <h2>1. Shipping Address</h2>
            <div class="form-row">
                <div class="form-group">
                    <label for="full_name">Full Name</label>
                    <input type="text" name="full_name" id="full_name" class="form-control" value="<?php echo htmlspecialchars($address['full_name'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" name="phone" id="phone" class="form-control" value="<?php echo htmlspecialchars($address['phone'] ?? ''); ?>" required>
                </div>
            </div>
            <div class="form-group">
                <label for="address_line1">Address Line 1</label>
                <input type="text" name="address_line1" id="address_line1" class="form-control" value="<?php echo htmlspecialchars($address['address_line1'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="address_line2">Address Line 2 (Optional)</label>
                <input type="text" name="address_line2" id="address_line2" class="form-control" value="<?php echo htmlspecialchars($address['address_line2'] ?? ''); ?>">
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="city">City</label>
                    <input type="text" name="city" id="city" class="form-control" value="<?php echo htmlspecialchars($address['city'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label for="state">State/Province</label>
                    <input type="text" name="state" id="state" class="form-control" value="<?php echo htmlspecialchars($address['state'] ?? ''); ?>" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="postal_code">Postal Code</label>
                    <input type="text" name="postal_code" id="postal_code" class="form-control" value="<?php echo htmlspecialchars($address['postal_code'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label for="country">Country</label>
                    <input type="text" name="country" id="country" class="form-control" value="<?php echo htmlspecialchars($address['country'] ?? 'USA'); ?>" required>
                </div>
            </div>
            <div class="form-group">
                 <input type="checkbox" name="save_address" id="save_address" value="1" <?php echo $address ? 'checked' : ''; ?>>
                 <label for="save_address" style="display: inline-block;">Save this address as default</label>
            </div>
            
            <h2 style="margin-top: 2rem;">2. Payment Method</h2>
            <div class="payment-methods">
                <div class="form-group">
                    <input type="radio" name="payment_method" id="payment_cod" value="cod" checked required>
                    <label for="payment_cod">Cash on Delivery (COD)</label>
                </div>
                <div class="form-group">
                    <input type="radio" name="payment_method" id="payment_card" value="card" required>
                    <label for="payment_card">Credit/Debit Card</label>
                    <div id="payment-card-details" class="payment-card-details">
                        <div class="form-group">
                            <label for="card_number">Card Number (simulation)</label>
                            <input type="text" name="card_number" id="card_number" class="form-control" placeholder="4242 4242 4242 4242">
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="card_expiry">Expiry (MM/YY)</label>
                                <input type="text" name="card_expiry" id="card_expiry" class="form-control" placeholder="12/26">
                            </div>
                             <div class="form-group">
                                <label for="card_cvc">CVC</label>
                                <input type="text" name="card_cvc" id="card_cvc" class="form-control" placeholder="123">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary btn-block" style="margin-top: 2rem;">Place Order</button>
        
        </form>
    </div>
    
    <div class="order-summary">
        <h2>Order Summary</h2>
        <?php foreach ($cart_items as $item): ?>
        <div class="summary-item">
            <span><?php echo htmlspecialchars($item['name']); ?> (x<?php echo $item['quantity']; ?>)</span>
            <span>Rs. <?php echo number_format($item['item_total'], 0); ?></span>
        </div>
        <?php endforeach; ?>
        
        <div class="summary-item">
            <span>Shipping</span>
            <span>FREE</span>
        </div>
        
        <div class="summary-total">
            <span>Total</span>
            <span>Rs. <?php echo number_format($subtotal, 0); ?></span>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>