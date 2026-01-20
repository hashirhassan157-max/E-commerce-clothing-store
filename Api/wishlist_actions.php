<?php
// api/wishlist_actions.php
require_once __DIR__ . '/../config/db.php';

header('Content-Type: application/json');
$response = ['success' => false, 'message' => 'An error occurred.'];

// Check for login
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    $response['message'] = 'You must be logged in to manage your wishlist.';
    echo json_encode($response);
    exit;
}

if (!isset($_POST['action']) || !isset($_POST['product_id'])) {
    $response['message'] = 'Invalid request.';
    echo json_encode($response);
    exit;
}

$user_id = $_SESSION['user_id'];
$action = $_POST['action'];
$product_id = (int)$_POST['product_id'];
$wishlist_count = 0;

try {
    switch ($action) {
        case 'add':
            // Check if already in wishlist
            $stmt = $conn->prepare("SELECT wishlist_id FROM wishlist WHERE user_id = ? AND product_id = ?");
            $stmt->bind_param("ii", $user_id, $product_id);
            $stmt->execute();
            $stmt->store_result();
            
            if ($stmt->num_rows == 0) {
                // Not in wishlist, add it
                $stmt_insert = $conn->prepare("INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)");
                $stmt_insert->bind_param("ii", $user_id, $product_id);
                $stmt_insert->execute();
                $stmt_insert->close();
                $response['message'] = 'Added to wishlist!';
            } else {
                $response['message'] = 'Item is already in your wishlist.';
            }
            $stmt->close();
            $response['success'] = true;
            break;

        case 'remove':
            $stmt = $conn->prepare("DELETE FROM wishlist WHERE user_id = ? AND product_id = ?");
            $stmt->bind_param("ii", $user_id, $product_id);
            $stmt->execute();
            
            if ($stmt->affected_rows > 0) {
                $response['success'] = true;
                $response['message'] = 'Removed from wishlist!';
            } else {
                $response['message'] = 'Item not found in wishlist.';
            }
            $stmt->close();
            break;
            
        default:
            $response['message'] = 'Invalid action.';
            break;
    }

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

// Get updated wishlist count
$stmt_count = $conn->prepare("SELECT COUNT(*) FROM wishlist WHERE user_id = ?");
$stmt_count->bind_param("i", $user_id);
$stmt_count->execute();
$stmt_count->bind_result($wishlist_count);
$stmt_count->fetch();
$stmt_count->close();

$response['wishlist_count'] = $wishlist_count;
echo json_encode($response);
?>