<?php
// admin/customers.php
include 'includes/header.php';
?>

<div class="header-bar">
    <h1>Registered Customers</h1>
</div>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Joined Date</th>
                <th>Total Orders</th>
                <th>Total Spent</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Advanced Query: Get User info AND count their orders AND sum their spending
            $sql = "SELECT u.user_id, u.name, u.email, u.created_at, 
                           COUNT(o.order_id) as order_count, 
                           IFNULL(SUM(o.total_amount), 0) as total_spent 
                    FROM users u 
                    LEFT JOIN orders o ON u.user_id = o.user_id 
                    GROUP BY u.user_id 
                    ORDER BY u.created_at DESC";
            
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
            ?>
                <tr>
                    <td>#<?php echo $row['user_id']; ?></td>
                    <td>
                        <strong><?php echo htmlspecialchars($row['name']); ?></strong>
                    </td>
                    <td>
                        <a href="mailto:<?php echo htmlspecialchars($row['email']); ?>" style="color:#3498db; text-decoration:none;">
                            <?php echo htmlspecialchars($row['email']); ?>
                        </a>
                    </td>
                    <td><?php echo date("M d, Y", strtotime($row['created_at'])); ?></td>
                    <td><span class="badge"><?php echo $row['order_count']; ?> orders</span></td>
                    <td style="font-weight:bold; color:#27ae60;">Rs. <?php echo number_format($row['total_spent']); ?></td>
                </tr>
            <?php
                }
            } else {
                echo "<tr><td colspan='6' style='text-align:center;'>No customers found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<style>
    .badge {
        background: #eef2f3; color: #333; padding: 5px 10px; 
        border-radius: 15px; font-size: 0.85rem; font-weight: bold;
    }
</style>

<?php include 'includes/footer.php'; ?>