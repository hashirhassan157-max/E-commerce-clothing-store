<?php
// admin/product_edit.php
include 'includes/header.php';

if (!isset($_GET['id'])) {
    header("Location: products.php");
    exit;
}

$product_id = (int)$_GET['id'];
$message = "";

// --- 1. Handle Delete Gallery Image Action ---
if (isset($_GET['delete_image'])) {
    $img_id = (int)$_GET['delete_image'];
    $conn->query("DELETE FROM product_images WHERE image_id = $img_id");
    // Redirect back to edit page to refresh
    header("Location: product_edit.php?id=$product_id&msg=ImageDeleted");
    exit;
}

// --- 2. Handle Form Submission (Update Product) ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $old_price = !empty($_POST['old_price']) ? $_POST['old_price'] : NULL;
    $stock = $_POST['stock'];
    $category_id = $_POST['category_id'];

    // Update Text Data
    $stmt = $conn->prepare("UPDATE products SET name=?, description=?, price=?, old_price=?, stock_quantity=?, category_id=? WHERE product_id=?");
    $stmt->bind_param("ssddiii", $name, $description, $price, $old_price, $stock, $category_id, $product_id);
    
    if ($stmt->execute()) {
        $message = "Product updated successfully!";
        
        // Handle Main Image Replacement (Optional)
        if (!empty($_FILES["image"]["name"])) {
            $target_dir = "../images/";
            $unique_name = time() . "_main_" . basename($_FILES["image"]["name"]);
            $target_file = $target_dir . $unique_name;
            $db_path = "images/" . $unique_name;
            
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $conn->query("UPDATE products SET image_url='$db_path' WHERE product_id=$product_id");
            }
        }

        // Handle Adding NEW Gallery Images
        if (isset($_FILES['gallery']) && !empty($_FILES['gallery']['name'][0])) {
            $target_dir = "../images/";
            $total_files = count($_FILES['gallery']['name']);
            $stmt_img = $conn->prepare("INSERT INTO product_images (product_id, image_url) VALUES (?, ?)");
            
            for ($i = 0; $i < $total_files; $i++) {
                $g_name = basename($_FILES['gallery']['name'][$i]);
                $g_unique = time() . "_new_" . $i . "_" . $g_name;
                if (move_uploaded_file($_FILES['gallery']['tmp_name'][$i], $target_dir . $g_unique)) {
                    $db_g_path = "images/" . $g_unique;
                    $stmt_img->bind_param("is", $product_id, $db_g_path);
                    $stmt_img->execute();
                }
            }
        }
    } else {
        $message = "Error: " . $conn->error;
    }
}

// --- 3. Fetch Current Product Data ---
$stmt = $conn->prepare("SELECT * FROM products WHERE product_id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) { echo "Product not found."; exit; }

// --- 4. Fetch Gallery Images ---
$gallery = $conn->query("SELECT * FROM product_images WHERE product_id = $product_id");
?>

<div class="header-bar">
    <h1>Edit Product: <?php echo htmlspecialchars($product['name']); ?></h1>
    <a href="products.php" class="btn-action btn-back">Back to List</a>
</div>

<div class="form-container" style="max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px;">
    <?php 
    if ($message) echo "<p style='color:green; background:#def; padding:10px;'>$message</p>"; 
    if (isset($_GET['msg']) && $_GET['msg']=='ImageDeleted') echo "<p style='color:orange; background:#fff3cd; padding:10px;'>Image Removed.</p>";
    ?>

    <form action="" method="post" enctype="multipart/form-data">
        <div class="form-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            
            <div style="grid-column: span 2;">
                <label>Product Name</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required class="form-control">
            </div>

            <div style="grid-column: span 2;">
                <label>Description</label>
                <textarea name="description" rows="4" class="form-control"><?php echo htmlspecialchars($product['description']); ?></textarea>
            </div>

            <div>
                <label>Price (Rs.)</label>
                <input type="number" name="price" value="<?php echo $product['price']; ?>" required class="form-control">
            </div>

            <div>
                <label>Old Price (Optional)</label>
                <input type="number" name="old_price" value="<?php echo $product['old_price']; ?>" class="form-control">
            </div>

            <div>
                <label>Stock Quantity</label>
                <input type="number" name="stock" value="<?php echo $product['stock_quantity']; ?>" required class="form-control">
            </div>

            <div>
                <label>Category</label>
                <select name="category_id" required class="form-control">
                    <?php
                    $cats = $conn->query("SELECT * FROM categories");
                    while($c = $cats->fetch_assoc()) {
                        $selected = ($c['category_id'] == $product['category_id']) ? 'selected' : '';
                        echo "<option value='".$c['category_id']."' $selected>".$c['name']."</option>";
                    }
                    ?>
                </select>
            </div>

            <div style="grid-column: span 2; background: #f9f9f9; padding: 15px; border-radius: 4px;">
                <label>Current Main Image:</label><br>
                <img src="../<?php echo $product['image_url']; ?>" style="width: 100px; height: 100px; object-fit: contain; border:1px solid #ddd; margin-bottom:10px;">
                <br>
                <label>Replace Main Image (Optional):</label>
                <input type="file" name="image" accept="image/*">
            </div>

            <div style="grid-column: span 2; background: #eef2f3; padding: 15px; border-radius: 4px;">
                <label style="font-weight:bold;">Current Gallery Images:</label>
                <div style="display:flex; gap:10px; flex-wrap:wrap; margin:10px 0;">
                    <?php while($img = $gallery->fetch_assoc()): ?>
                        <div style="position:relative;">
                            <img src="../<?php echo $img['image_url']; ?>" style="width: 80px; height: 80px; object-fit: cover; border-radius:4px;">
                            <a href="product_edit.php?id=<?php echo $product_id; ?>&delete_image=<?php echo $img['image_id']; ?>" 
                               onclick="return confirm('Delete this image?')"
                               style="position:absolute; top:-5px; right:-5px; background:red; color:white; border-radius:50%; width:20px; height:20px; text-align:center; line-height:20px; text-decoration:none; font-size:12px;">&times;</a>
                        </div>
                    <?php endwhile; ?>
                </div>
                
                <hr style="border-top:1px solid #ccc; margin:15px 0;">
                
                <label>Add More Gallery Images:</label>
                <input type="file" name="gallery[]" multiple accept="image/*">
            </div>

        </div>

        <div style="margin-top: 20px; text-align: right;">
            <button type="submit" style="background:#3498db; color:white; border:none; padding:12px 25px; font-size:1rem; border-radius:4px; cursor:pointer;">Update Product</button>
        </div>
    </form>
</div>

<style>
    .form-control { width:100%; padding:10px; border:1px solid #ddd; border-radius:4px; margin-top:5px; box-sizing:border-box; }
    .btn-back { background: #95a5a6; color: white; text-decoration:none; padding:10px 15px; border-radius:4px; }
    .btn-back:hover { background: #7f8c8d; }
</style>

<?php include 'includes/footer.php'; ?>