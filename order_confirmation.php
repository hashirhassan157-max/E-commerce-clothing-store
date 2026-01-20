<?php
// order_confirmation.php
include 'includes/header.php';

// Must be logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: login.php");
    exit;
}

if (!isset($_GET['order_id'])) {
    header("location: index.php");
    exit;
}

$order_id = (int)$_GET['order_id'];
$user_id = $_SESSION['user_id'];

// Verify this order belongs to this user
$stmt = $conn->prepare("SELECT * FROM orders WHERE order_id = ? AND user_id = ?");
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    // Order not found or doesn't belong to user
    header("location: index.php");
    exit;
}

$order = $result->fetch_assoc();
$stmt->close();

?>

<div class="container" style="text-align: center; max-width: 700px;">
    <div class="alert alert-success" style="font-size: 1.5rem; margin-bottom: 2rem;">
        <i class="fa-solid fa-check-circle"></i> Thank You For Your Order!
    </div>
    
    <h1 class="page-title">Order Confirmation</h1>
    
    <p style="font-size: 1.2rem; margin-bottom: 1.5rem;">
        Your order <strong>#<?php echo $order['order_id']; ?></strong> has been placed successfully.
    </p>
    <p>An email confirmation has been (simulated) sent to <strong><?php echo htmlspecialchars($_SESSION['email']); ?></strong>.</p>
    
    <div class="order-summary" style="text-align: left; margin-top: 2rem; position: static;">
        <h2>Order Summary</h2>
        <p><strong>Order Date:</strong> <?php echo date("F j, Y, g:i a", strtotime($order['order_date'])); ?></p>
        <p><strong>Total Amount:</strong> <span style="font-weight: 700; color: var(--primary-color);">Rs.<?php echo number_format($order['total_amount'], 0); ?></span></p>
        <p><strong>Payment Method:</strong> <?php echo htmlspecialchars(strtoupper($order['payment_method'])); ?></p>
        <p><strong>Order Status:</strong> <?php echo htmlspecialchars($order['order_status']); ?></p>
    </div>
    
    <a href="index.php" class="btn btn-primary" style="margin-top: 2rem;">Continue Shopping</a>
</div>

<?php include 'includes/footer.php'; ?>