<?php
// category.php
include 'includes/header.php';

// Get category ID from URL, default to 1 (Men's) if not set
$category_id = isset($_GET['id']) ? (int)$_GET['id'] : 1;

// Fetch category name
$cat_stmt = $conn->prepare("SELECT name FROM categories WHERE category_id = ?");
$cat_stmt->bind_param("i", $category_id);
$cat_stmt->execute();
$cat_result = $cat_stmt->get_result();
$category = $cat_result->fetch_assoc();
$category_name = $category ? htmlspecialchars($category['name']) : 'Category';
$cat_stmt->close();

?>

<div class="container page-container">
    <h1 class="page-title"><?php echo $category_name; ?></h1>
    
    <div class="product-grid large-grid">
        <?php
        // Fetch products for this category
        $stmt = $conn->prepare("SELECT product_id, name, price, old_price, image_url FROM products WHERE category_id = ?");
        $stmt->bind_param("i", $category_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                // We ONLY include the reusable card.
                // All the old, duplicate HTML is now deleted.
                include 'includes/product-card.php';
            }
        } else {
            echo "<p>No products found in this category.</p>";
        }
        $stmt->close();
        ?>
    </div>
</div>

<?php
include 'includes/footer.php';
?>