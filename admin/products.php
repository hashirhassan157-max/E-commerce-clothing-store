<?php
// admin/products.php
include 'includes/header.php';

// Delete Logic (We will use this later, but setting it up now)
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM products WHERE product_id = $id");
    echo "<script>window.location.href='products.php';</script>";
}
?>

<div class="header-bar">
    <h1>Manage Products</h1>
    <a href="product_add.php" class="btn-action btn-add">+ Add New Product</a>
</div>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th style="width: 60px;">Image</th>
                <th>Product Name</th>
                <th>Category</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Fetch all products
            $sql = "SELECT p.*, c.name as category_name 
                    FROM products p 
                    LEFT JOIN categories c ON p.category_id = c.category_id 
                    ORDER BY p.created_at DESC";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    // Check stock status
                    $stock_status = '';
                    if ($row['stock_quantity'] == 0) {
                        $stock_status = '<span style="color:red; font-weight:bold;">Out of Stock</span>';
                    } elseif ($row['stock_quantity'] < 5) {
                        $stock_status = '<span style="color:orange; font-weight:bold;">Low (' . $row['stock_quantity'] . ')</span>';
                    } else {
                        $stock_status = '<span style="color:green;">' . $row['stock_quantity'] . ' in stock</span>';
                    }
            ?>
                <tr>
                    <td>
                        <img src="../<?php echo htmlspecialchars($row['image_url']); ?>" 
                             alt="Img" 
                             style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px; border: 1px solid #ddd;">
                    </td>
                    <td>
                        <strong><?php echo htmlspecialchars($row['name']); ?></strong>
                        <?php if($row['old_price']): ?>
                            <br><small style="color:red;">On Sale</small>
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($row['category_name'] ?? 'Uncategorized'); ?></td>
                    <td>Rs. <?php echo number_format($row['price']); ?></td>
                    <td><?php echo $stock_status; ?></td>
                    <td>
                        <a href="product_edit.php?id=<?php echo $row['product_id']; ?>" class="btn-sm btn-edit"><i class="fas fa-edit"></i></a>
                        <a href="products.php?delete=<?php echo $row['product_id']; ?>" class="btn-sm btn-delete" onclick="return confirm('Are you sure?');"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
            <?php
                }
            } else {
                echo "<tr><td colspan='6' style='text-align:center;'>No products found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<style>
    /* Specific styles for this page */
    .btn-action { text-decoration: none; padding: 10px 20px; border-radius: 4px; color: white; font-weight: bold; }
    .btn-add { background-color: #2ecc71; }
    .btn-add:hover { background-color: #27ae60; }
    
    .btn-sm { padding: 5px 10px; border-radius: 4px; color: white; text-decoration: none; margin-right: 5px; font-size: 0.9rem; }
    .btn-edit { background-color: #3498db; }
    .btn-delete { background-color: #e74c3c; }
</style>

<?php include 'includes/footer.php'; ?>