<?php
// admin/order_details.php
include 'includes/header.php';

if (!isset($_GET['id'])) {
    header("Location: orders.php");
    exit;
}

$order_id = (int)$_GET['id'];
$message = "";

// --- Handle Status Update ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_status'])) {
    $new_status = $_POST['order_status'];
    $stmt = $conn->prepare("UPDATE orders SET order_status = ? WHERE order_id = ?");
    $stmt->bind_param("si", $new_status, $order_id);
    if ($stmt->execute()) {
        $message = "Order status updated to <strong>$new_status</strong>";
    }
}

// --- Fetch Order & Address Data ---
// joining orders, users, and addresses tables
$sql = "SELECT o.*, u.email, a.full_name, a.phone, a.address_line1, a.address_line2, a.city, a.state, a.postal_code, a.country 
        FROM orders o
        JOIN users u ON o.user_id = u.user_id
        JOIN addresses a ON o.shipping_address_id = a.address_id
        WHERE o.order_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) { echo "Order not found."; exit; }

// --- Fetch Order Items ---
$sql_items = "SELECT oi.*, p.name, p.image_url 
              FROM order_items oi
              JOIN products p ON oi.product_id = p.product_id
              WHERE oi.order_id = ?";
$stmt_items = $conn->prepare($sql_items);
$stmt_items->bind_param("i", $order_id);
$stmt_items->execute();
$items = $stmt_items->get_result();
?>

<div class="header-bar">
    <h1>Order #<?php echo $order['order_id']; ?> Details</h1>
    <a href="orders.php" class="btn-action" style="background:#7f8c8d; text-decoration:none; color:white; padding:8px 15px; border-radius:4px;">Back to Orders</a>
</div>

<?php if($message) echo "<p style='background:#d4edda; color:#155724; padding:15px; border-radius:4px; margin-bottom:20px;'>$message</p>"; ?>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px;">

    <div class="table-container">
        <h3>Items Ordered</h3>
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Qty</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php while($item = $items->fetch_assoc()): ?>
                <tr>
                    <td style="display:flex; align-items:center; gap:10px;">
                        <img src="../<?php echo $item['image_url']; ?>" style="width:50px; height:50px; object-fit:cover; border-radius:4px;">
                        <?php echo htmlspecialchars($item['name']); ?>
                    </td>
                    <td>Rs. <?php echo number_format($item['price_at_purchase']); ?></td>
                    <td>x <?php echo $item['quantity']; ?></td>
                    <td>Rs. <?php echo number_format($item['price_at_purchase'] * $item['quantity']); ?></td>
                </tr>
                <?php endwhile; ?>
                <tr style="font-weight:bold; background:#f8f9fa;">
                    <td colspan="3" style="text-align:right;">Grand Total:</td>
                    <td>Rs. <?php echo number_format($order['total_amount']); ?></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div style="display:flex; flex-direction:column; gap:20px;">
        
        <div class="card" style="display:block;">
            <h3>Update Status</h3>
            <form action="" method="POST" style="margin-top:10px;">
                <select name="order_status" style="width:100%; padding:10px; margin-bottom:10px;">
                    <option value="Processing" <?php if($order['order_status']=='Processing') echo 'selected'; ?>>Processing</option>
                    <option value="Shipped" <?php if($order['order_status']=='Shipped') echo 'selected'; ?>>Shipped</option>
                    <option value="Delivered" <?php if($order['order_status']=='Delivered') echo 'selected'; ?>>Delivered</option>
                    <option value="Cancelled" <?php if($order['order_status']=='Cancelled') echo 'selected'; ?>>Cancelled</option>
                </select>
                <button type="submit" name="update_status" style="width:100%; padding:10px; background:#2ecc71; color:white; border:none; border-radius:4px; cursor:pointer;">Update Status</button>
            </form>
        </div>

        <div class="card" style="display:block;">
            <h3>Shipping Address</h3>
            <p><strong><?php echo htmlspecialchars($order['full_name']); ?></strong></p>
            <p><?php echo htmlspecialchars($order['address_line1']); ?></p>
            <?php if($order['address_line2']) echo "<p>".htmlspecialchars($order['address_line2'])."</p>"; ?>
            <p><?php echo htmlspecialchars($order['city'] . ", " . $order['state'] . " " . $order['postal_code']); ?></p>
            <p><?php echo htmlspecialchars($order['country']); ?></p>
            <hr>
            <p><i class="fas fa-phone"></i> <?php echo htmlspecialchars($order['phone']); ?></p>
            <p><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($order['email']); ?></p>
        </div>

    </div>
</div>

<?php include 'includes/footer.php'; ?>