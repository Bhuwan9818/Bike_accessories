-- ============================================================
-- BIKE ACCESSORIES INDIA — Database Setup
-- Import this file into phpMyAdmin or run: mysql -u root -p < database.sql
-- ============================================================

CREATE DATABASE IF NOT EXISTS bike_accessories CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE bike_accessories;

-- ADMIN USERS
CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(200),
    email VARCHAR(200),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Default admin: username=admin, password=Admin@123
INSERT INTO admin_users (username, password, full_name, email) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'admin@bikeaccessories.in');

-- CATEGORIES
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    slug VARCHAR(200) NOT NULL UNIQUE,
    icon VARCHAR(10) DEFAULT '🔧',
    description TEXT,
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO categories (name, slug, icon, description, sort_order) VALUES
('Petrol Tank', 'petrol-tank', '⛽', 'OEM and aftermarket petrol tanks for all major bike and scooter models.', 1),
('Bike Fiber With Tank', 'bike-fiber', '🏍️', 'Complete fiber body kits with tank covers for bikes.', 2),
('Scooty Body Kit', 'scooty-body', '🛵', 'Full body panel kits for scooters and mopeds.', 3),
('Bike & Scooty Seat', 'seat', '🪑', 'Replacement seats and seat covers for bikes and scooters.', 4),
('Bike & Scooty Silencer', 'silencer', '💨', 'Stock and performance silencers for all models.', 5),
('Headlight', 'headlight', '💡', 'LED and halogen headlight assemblies for bikes and scooters.', 6),
('Shocker / Suspension', 'shocker', '🔩', 'Front and rear shock absorbers for all models.', 7),
('Speedometer / Meter', 'speedometer', '🎯', 'Digital and analog speedometer clusters.', 8);

-- PRODUCTS
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name VARCHAR(300) NOT NULL,
    slug VARCHAR(300) NOT NULL UNIQUE,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    old_price DECIMAL(10,2),
    compat VARCHAR(300),
    rating DECIMAL(2,1) DEFAULT 4.5,
    reviews_count INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    is_featured TINYINT(1) DEFAULT 0,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);

-- PRODUCT IMAGES
CREATE TABLE IF NOT EXISTS product_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    image_path VARCHAR(500) NOT NULL,
    is_cover TINYINT(1) DEFAULT 0,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- SITE SETTINGS
CREATE TABLE IF NOT EXISTS site_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO site_settings (setting_key, setting_value) VALUES
('site_name', 'Bike Accessories India'),
('site_tagline', "India's #1 Bike Parts Marketplace"),
('contact_phone', '1800-123-BIKE'),
('contact_email', 'help@bikeaccessories.in'),
('whatsapp', '+91-9999999999'),
('free_shipping_above', '999'),
('logo_path', '');
