<?php
// wishlist.php
include 'includes/header.php';

// Must be logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$wishlist_items = [];

$sql = "SELECT p.product_id, p.name, p.price, p.image_url 
        FROM products p
        JOIN wishlist w ON p.product_id = w.product_id
        WHERE w.user_id = ?";
        
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $wishlist_items[] = $row;
    }
}
$stmt->close();
?>

<div class="container wishlist-container">
    <h1 class="page-title">Your Wishlist</h1>
    
    <?php if (empty($wishlist_items)): ?>
        <div class="empty-page-message">
            <p>Your wishlist is empty.</p>
            <a href="index.php" class="btn btn-primary">Discover Products</a>
        </div>
    <?php else: ?>
        <div class="wishlist-items-list">
            <?php foreach ($wishlist_items as $item): ?>
            <div class="wishlist-item">
                <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="wishlist-item-image">
                <div class="wishlist-item-info">
                    <h3><a href="product.php?id=<?php echo $item['product_id']; ?>"><?php echo htmlspecialchars($item['name']); ?></a></h3>
                    <p>Rs. <?php echo number_format($item['price'], 0); ?></p>
                </div>
                <div class="wishlist-item-actions">
                    <button class="btn btn-primary btn-add-to-cart" data-product-id="<?php echo $item['product_id']; ?>">Add to Cart</button>
                    <button class="btn btn-icon btn-remove-from-wishlist" data-product-id="<?php echo $item['product_id']; ?>" aria-label="Remove from Wishlist">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>