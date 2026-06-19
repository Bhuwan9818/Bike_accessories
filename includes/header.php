<?php
// includes/header.php
// Usage: include with $pageTitle and $depth set
// $depth = 0 for root, 1 for /admin, 2 for /pages/categories or /pages/products
if (!isset($depth)) $depth = 0;
$root = str_repeat('../', $depth);
$categories = getAllCategories();
$siteName = setting('site_name', 'Bike Accessories India');

// Check admin session for conditional display of admin links
if (session_status() === PHP_SESSION_NONE) session_start();
$_isAdmin = !empty($_SESSION['admin_id']) && !empty($_SESSION['admin_user']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title><?= htmlspecialchars($pageTitle ?? $siteName) ?> – <?= $siteName ?></title>
<link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@400;600;700&family=Barlow:wght@300;400;500;600;700;900&family=Barlow+Condensed:wght@400;600;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= $root ?>assets/css/main.css">
<script>window.BAI_ROOT = '<?= $root ?>';</script>
<?php if (isset($extraCSS)) echo "<style>$extraCSS</style>"; ?>
</head>
<body>

<!-- TOP BAR -->
<div class="topbar">
  <div class="topbar-inner">
    <span><span class="tdot"></span>FREE SHIPPING ABOVE ₹<?= setting('free_shipping_above','999') ?><span class="tdot"></span>OEM CERTIFIED<span class="tdot"></span>12-MONTH WARRANTY<span class="tdot"></span>COD AVAILABLE<span class="tdot"></span>PAN-INDIA DELIVERY<span class="tdot"></span></span>
  </div>
</div>

<!-- NAV -->
<nav id="main-nav">
  <div class="container">
    <div class="nav-inner">

      <!-- Logo -->
      <a href="<?= $root ?>index.php" class="nav-logo">
        <img src="<?= $root ?>assets/images/logo.png" alt="">
      </a>

      <!-- Desktop Links -->
      <ul class="nav-links">
        <li><a href="<?= $root ?>index.php" <?= (($activePage??'')=='home') ? "class='active'" : '' ?>>Home</a></li>
        <li class="nav-dropdown">
          <a href="#">Categories</a>
          <div class="dropdown-menu">
            <?php foreach ($categories as $cat): ?>
            <a href="<?= $root ?>pages/categories/view.php?slug=<?= $cat['slug'] ?>">
              <span class="di"><?= $cat['icon'] ?></span><?= htmlspecialchars($cat['name']) ?>
            </a>
            <?php endforeach; ?>
          </div>
        </li>
        <li><a href="<?= $root ?>index.php#why-us">Why Us</a></li>
      </ul>

      <!-- Search -->
      <div class="nav-search" id="nav-search-wrap">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
        <input type="text" id="nav-search-input" placeholder="Search products..." autocomplete="off">
        <div class="search-dropdown" id="search-dropdown"></div>
      </div>

      <!-- Icons -->
      <div class="nav-icons">
        <button class="nav-icon-btn" onclick="toggleCart()" aria-label="Cart">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/>
            <line x1="3" y1="6" x2="21" y2="6"/>
            <path d="M16 10a4 4 0 0 1-8 0"/>
          </svg>
          <span class="nbadge" id="cart-count">0</span>
        </button>
        <?php if ($_isAdmin): ?>
        <a href="<?= $root ?>admin/" class="btn btn-red btn-sm nav-admin-btn">⚙ Admin</a>
        <?php endif; ?>
        <button class="hamburger" id="hamburger" aria-label="Menu">
          <span></span><span></span><span></span>
        </button>
      </div>
    </div>
  </div>
</nav>

<!-- CART DRAWER -->
<div id="cart-overlay" onclick="toggleCart()"></div>
<div id="cart-drawer">
  <div class="cart-head">
    <div class="cart-title">🛒 YOUR CART</div>
    <button onclick="toggleCart()" class="cart-close">✕</button>
  </div>
  <div id="cart-items"></div>
  <div class="cart-foot">
    <div class="cart-total-row">
      <span>TOTAL</span>
      <span id="cart-total">₹0</span>
    </div>
    <button class="btn btn-red" style="width:100%;justify-content:center;height:50px;font-size:15px">
      Proceed to Checkout →
    </button>
  </div>
</div>

<!-- SEARCH OVERLAY -->
<div id="search-overlay">
  <div class="search-overlay-inner">
    <div class="search-overlay-box">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#888" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
      <input id="search-overlay-input" type="text" placeholder="Search products, categories...">
      <button onclick="closeSearch()" class="search-close">✕</button>
    </div>
    <div id="search-overlay-results"></div>
  </div>
</div>

<!-- MOBILE MENU -->
<div class="mobile-menu" id="mobile-menu">
  <div class="mobile-menu-inner">
    <div class="mm-search">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#888" stroke-width="2">
        <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
      </svg>
      <input type="text" placeholder="Search products..." id="mm-search-input">
    </div>
    <a href="<?= $root ?>index.php" class="mm-link <?= (($activePage??'')=='home')?'amm':'' ?>">Home</a>
    <div class="mm-cat-title">Categories</div>
    <?php foreach ($categories as $cat): ?>
    <a href="<?= $root ?>pages/categories/view.php?slug=<?= $cat['slug'] ?>" class="mm-link">
      <?= $cat['icon'] ?> <?= htmlspecialchars($cat['name']) ?>
    </a>
    <?php endforeach; ?>
    <a href="<?= $root ?>index.php#why-us" class="mm-link">Why Us</a>
    <?php if ($_isAdmin): ?>
    <a href="<?= $root ?>admin/" class="mm-link" style="color:var(--red)">⚙ Admin Panel</a>
    <div style="margin-top:auto;padding-top:2rem">
      <a href="<?= $root ?>admin/" class="btn btn-red" style="width:100%;justify-content:center">+ Add Product</a>
    </div>
    <?php endif; ?>
  </div>
</div>

