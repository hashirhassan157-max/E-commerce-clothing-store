<?php
// includes/header.php
require_once __DIR__ . '/../config/db.php';

// Get counts
$cart_count = isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;
$is_logged_in = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;

// Wishlist count logic
$wishlist_count = 0;
$wishlist_product_ids = []; // To store IDs for the heart icon check
if ($is_logged_in) {
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT product_id FROM wishlist WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $wishlist_product_ids[] = $row['product_id'];
    }
    $stmt->close();
    $wishlist_count = count($wishlist_product_ids);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PJ Collection</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <header class="main-header">
        <nav class="navbar">
            
            <div class="nav-left">
                <a href="index.php" class="logo">
                    <img src="images/PJ LOGO COLLECTION .png" alt="PJ Collection Logo">
                </a>
            </div>

            <ul class="nav-links desktop-only">
                <li><a href="index.php">Home</a></li>
                <li><a href="category.php?id=1">Men's</a></li>
                <li><a href="category.php?id=2">Women's</a></li>
                <li><a href="category.php?id=4">Shoes</a></li>
                <li><a href="category.php?id=3">Accessories</a></li>
                <li><a href="sale.php" style="color: #dc3545; font-weight: 700;">Sale</a></li>
            </ul>

            <div class="nav-right">
                
                <form action="search.php" method="GET" class="search-form desktop-only">
                    <input type="text" name="q" placeholder="Search products..." required>
                    <button type="submit" aria-label="Search"><i class="fa-solid fa-search"></i></button>
                </form>

                <button class="nav-icon mobile-search-btn" aria-label="Open Search">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>

                <a href="wishlist.php" class="nav-icon" aria-label="Wishlist">
                    <i class="fa-solid fa-heart"></i>
                    <span class="icon-badge" id="wishlist-count"><?php echo $wishlist_count; ?></span>
                </a>
                <a href="cart.php" class="nav-icon" aria-label="Cart">
                    <i class="fa-solid fa-cart-shopping"></i>
                    <span class="icon-badge" id="cart-count"><?php echo $cart_count; ?></span>
                </a>
                
                <div class="desktop-auth desktop-only">
                    <?php if ($is_logged_in): ?>
                        <a href="account.php" class="nav-auth">My Account</a>
                        <a href="logout.php" class="nav-auth">Logout</a>
                    <?php else: ?>
                        <a href="login.php" class="nav-auth">Sign In</a>
                    <?php endif; ?>
                </div>
                
                <button class="nav-toggle" id="nav-toggle-btn" aria-label="toggle navigation">
                    <i class="fa-solid fa-bars"></i>
                </button>
            </div>
        </nav>

        <div class="mobile-menu" id="mobile-menu">
            <ul class="mobile-nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="category.php?id=1">Men's</a></li>
                <li><a href="category.php?id=2">Women's</a></li>
                <li><a href="category.php?id=4">Shoes</a></li>
                <li><a href="category.php?id=3">Accessories</a></li>
                <li><a href="sale.php" style="color: #dc3545;">Sale</a></li>
                <hr>
                <?php if ($is_logged_in): ?>
                    <li><a href="account.php">My Account</a></li>
                    <li><a href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php">Sign In</a></li>
                    <li><a href="register.php">Sign Up</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </header>

    <div id="search-overlay" class="search-overlay">
        <button class="close-search" id="close-search">&times;</button>
        <div class="search-overlay-content">
            <h2>What are you looking for?</h2>
            <form action="search.php" method="GET" class="overlay-search-form">
                <input type="text" name="q" placeholder="Search for products..." required autofocus>
                <button type="submit">Search</button>
            </form>
        </div>
    </div>

    <main>