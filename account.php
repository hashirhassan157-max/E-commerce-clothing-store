<?php
// account.php
include 'includes/header.php';

// Security: Check if user is logged in.
// We already set $is_logged_in in the header, but it's good practice to double-check.
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: login.php");
    exit;
}

// Get user ID from session
$user_id = $_SESSION['user_id'];
?>

<div class="container account-container">
    <h1 class="page-title">Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?></h1>
    <p class="account-email">Signed in as: <?php echo htmlspecialchars($_SESSION['email']); ?></p>

    <hr>

    <h2 class="section-title">Your Order History</h2>

    <div class="order-history-list">
        <?php
        // 1. Fetch all orders for this user
        $stmt_orders = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC");
        $stmt_orders->bind_param("i", $user_id);
        $stmt_orders->execute();
        $orders_result = $stmt_orders->get_result();

        if ($orders_result->num_rows > 0) {
            while ($order = $orders_result->fetch_assoc()) {
                $order_id = $order['order_id'];
        ?>
                <div class="order-block">
                    <div class="order-header">
                        <h3>Order #<?php echo $order_id; ?></h3>
                        <p><strong>Date:</strong> <?php echo date("F j, Y", strtotime($order['order_date'])); ?></p>
                        <p><strong>Total:</strong> <span class="order-total">Rs. <?php echo number_format($order['total_amount'], 0); ?></span></p>
                        <p><strong>Status:</strong> <?php echo htmlspecialchars($order['order_status']); ?></p>
                    </div>

                    <div class="order-items-list">
                        <?php
                        // 2. Fetch all items for this specific order
                        $stmt_items = $conn->prepare(
                            "SELECT oi.*, p.name, p.image_url 
                             FROM order_items oi
                             JOIN products p ON oi.product_id = p.product_id
                             WHERE oi.order_id = ?"
                        );
                        $stmt_items->bind_param("i", $order_id);
                        $stmt_items->execute();
                        $items_result = $stmt_items->get_result();
                        
                        while ($item = $items_result->fetch_assoc()) {
                        ?>
                            <div class="order-item">
                                <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="order-item-image">
                                <div class="order-item-info">
                                    <strong><?php echo htmlspecialchars($item['name']); ?></strong>
                                    <p>Quantity: <?php echo $item['quantity']; ?></p>
                                    <p>Price (at purchase): Rs. <?php echo number_format($item['price_at_purchase'], 0); ?></p>
                                </div>
                            </div>
                        <?php
                        } // end items loop
                        $stmt_items->close();
                        ?>
                    </div>
                </div>
        <?php
            } // end orders loop
        } else {
            echo "<p>You have not placed any orders yet.</p>";
        }
        $stmt_orders->close();
        ?>
    </div>
</div>

<?php
include 'includes/footer.php';
?>