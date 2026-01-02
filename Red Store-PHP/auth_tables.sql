-- Modify users table to include role
ALTER TABLE users ADD COLUMN role ENUM('user', 'admin') NOT NULL DEFAULT 'user' AFTER email;

-- Create admin table for additional admin information
CREATE TABLE IF NOT EXISTS admin_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    dashboard_preferences TEXT,
    last_login TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Add approval status to orders table
ALTER TABLE orders ADD COLUMN approval_status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending' AFTER status;
ALTER TABLE orders ADD COLUMN admin_notes TEXT AFTER approval_status;

-- Insert admin user
INSERT INTO users (name, email, role, password, created_at) 
VALUES ('Admin User', 'admin@redstore.com', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NOW());
-- Note: Password is 'password' - you should change this in production

