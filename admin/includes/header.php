<?php
// admin/includes/header.php
session_start();

// 1. Check Login
if (!isset($_SESSION['admin_loggedin'])) {
    header("location: login.php");
    exit;
}

require_once '../config/db.php';

// 2. FETCH ADMIN DATA (New Logic)
// We need to get the latest image/username from the DB, not just the session
$current_admin_id = $_SESSION['admin_id'];
$stmt = $conn->prepare("SELECT username, profile_image FROM administrators WHERE admin_id = ?");
$stmt->bind_param("i", $current_admin_id);
$stmt->execute();
$current_admin = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Update session name just in case it changed
if ($current_admin) {
    $_SESSION['admin_username'] = $current_admin['username'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --primary: #2c3e50; --secondary: #34495e; --accent: #3498db; --text: #ecf0f1; }
        body { font-family: 'Segoe UI', sans-serif; margin: 0; display: flex; background: #f4f6f9; min-height: 100vh; }
        
        /* Sidebar */
        .sidebar { width: 250px; background: var(--primary); color: var(--text); height: 100vh; position: fixed; top: 0; left: 0; display: flex; flex-direction: column; }
        .sidebar-header { padding: 10px; text-align: center; background: var(--secondary); font-weight: bold; font-size: 1.2rem; }
        .sidebar-menu { list-style: none; padding: 0; margin: 0; flex-grow: 1; }
        .sidebar-menu li a { display: block; padding: 15px 20px; color: #bdc3c7; text-decoration: none; border-bottom: 1px solid rgba(255,255,255,0.05); }
        .sidebar-menu li a:hover, .sidebar-menu li a.active { background: var(--secondary); color: white; border-left: 4px solid var(--accent); }
        .sidebar-menu i { margin-right: 10px; width: 20px; text-align: center; }
        
        /* Main Content */
        .main-content { margin-left: 250px; padding: 20px; width: 100%; }
        .header-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        
        /* Cards */
        .dashboard-cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); display: flex; justify-content: space-between; align-items: center; }
        .card-info h3 { margin: 0; font-size: 2rem; color: #333; }
        .card-info p { margin: 0; color: #777; font-size: 0.9rem; }
        .card-icon { font-size: 2.5rem; color: var(--accent); opacity: 0.2; }
        
        /* Tables */
        .table-container { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #f8f9fa; color: #333; font-weight: 600; }
        .status-badge { padding: 5px 10px; border-radius: 15px; font-size: 0.8rem; font-weight: bold; }
        
        /* Form Elements */
        .form-container { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .form-control { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-header">
        PJ Admin Panel
    </div>
    
    <div style="text-align:center; padding:20px 0; border-bottom:1px solid rgba(255,255,255,0.05);">
        
        <?php if (!empty($current_admin['profile_image'])): ?>
            <img src="../<?php echo htmlspecialchars($current_admin['profile_image']); ?>" 
                 style="width: 90px; height: 90px; border-radius: 50%; object-fit: cover; margin-bottom: 10px; border: 3px solid rgba(255,255,255,0.1);">
        <?php else: ?>
            <i class="fas fa-user-circle" style="font-size:4rem; color:#bdc3c7; margin-bottom: 10px;"></i>
        <?php endif; ?>

        <p style="margin:5px 0 0; color:white; font-weight:500;">
            Hello Dear, <?php echo htmlspecialchars($current_admin['username']); ?>
        </p>
    </div>

    <ul class="sidebar-menu">
        <li><a href="index.php"><i class="fas fa-home"></i> Dashboard</a></li>
        <li><a href="products.php"><i class="fas fa-box"></i> Products</a></li>
        <li><a href="orders.php"><i class="fas fa-shopping-cart"></i> Orders</a></li>
        <li><a href="customers.php"><i class="fas fa-users"></i> Customers</a></li>
        <li><a href="profile.php"><i class="fas fa-user-cog"></i> My Profile</a></li>
        <li>
            <a href="../index.php" target="_blank" style="background:var(--accent); color:white; margin:15px 20px; text-align:center; border-radius:4px; padding:10px;">
                <i class="fas fa-external-link-alt"></i> Visit Website
            </a>
        </li>
        <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</div>

<div class="main-content">