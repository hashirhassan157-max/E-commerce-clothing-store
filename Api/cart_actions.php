<?php
// api/cart_actions.php
require_once __DIR__ . '/../config/db.php';

header('Content-Type: application/json');
$response = ['success' => false, 'message' => 'An error occurred.'];

if (!isset($_POST['action']) || !isset($_POST['product_id'])) {
    $response['message'] = 'Invalid request.';
    echo json_encode($response);
    exit;
}

$action = $_POST['action'];
$product_id = (int)$_POST['product_id'];
$quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

// Initialize cart if not set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

try {
    switch ($action) {
        case 'add':
            if ($quantity <= 0) $quantity = 1;
            
            // Check stock
            $stmt = $conn->prepare("SELECT stock_quantity FROM products WHERE product_id = ?");
            $stmt->bind_param("i", $product_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $product = $result->fetch_assoc();
            $stmt->close();

            if (!$product) {
                 $response['message'] = 'Product not found.';
            } else {
                $current_quantity = isset($_SESSION['cart'][$product_id]) ? $_SESSION['cart'][$product_id] : 0;
                $new_quantity = $current_quantity + $quantity;

                if ($new_quantity > $product['stock_quantity']) {
                    $response['message'] = 'Not enough stock. Only ' . $product['stock_quantity'] . ' available.';
                } else {
                    $_SESSION['cart'][$product_id] = $new_quantity;
                    $response['success'] = true;
                    $response['message'] = 'Product added to cart!';
                }
            }
            break;

        case 'update':
            if ($quantity <= 0) {
                // If quantity is 0 or less, remove it
                unset($_SESSION['cart'][$product_id]);
                $response['success'] = true;
                $response['message'] = 'Item removed from cart.';
                // Redirect back to cart page
                header('Location: ../cart.php');
                exit;
            }

            // Check stock
            $stmt = $conn->prepare("SELECT stock_quantity FROM products WHERE product_id = ?");
            $stmt->bind_param("i", $product_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $product = $result->fetch_assoc();
            $stmt->close();
            
            if ($product && $quantity <= $product['stock_quantity']) {
                $_SESSION['cart'][$product_id] = $quantity;
                $response['success'] = true;
                $response['message'] = 'Cart updated.';
                // Redirect back to cart page on form submission
                header('Location: ../cart.php');
                exit;
            } else {
                // Handle error for form submission
                $_SESSION['cart_error'] = 'Not enough stock for item.';
                header('Location: ../cart.php');
                exit;
            }
            break;

        case 'remove':
            if (isset($_SESSION['cart'][$product_id])) {
                unset($_SESSION['cart'][$product_id]);
            }
            $response['success'] = true;
            $response['message'] = 'Item removed from cart.';
            // Redirect back to cart page on form submission
            header('Location: ../cart.php');
            exit;
            break;
            
        default:
            $response['message'] = 'Invalid action.';
            break;
    }

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

// For AJAX requests
$response['cart_count'] = count($_SESSION['cart']);
echo json_encode($response);
?>