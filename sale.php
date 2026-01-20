<?php
// sale.php
include 'includes/header.php';
?>

<div class="container page-container">
    <h1 class="page-title">Items On Sale</h1>
    
    <div class="product-grid large-grid">
        <?php
        // Fetch ALL products that are on sale
        $sql = "SELECT product_id, name, price, old_price, image_url FROM products WHERE old_price IS NOT NULL ORDER BY category_id";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                // We use the same reusable card!
                include 'includes/product-card.php';
            }
        } else {
            echo "<p>No items are currently on sale.</p>";
        }
        ?>
    </div>
</div>

<?php
include 'includes/footer.php';
?>