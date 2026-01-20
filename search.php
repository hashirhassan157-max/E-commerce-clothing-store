<?php
// search.php
include 'includes/header.php';

// Check if a search query is set
$search_query = "";
if (isset($_GET['q']) && !empty($_GET['q'])) {
    $search_query = trim($_GET['q']);
} else {
    // If no query, just show an empty page or a message
    echo "<div class='container'><p>Please enter a search term.</p></div>";
    include 'includes/footer.php';
    exit;
}

// Security: Prepare the search term for a LIKE query
$search_term = "%" . $search_query . "%";

?>

<div class="container page-container">
    <h1 class="page-title">Search Results for "<?php echo htmlspecialchars($search_query); ?>"</h1>
    
    <div class="product-grid large-grid">
        <?php
        // Fetch products that match the name OR description
        $stmt = $conn->prepare(
            "SELECT product_id, name, price, old_price, image_url 
             FROM products 
             WHERE name LIKE ? OR description LIKE ?"
        );
        $stmt->bind_param("ss", $search_term, $search_term);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                // We use our reusable product card!
                include 'includes/product-card.php';
            }
        } else {
            echo "<p>No products found matching your search.</p>";
        }
        $stmt->close();
        ?>
    </div>
</div>

<?php
include 'includes/footer.php';
?>