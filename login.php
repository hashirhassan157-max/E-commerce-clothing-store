<?php
// login.php
include 'includes/header.php';

// If user is already logged in, redirect to homepage
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: index.php");
    exit;
}

$email = $password = "";
$error_msg = "";

// Check for registration success message
$success_msg = "";
if (isset($_GET['registered']) && $_GET['registered'] == 'true') {
    $success_msg = "Registration successful! Please log in.";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    
    if (empty($email) || empty($password)) {
        $error_msg = "Please enter both email and password.";
    } else {
        $sql = "SELECT user_id, name, email, password_hash FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        
        if ($stmt->execute()) {
            $stmt->store_result();
            
            if ($stmt->num_rows == 1) {
                $stmt->bind_result($user_id, $name, $email_db, $hashed_password);
                if ($stmt->fetch()) {
                    if (password_verify($password, $hashed_password)) {
                        // Password is correct, start a new session
                        session_regenerate_id(); // Security precaution
                        
                        $_SESSION["loggedin"] = true;
                        $_SESSION["user_id"] = $user_id;
                        $_SESSION["name"] = $name;
                        $_SESSION["email"] = $email_db;
                        
                        // Redirect to home page
                        header("location: index.php");
                        exit;
                    } else {
                        $error_msg = "Invalid email or password.";
                    }
                }
            } else {
                $error_msg = "Invalid email or password.";
            }
        } else {
            $error_msg = "Oops! Something went wrong.";
        }
        $stmt->close();
    }
}
?>

<div class="container">
    <div class="form-container">
        <h1 class="page-title">Sign In</h1>
        <p>Welcome back!</p>
        
        <?php 
        if (!empty($error_msg)) {
            echo '<div class="alert alert-danger">' . $error_msg . '</div>';
        }
        if (!empty($success_msg)) {
            echo '<div class="alert alert-success">' . $success_msg . '</div>';
        }
        ?>
        
        <form action="login.php" method="post">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" class="form-control" value="<?php echo htmlspecialchars($email); ?>" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary btn-block">Sign In</button>
            </div>
        </form>
        <p>Don't have an account? <a href="register.php">Sign Up</a></p>
    </div>
</div>

<?php include 'includes/footer.php'; ?>