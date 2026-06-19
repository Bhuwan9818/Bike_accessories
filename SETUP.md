# Bike Accessories India – Setup Guide

## Requirements
- PHP 7.4+ with PDO and PDO_MySQL extensions
- MySQL 5.7+ or MariaDB 10.3+
- Apache/Nginx web server
- Writable `uploads/` directory

## Installation Steps

### 1. Database Setup
```sql
mysql -u root -p < database.sql
```
Or import `database.sql` via phpMyAdmin.

### 2. Configure Database
Edit `includes/config.php`:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'bike_accessories');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
define('BASE_URL', 'http://localhost/BikeAccessoriesIndia');
define('UPLOAD_DIR', __DIR__ . '/../uploads/products/');
define('UPLOAD_URL', BASE_URL . '/uploads/products/');
```

### 3. Set Permissions
```bash
chmod 755 uploads/
chmod 755 uploads/products/
```

### 4. Admin Login
- URL: `http://yourdomain.com/admin/`  
- Username: `admin`  
- Password: `Admin@123`  
*(Change password immediately after first login)*

## File Structure
```
BikeAccessoriesIndia/
├── index.php              ← Homepage
├── database.sql           ← Database schema + seed data
├── includes/
│   ├── config.php         ← DB config & helpers
│   ├── auth.php           ← Admin session management
│   ├── functions.php      ← All DB functions
│   ├── header.php         ← Site header/nav
│   └── footer.php         ← Site footer
├── admin/
│   ├── index.php          ← Dashboard
│   ├── login.php          ← Admin login
│   ├── logout.php
│   ├── settings.php       ← Site settings
│   ├── change-password.php
│   ├── products/
│   │   ├── index.php      ← Products list
│   │   ├── add.php        ← Add product
│   │   └── edit.php       ← Edit product
│   └── categories/
│       ├── index.php      ← Categories list
│       ├── add.php        ← Add category
│       └── edit.php       ← Edit category
├── pages/
│   ├── categories/
│   │   └── view.php       ← Category page (?slug=xxx)
│   └── products/
│       └── view.php       ← Product detail page (?slug=xxx)
├── api/
│   └── search.php         ← AJAX search endpoint
├── assets/
│   ├── css/main.css       ← Frontend styles
│   ├── css/admin.css      ← Admin styles
│   ├── js/app.js          ← Frontend JS
│   └── images/placeholder.svg
└── uploads/
    └── products/          ← Uploaded product images
```
