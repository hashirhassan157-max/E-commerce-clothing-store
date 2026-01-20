<?php
// order_process.php
require_once 'config/db.php';

// Must be logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: login.php");
    exit;
}

// Must have items in cart
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("location: cart.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $user_id = $_SESSION['user_id'];
    
    // --- 1. Process and Save Address ---
    $full_name = $_POST['full_name'];
    $phone = $_POST['phone'];
    $address_line1 = $_POST['address_line1'];
    $address_line2 = $_POST['address_line2'] ?? null;
    $city = $_POST['city'];
    $state = $_POST['state'];
    $postal_code = $_POST['postal_code'];
    $country = $_POST['country'];
    $save_address = isset($_POST['save_address']) && $_POST['save_address'] == '1';
    
    $address_id = null;

    // Use transaction for safety
    $conn->begin_transaction();
    
    try {
        if ($save_address) {
            // User wants to save this as default.
            // First, set any existing default addresses to non-default
            $stmt = $conn->prepare("UPDATE addresses SET is_default = 0 WHERE user_id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $stmt->close();
            
            // Check if this exact address already exists
            $stmt = $conn->prepare("SELECT address_id FROM addresses WHERE user_id = ? AND address_line1 = ? AND postal_code = ?");
            $stmt->bind_param("iss", $user_id, $address_line1, $postal_code);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                // Address exists, update it and set as default
                $existing_address = $result->fetch_assoc();
                $address_id = $existing_address['address_id'];
                $stmt_update = $conn->prepare("UPDATE addresses SET full_name = ?, phone = ?, address_line2 = ?, city = ?, state = ?, country = ?, is_default = 1 WHERE address_id = ?");
                $stmt_update->bind_param("ssssssi", $full_name, $phone, $address_line2, $city, $state, $country, $address_id);
                $stmt_update->execute();
                $stmt_update->close();
            } else {
                // New address, insert it
                $stmt_insert = $conn->prepare("INSERT INTO addresses (user_id, full_name, phone, address_line1, address_line2, city, state, postal_code, country, is_default) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1)");
                $stmt_insert->bind_param("isssssssss", $user_id, $full_name, $phone, $address_line1, $address_line2, $city, $state, $postal_code, $country);
                $stmt_insert->execute();
                $address_id = $stmt_insert->insert_id;
                $stmt_insert->close();
            }
            $stmt->close();
        } else {
            // User did not check "save", so we save it as a one-time address for this order
            // We set is_default = 0 so it doesn't become their main address
            $stmt_insert = $conn->prepare("INSERT INTO addresses (user_id, full_name, phone, address_line1, address_line2, city, state, postal_code, country, is_default) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 0)");
            
            // *** THIS IS THE CORRECTED LINE ***
            $stmt_insert->bind_param("issssssss", $user_id, $full_name, $phone, $address_line1, $address_line2, $city, $state, $postal_code, $country);
            
            $stmt_insert->execute();
            $address_id = $stmt_insert->insert_id;
            $stmt_insert->close();
        }
        
        // --- 2. Create Order ---
        $payment_method = $_POST['payment_method'];
        $order_status = 'Processing'; //Set all new orders to "Processing"
        $total_amount = 0;

        // Fetch prices from DB to ensure they are correct
        $product_ids_keys = array_keys($_SESSION['cart']);
        $product_ids_sql = implode(',', array_map('intval', $product_ids_keys)); // Securely implode IDs
        
        if (empty($product_ids_sql)) {
            throw new Exception("Cart is empty.");
        }

        $sql = "SELECT product_id, price, stock_quantity FROM products WHERE product_id IN ($product_ids_sql)";
        $result = $conn->query($sql);
        $products_from_db = [];
        while($row = $result->fetch_assoc()) {
            $products_from_db[$row['product_id']] = $row;
        }

        // Calculate total and check stock
        foreach ($_SESSION['cart'] as $product_id => $quantity) {
            if (!isset($products_from_db[$product_id])) {
                throw new Exception("Product ID $product_id not found.");
            }
            if ($quantity > $products_from_db[$product_id]['stock_quantity']) {
                throw new Exception("Not enough stock for product.");
            }
            $total_amount += $products_from_db[$product_id]['price'] * $quantity;
        }

        // Insert into `orders` table
        $stmt_order = $conn->prepare("INSERT INTO orders (user_id, total_amount, shipping_address_id, payment_method, order_status) VALUES (?, ?, ?, ?, ?)");
        $stmt_order->bind_param("idiss", $user_id, $total_amount, $address_id, $payment_method, $order_status);
        $stmt_order->execute();
        $order_id = $stmt_order->insert_id;
        $stmt_order->close();
        
        // --- 3. Create Order Items and Update Stock ---
        $stmt_item = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price_at_purchase) VALUES (?, ?, ?, ?)");
        $stmt_stock = $conn->prepare("UPDATE products SET stock_quantity = stock_quantity - ? WHERE product_id = ?");
        
        foreach ($_SESSION['cart'] as $product_id => $quantity) {
            $price_at_purchase = $products_from_db[$product_id]['price'];
            
            // Insert order item
            $stmt_item->bind_param("iiid", $order_id, $product_id, $quantity, $price_at_purchase);
            $stmt_item->execute();
            
            // Update stock
            $stmt_stock->bind_param("ii", $quantity, $product_id);
            $stmt_stock->execute();
        }
        $stmt_item->close();
        $stmt_stock->close();

        // --- 4. Finalize ---
        // Commit the transaction
        $conn->commit();
        
        // Clear the cart
        unset($_SESSION['cart']);
        
        // Redirect to confirmation page
        header("location: order_confirmation.php?order_id=" . $order_id);
        exit;
        
    } catch (Exception $e) {
        // Something went wrong, roll back
        $conn->rollback();
        // Set an error message and redirect back to checkout
        $_SESSION['checkout_error'] = "Error placing order: "."Please fill all the details";
        header("location: checkout.php");
        exit;
    }
}
?>