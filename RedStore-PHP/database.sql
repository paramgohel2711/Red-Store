-- Create database
CREATE DATABASE IF NOT EXISTS redstore;
USE redstore;

-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    address VARCHAR(255),
    city VARCHAR(100),
    zip_code VARCHAR(20),
    phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create categories table
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create products table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    image VARCHAR(255),
    rating DECIMAL(3, 2) DEFAULT 0,
    stock INT DEFAULT 0,
    featured BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- Create orders table
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    total_amount DECIMAL(10, 2) NOT NULL,
    receiver_name VARCHAR(100) NOT NULL,
    address VARCHAR(255) NOT NULL,
    zip_code VARCHAR(20) NOT NULL,
    payment_method VARCHAR(50) NOT NULL,
    status VARCHAR(50) DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Create order_items table
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Insert sample categories
INSERT INTO categories (name) VALUES 
('Shoes'), 
('T-shirts'), 
('Watches'), 
('Socks'),
('Joggers'),
('Smart Bands');

-- Insert sample products
INSERT INTO products (category_id, name, description, price, image, rating, stock, featured) VALUES
(1, 'Red Printed Shoes (gray)', 'Comfortable gray shoes perfect for casual wear', 499, 'gray-shoes.jpg', 4.0, 50, FALSE),
(1, 'Sports Shoes', 'Black sports shoes with excellent grip and comfort', 499, 'sports-shoes.jpg', 3.0, 45, TRUE),
(2, 'Red Printed T-shirt', 'Stylish red t-shirt with printed design', 499, 'red-tshirt.jpg', 4.0, 60, TRUE),
(2, 'Red Printed T-shirt (black)', 'Classic black t-shirt with printed design', 499, 'black-tshirt.jpg', 3.0, 55, FALSE),
(2, 'Red Printed T-shirt (navy blue)', 'Navy blue t-shirt with printed design', 499, 'navy-tshirt.jpg', 3.0, 40, TRUE),
(3, 'Red Printed Watch', 'Elegant black watch for everyday use', 499, 'watch.jpg', 3.0, 30, FALSE),
(4, 'Red Printed Socks', 'Comfortable socks for daily wear', 499, 'socks.jpg', 4.0, 100, FALSE),
(5, 'Red Printed Joggers', 'Comfortable joggers for workout and casual wear', 499, 'joggers.jpg', 4.0, 35, TRUE),
(6, 'Smart Band 4', 'The Mi Smart Band 4 features a 39.9% larger (than Mi Band 3) AMOLED color full display with adjustable brightness, so everything is clear as can be.', 1999, 'smart-band.jpg', 4.5, 25, TRUE);

