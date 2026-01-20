<?php
// admin/profile.php
include 'includes/header.php';

$admin_id = $_SESSION['admin_id'];
$message = "";
$error = "";

// 1. Handle Form Submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    // Update Username
    $conn->query("UPDATE administrators SET username='$username' WHERE admin_id=$admin_id");
    $_SESSION['admin_username'] = $username; // Update session
    $message = "Profile updated!";

    // Update Password (Only if typed)
    if (!empty($password)) {
        if (strlen($password) < 6) {
            $error = "Password must be at least 6 characters.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $conn->query("UPDATE administrators SET password='$hash' WHERE admin_id=$admin_id");
            $message .= " Password changed.";
        }
    }

    // Update Image
    if (!empty($_FILES['image']['name'])) {
        $target_dir = "../images/";
        $file_name = time() . "_admin_" . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $file_name;
        $db_path = "images/" . $file_name;

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $stmt = $conn->prepare("UPDATE administrators SET profile_image = ? WHERE admin_id = ?");
            $stmt->bind_param("si", $db_path, $admin_id);
            $stmt->execute();
            $message .= " Image uploaded.";
        } else {
            $error = "Failed to upload image.";
        }
    }
}

// 2. Fetch Current Admin Data
$res = $conn->query("SELECT * FROM administrators WHERE admin_id = $admin_id");
$admin = $res->fetch_assoc();
?>

<div class="header-bar">
    <h1>My Profile</h1>
</div>

<div class="form-container" style="max-width: 600px; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
    
    <?php if($message) echo "<p style='color:green; background:#def; padding:10px;'>$message</p>"; ?>
    <?php if($error) echo "<p style='color:red; background:#fee; padding:10px;'>$error</p>"; ?>

    <form action="" method="post" enctype="multipart/form-data" style="display:flex; flex-direction:column; gap:15px;">
        
        <div style="text-align:center; margin-bottom:20px;">
            <?php 
            $img_src = $admin['profile_image'] ? "../" . $admin['profile_image'] : "https://via.placeholder.com/150";
            ?>
            <img src="<?php echo $img_src; ?>" style="width:120px; height:120px; border-radius:50%; object-fit:cover; border:3px solid #eee;">
            <br>
            <label style="cursor:pointer; color:#3498db; font-weight:bold; font-size:0.9rem;">
                Change Photo
                <input type="file" name="image" style="display:none;" onchange="this.form.submit()">
            </label>
        </div>

        <div>
            <label>Username</label>
            <input type="text" name="username" value="<?php echo htmlspecialchars($admin['username']); ?>" required class="form-control">
        </div>

        <div>
            <label>New Password <small>(Leave blank to keep current)</small></label>
            <input type="password" name="password" placeholder="******" class="form-control">
        </div>

        <button type="submit" style="background:#2c3e50; color:white; padding:12px; border:none; border-radius:4px; cursor:pointer; font-size:1rem;">Update Profile</button>
    </form>
</div>

<style>
    .form-control { width:100%; padding:10px; border:1px solid #ddd; border-radius:4px; margin-top:5px; box-sizing:border-box; }
</style>

<?php include 'includes/footer.php'; ?>