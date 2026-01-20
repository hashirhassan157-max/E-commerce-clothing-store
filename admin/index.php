<?php include 'includes/header.php'; ?>

<?php
// 1. Total Orders
$res = $conn->query("SELECT COUNT(*) as count FROM orders");
$total_orders = $res->fetch_assoc()['count'];

// 2. Total Products
$res = $conn->query("SELECT COUNT(*) as count FROM products");
$total_products = $res->fetch_assoc()['count'];

// 3. Total Sales (Money)
$res = $conn->query("SELECT SUM(total_amount) as total FROM orders");
$row = $res->fetch_assoc();
$total_sales = $row['total'] ? $row['total'] : 0;
?>

<div class="header-bar">
    <h1>Dashboard Overview</h1>
    <span>Welcome, Admin</span>
</div>

<div class="dashboard-cards">
    <div class="card">
        <div class="card-info">
            <h3><?php echo $total_orders; ?></h3>
            <p>Total Orders</p>
        </div>
        <div class="card-icon"><i class="fas fa-shopping-bag"></i></div>
    </div>
    
    <div class="card">
        <div class="card-info">
            <h3>Rs. <?php echo number_format($total_sales); ?></h3>
            <p>Total Earnings</p>
        </div>
        <div class="card-icon"><i class="fas fa-wallet"></i></div>
    </div>

    <div class="card">
        <div class="card-info">
            <h3><?php echo $total_products; ?></h3>
            <p>Total Products</p>
        </div>
        <div class="card-icon"><i class="fas fa-tshirt"></i></div>
    </div>
</div>

<div class="table-container">
    <h2>Recent Orders</h2>
    <table>
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Date</th>
                <th>Total</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Fetch last 5 orders
            $sql = "SELECT order_id, order_date, total_amount, order_status FROM orders ORDER BY order_date DESC LIMIT 5";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>#" . $row['order_id'] . "</td>";
                    echo "<td>" . date("M d, Y", strtotime($row['order_date'])) . "</td>";
                    echo "<td>Rs. " . number_format($row['total_amount']) . "</td>";
                    echo "<td><span class='status-badge status-" . $row['order_status'] . "'>" . $row['order_status'] . "</span></td>";
                    echo "<td><button style='padding:5px 10px; cursor:pointer;'>View</button></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5'>No orders found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<?php include 'includes/footer.php'; ?>