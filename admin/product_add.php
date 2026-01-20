<?php
// admin/product_add.php
include 'includes/header.php';

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $old_price = !empty($_POST['old_price']) ? $_POST['old_price'] : NULL;
    $stock = $_POST['stock'];
    $category_id = $_POST['category_id'];

    // 1. Handle Main Image Upload (Required)
    $target_dir = "../images/";
    $main_file_name = basename($_FILES["image"]["name"]);
    $unique_main_name = time() . "_main_" . $main_file_name;
    $target_file = $target_dir . $unique_main_name;
    $db_main_image = "images/" . $unique_main_name;

    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        
        // 2. Insert Product Data
        $stmt = $conn->prepare("INSERT INTO products (name, description, price, old_price, stock_quantity, category_id, image_url) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssddiis", $name, $description, $price, $old_price, $stock, $category_id, $db_main_image);
        
        if ($stmt->execute()) {
            $new_product_id = $stmt->insert_id; // Get the ID of the product we just created
            
            // 3. Handle Multiple Gallery Images (Optional)
            if (isset($_FILES['gallery']) && !empty($_FILES['gallery']['name'][0])) {
                $total_files = count($_FILES['gallery']['name']);
                
                $stmt_img = $conn->prepare("INSERT INTO product_images (product_id, image_url) VALUES (?, ?)");
                
                for ($i = 0; $i < $total_files; $i++) {
                    $g_name = basename($_FILES['gallery']['name'][$i]);
                    $g_unique = time() . "_gallery_" . $i . "_" . $g_name;
                    $g_target = $target_dir . $g_unique;
                    $g_db_path = "images/" . $g_unique;
                    
                    if (move_uploaded_file($_FILES['gallery']['tmp_name'][$i], $g_target)) {
                        $stmt_img->bind_param("is", $new_product_id, $g_db_path);
                        $stmt_img->execute();
                    }
                }
                $stmt_img->close();
            }

            echo "<script>alert('Product and gallery images added successfully!'); window.location.href='products.php';</script>";
        } else {
            $error = "Database Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $error = "Error uploading main image.";
    }
}
?>

<div class="header-bar">
    <h1>Add New Product</h1>
    <a href="products.php" class="btn-action btn-back">Back to List</a>
</div>

<div class="form-container" style="max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px;">
    <?php if($error) echo "<p style='color:red; background:#fee; padding:10px;'>$error</p>"; ?>

    <form action="" method="post" enctype="multipart/form-data">
        <div class="form-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            
            <div style="grid-column: span 2;">
                <label style="display:block; margin-bottom:5px; font-weight:bold;">Product Name</label>
                <input type="text" name="name" required style="width:100%; padding:10px; border:1px solid #ddd; border-radius:4px;">
            </div>

            <div style="grid-column: span 2;">
                <label style="display:block; margin-bottom:5px; font-weight:bold;">Description</label>
                <textarea name="description" rows="4" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:4px;"></textarea>
            </div>

            <div>
                <label style="display:block; margin-bottom:5px; font-weight:bold;">Price (Rs.)</label>
                <input type="number" name="price" required style="width:100%; padding:10px; border:1px solid #ddd; border-radius:4px;">
            </div>

            <div>
                <label style="display:block; margin-bottom:5px; font-weight:bold;">Old Price (Optional)</label>
                <input type="number" name="old_price" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:4px;">
            </div>

            <div>
                <label style="display:block; margin-bottom:5px; font-weight:bold;">Stock Quantity</label>
                <input type="number" name="stock" value="10" required style="width:100%; padding:10px; border:1px solid #ddd; border-radius:4px;">
            </div>

            <div>
                <label style="display:block; margin-bottom:5px; font-weight:bold;">Category</label>
                <select name="category_id" required style="width:100%; padding:10px; border:1px solid #ddd; border-radius:4px;">
                    <?php
                    $cats = $conn->query("SELECT * FROM categories");
                    while($c = $cats->fetch_assoc()) {
                        echo "<option value='".$c['category_id']."'>".$c['name']."</option>";
                    }
                    ?>
                </select>
            </div>

            <div style="grid-column: span 2; background: #f9f9f9; padding: 15px; border-radius: 4px;">
                <label style="display:block; margin-bottom:5px; font-weight:bold;">Main Thumbnail Image (Required)</label>
                <input type="file" name="image" required accept="image/*" style="width:100%;">
            </div>

            <div style="grid-column: span 2; background: #eef2f3; padding: 15px; border-radius: 4px;">
                <label style="display:block; margin-bottom:5px; font-weight:bold;">Gallery Images (Select Multiple)</label>
                <p style="margin:0 0 10px; font-size:0.85rem; color:#666;">Hold <strong>Ctrl</strong> (or Cmd) to select multiple photos.</p>
                <input type="file" name="gallery[]" multiple accept="image/*" style="width:100%;">
            </div>

        </div>

        <div style="margin-top: 20px; text-align: right;">
            <button type="submit" style="background:#2ecc71; color:white; border:none; padding:12px 25px; font-size:1rem; border-radius:4px; cursor:pointer;">Save Product</button>
        </div>
    </form>
</div>

<style>
    .btn-back { background: #95a5a6; color: white; text-decoration:none; padding:10px 15px; border-radius:4px; }
    .btn-back:hover { background: #7f8c8d; }
</style>

<?php include 'includes/footer.php'; ?>