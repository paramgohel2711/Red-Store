<?php 
include 'includes/header.php';

// Require login
requireLogin();

$user = getUserDetails($conn, $_SESSION['user_id']);
$message = '';

// Handle profile update
if (isset($_POST['update_profile'])) {
    $name = $_POST['name'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $zip_code = $_POST['zip_code'];
    $phone = $_POST['phone'];
    
    if (updateUserProfile($conn, $_SESSION['user_id'], $name, $address, $city, $zip_code, $phone)) {
        $message = "Profile updated successfully";
        $user = getUserDetails($conn, $_SESSION['user_id']);
    } else {
        $message = "Failed to update profile";
    }
}

// Handle password change
if (isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    if ($new_password != $confirm_password) {
        $message = "New passwords do not match";
    } elseif (strlen($new_password) < 6) {
        $message = "Password must be at least 6 characters long";
    } elseif (changeUserPassword($conn, $_SESSION['user_id'], $current_password, $new_password)) {
        $message = "Password changed successfully";
    } else {
        $message = "Current password is incorrect";
    }
}
?>
<style>
/* Account Page Styles */
.account-page {
    padding: 50px 0;
    background: #fff;
}
.form-container {
    background: #fff;
    width: 300px;
    height: 400px;
    position: relative;
    text-align: center;
    padding: 20px 0;
    margin: auto;
    box-shadow: 0 0 20px 0px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.form-container span {
    font-weight: bold;
    padding: 0 10px;
    color: #555;
    cursor: pointer;
    display: inline-block;
}

.form-container span a {
    text-decoration: none;
    color: #555;
}

.form-btn {
    display: flex;
    justify-content: space-between;
    margin: 0 auto 30px;
    width: 220px;
}

.form-container form {
    max-width: 300px;
    padding: 0 20px;
    position: absolute;
    top: 130px;
    transition: transform 1s;
}

.form-container form input {
    width: 100%;
    height: 30px;
    margin: 10px 0;
    padding: 0 10px;
    border: 1px solid #ccc;
}

.form-container form .btn {
    width: 100%;
    border: none;
    cursor: pointer;
    margin: 10px 0;
}

.form-container form .btn:focus {
    outline: none;
}

.form-container form a {
    font-size: 12px;
    color: #555;
    text-decoration: none;
}

#indicator {
    width: 100px;
    border: none;
    background: #ff523b;
    height: 3px;
    margin-top: 8px;
    transform: translateX(0px);
    transition: transform 1s;
}

.active-form {
    color: #ff523b !important;
}

.error-message, .success-message {
    margin: 10px 0;
    padding: 10px;
    border-radius: 5px;
    font-size: 14px;
}

.error-message {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.success-message {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

/* Profile Page Styles */
.profile-menu {
    background: #f5f5f5;
    padding: 20px;
    border-radius: 5px;
}

.profile-menu h3 {
    margin-bottom: 20px;
    color: #333;
}

.profile-menu ul {
    list-style: none;
}

.profile-menu ul li {
    margin-bottom: 10px;
}

.profile-menu ul li a {
    color: #555;
    text-decoration: none;
    display: block;
    padding: 10px;
    border-radius: 5px;
    transition: all 0.3s;
}

.profile-menu ul li a:hover {
    background: #eee;
}

.profile-menu ul li a.active {
    background: #ff523b;
    color: #fff;
}

.profile-details, .order-history, .change-password {
    background: #fff;
    padding: 20px;
    border-radius: 5px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

.profile-details h3, .order-history h3, .change-password h3 {
    margin-bottom: 20px;
    color: #333;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
    color: #555;
}

.form-group input {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.order-table {
    width: 100%;
    border-collapse: collapse;
}

.order-table th {
    background: #f5f5f5;
    padding: 10px;
    text-align: left;
    color: #333;
}

.order-table td {
    padding: 10px;
    border-bottom: 1px solid #eee;
}

.message {
    margin: 10px 0;
    padding: 10px;
    border-radius: 5px;
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

</style>
<!-- Account Page -->
<div class="account-page">
    <div class="container">
        <h2>My Account</h2>
        
        <?php if (!empty($message)): ?>
            <div class="message">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <div class="row">
            <div class="col-2">
                <div class="profile-menu">
                    <h3>Welcome, <?php echo htmlspecialchars($user['name']); ?></h3>
                    <ul>
                        <li><a href="#profile" class="active">Profile</a></li>
                        <li><a href="#password">Change Password</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="col-2">
                <div id="profile" class="profile-details">
                    <h3>Profile Details</h3>
                    <form method="post" action="">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="address">Address</label>
                            <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($user['address'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="city">City</label>
                            <input type="text" id="city" name="city" value="<?php echo htmlspecialchars($user['city'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="zip_code">Zip Code</label>
                            <input type="text" id="zip_code" name="zip_code" value="<?php echo htmlspecialchars($user['zip_code'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="phone">Phone</label>
                            <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                        </div>
                        
                        <button type="submit" name="update_profile" class="btn">Update Profile</button>
                    </form>
                </div>
                
                <div id="password" class="change-password" style="display: none;">
                    <h3>Change Password</h3>
                    <form method="post" action="">
                        <div class="form-group">
                            <label for="current_password">Current Password</label>
                            <input type="password" id="current_password" name="current_password" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="new_password">New Password</label>
                            <input type="password" id="new_password" name="new_password" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="confirm_password">Confirm New Password</label>
                            <input type="password" id="confirm_password" name="confirm_password" required>
                        </div>
                        
                        <button type="submit" name="change_password" class="btn">Change Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Tab switching functionality
    document.addEventListener('DOMContentLoaded', function() {
        const menuItems = document.querySelectorAll('.profile-menu a');
        const tabs = document.querySelectorAll('#profile, #orders, #password');
        
        menuItems.forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Remove active class from all menu items
                menuItems.forEach(mi => mi.classList.remove('active'));
                
                // Add active class to clicked menu item
                this.classList.add('active');
                
                // Hide all tabs
                tabs.forEach(tab => tab.style.display = 'none');
                
                // Show the selected tab
                const targetId = this.getAttribute('href').substring(1);
                document.getElementById(targetId).style.display = 'block';
            });
        });
    });
</script>

<?php include 'includes/footer.php'; ?>