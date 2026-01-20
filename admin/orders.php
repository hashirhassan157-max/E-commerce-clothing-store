<?php
// admin/orders.php
include 'includes/header.php';
?>

<div class="header-bar">
    <h1>Manage Orders</h1>
</div>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Customer Name</th>
                <th>Date</th>
                <th>Total Amount</th>
                <th>Payment</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Fetch orders with customer names
            $sql = "SELECT o.*, u.name as customer_name 
                    FROM orders o 
                    JOIN users u ON o.user_id = u.user_id 
                    ORDER BY o.order_date DESC";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    // Color code the status
                    $status_class = 'status-' . str_replace(' ', '', $row['order_status']);
            ?>
                <tr>
                    <td>#<?php echo $row['order_id']; ?></td>
                    <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
                    <td><?php echo date("M d, Y", strtotime($row['order_date'])); ?></td>
                    <td>Rs. <?php echo number_format($row['total_amount']); ?></td>
                    <td><?php echo strtoupper($row['payment_method']); ?></td>
                    <td><span class="status-badge <?php echo $status_class; ?>"><?php echo $row['order_status']; ?></span></td>
                    <td>
                        <a href="order_details.php?id=<?php echo $row['order_id']; ?>" class="btn-view">View Details</a>
                    </td>
                </tr>
            <?php
                }
            } else {
                echo "<tr><td colspan='7' style='text-align:center;'>No orders found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<style>
    .btn-view {
        background: #3498db; color: white; padding: 6px 12px; 
        border-radius: 4px; text-decoration: none; font-size: 0.9rem;
    }
    .btn-view:hover { background: #2980b9; }
    
    /* Status Colors */
    .status-Processing { background: #ffeeba; color: #856404; }
    .status-Shipped { background: #b8daff; color: #004085; }
    .status-Delivered { background: #c3e6cb; color: #155724; }
    .status-Cancelled { background: #f5c6cb; color: #721c24; }
</style>

<?php include 'includes/footer.php'; ?>