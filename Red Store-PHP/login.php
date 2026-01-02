<?php 
include 'includes/header.php';

$error = '';
$email = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    // Validate input
    if (empty($email) || empty($password)) {
        $error = "Please enter both email and password";
    } else {
        $result = loginUser($conn, $email, $password);
        
        if ($result['success']) {
            // Redirect based on role
            if ($result['role'] == 'admin') {
                header("Location: admin/index.php");
            } else {
                header("Location: index.php");
            }
            exit();
        } else {
            $error = $result['message'];
        }
    }
}
?>

<!-- Login Page -->
<div class="account-page">
    <div class="container">
        <div class="row">
            <div class="col-2">
            <img src="assets/images/image1.png" alt="Login Image" style="float: left; width: 400px; height: auto;">

            </div>
            <div class="col-2">
                <div class="form-container">
                    <div class="form-btn">
                        <span class="active-form">Login</span>
                        <span><a href="signup.php">Register</a></span>
                        <hr id="indicator">
                    </div>
                    
                    <?php if (!empty($error)): ?>
                        <div class="error-message">
                            <?php echo $error; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form id="LoginForm" method="post" action="">
                        <input type="email" name="email" placeholder="Email" value="<?php echo htmlspecialchars($email); ?>" required>
                        <input type="password" name="password" placeholder="Password" required>
                        <button type="submit" class="btn">Login</button>
                        <a href="forgot-password.php">Forgot password?</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

