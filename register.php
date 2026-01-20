<?php
// register.php
include 'includes/header.php';

$name = $email = $password = "";
$error_msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate name
    $name = trim($_POST["name"]);
    if (empty($name)) {
        $error_msg = "Please enter your name.";
    }

    // Validate email
    $email = trim($_POST["email"]);
    if (empty($email)) {
        $error_msg = "Please enter your email.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_msg = "Invalid email format.";
    } else {
        // Check if email is already taken
        $sql = "SELECT user_id FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows == 1) {
            $error_msg = "This email is already registered.";
        }
        $stmt->close();
    }

    // Validate password
    $password = trim($_POST["password"]);
    if (empty($password) || strlen($password) < 6) {
        $error_msg = "Password must have at least 6 characters.";
    }

    // If no errors, insert into database
    if (empty($error_msg)) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $name, $email, $password_hash);
        
        if ($stmt->execute()) {
            // Redirect to login page
            header("location: login.php?registered=true");
            exit;
        } else {
            $error_msg = "Something went wrong. Please try again later.";
        }
        $stmt->close();
    }
}
?>

<div class="container">
    <div class="form-container">
        <h1 class="page-title">Sign Up</h1>
        <p>Create your account to start shopping.</p>
        
        <?php 
        if (!empty($error_msg)) {
            echo '<div class="alert alert-danger">' . $error_msg . '</div>';
        }
        ?>
        
        <form action="register.php" method="post">
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" name="name" id="name" class="form-control" value="<?php echo htmlspecialchars($name); ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" class="form-control" value="<?php echo htmlspecialchars($email); ?>" required>
            </div>
            <div class="form-group">
                <label for="password">Password (min. 6 characters)</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary btn-block">Create Account</button>
            </div>
        </form>
        <p>Already have an account? <a href="login.php">Sign In</a></p>
    </div>
</div>

<?php include 'includes/footer.php'; ?>