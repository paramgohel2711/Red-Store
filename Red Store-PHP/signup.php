<?php 
include 'includes/header.php';

$error = '';
$success = '';
$name = '';
$email = '';

// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validate input
    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "Please fill in all fields";
    } elseif ($password != $confirm_password) {
        $error = "Passwords do not match";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long";
    } else {
        $result = registerUser($conn, $name, $email, $password);
        
        if ($result['success']) {
            $success = "Registration successful! You can now login.";
            $name = '';
            $email = '';
        } else {
            $error = $result['message'];
        }
    }
}
?>

<!-- Signup Page -->
<div class="account-page">
    <div class="container">
        <div class="row">
            <div class="col-2">
            <img src="assets/images/image1.png" alt="Login Image" style="float: left; width: 400px; height: auto;">
            </div>
            <div class="col-2">
                <div class="form-container">
                    <div class="form-btn">
                        <span><a href="login.php">Login</a></span>
                        <span class="active-form">Register</span>
                        <hr id="indicator" style="transform: translateX(100px);">
                    </div>
                    
                    <?php if (!empty($error)): ?>
                        <div class="error-message">
                            <?php echo $error; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($success)): ?>
                        <div class="success-message">
                            <?php echo $success; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form id="RegForm" method="post" action="">
                        <input type="text" name="name" placeholder="Name"  required>
                        <input type="email" name="email" placeholder="Email"  required>
                        <input type="password" name="password" placeholder="Password" required>
                        <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                        <button type="submit" class="btn">Register</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

