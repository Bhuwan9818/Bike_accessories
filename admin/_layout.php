<?php
// admin/_layout.php
// Include at top of every admin page AFTER setting $pageTitle and $activeNav
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
requireLogin();
$adminName = adminName();
if (!isset($adminDepth)) $adminDepth = 0;
$root = str_repeat('../', $adminDepth);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title><?= htmlspecialchars($pageTitle ?? 'Admin') ?> – BAI Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@400;600;700&family=Barlow:wght@300;400;500;600;700;900&family=Barlow+Condensed:wght@400;600;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= str_repeat('../', $adminDepth ?? 1) ?>assets/css/admin.css">
<?php if (isset($extraCSS)) echo "<style>$extraCSS</style>"; ?>
</head>
<body>
<div class="admin-layout">

<!-- ══ SIDEBAR OVERLAY (mobile tap-to-close) ══ -->
<div class="sidebar-overlay" id="sidebar-overlay"></div>

<!-- ══ SIDEBAR ══ -->
<aside class="admin-sidebar" id="admin-sidebar">
  <div class="sidebar-logo">
    <a href="<?= $root ?>index.php" class="nav-logo">
        <img src="<?= $root ?>assets/images/logo.png" alt="">
      </a>
    <button id="closeSidebar" style="background:none;display:none; border:none;color:var(--chrome-light);font-size:22px;cursor:pointer;">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
    </button>
  </div>
  <div class="sidebar-admin-info">
    <div class="sidebar-admin-name">👤 <?= htmlspecialchars($adminName) ?></div>
    <div class="sidebar-admin-role">Administrator</div>
  </div>
  <nav class="sidebar-nav">
    <div class="sidebar-section">Main</div>
    <a href="<?= BASE_URL ?>/admin/" class="sidebar-link <?= ($activeNav??'')==='dashboard' ? 'active' : '' ?>">
      <span class="si">📊</span> Dashboard
    </a>
    <a href="<?= BASE_URL ?>/index.php" class="sidebar-link" target="_blank">
      <span class="si">🌐</span> View Store
    </a>

    <div class="sidebar-section">Catalogue</div>
    <a href="<?= BASE_URL ?>/admin/products/" class="sidebar-link <?= ($activeNav??'')==='products' ? 'active' : '' ?>">
      <span class="si">🔧</span> Products
    </a>
    <a href="<?= BASE_URL ?>/admin/products/add.php" class="sidebar-link <?= ($activeNav??'')==='add-product' ? 'active' : '' ?>">
      <span class="si">➕</span> Add Product
    </a>
    <a href="<?= BASE_URL ?>/admin/categories/" class="sidebar-link <?= ($activeNav??'')==='categories' ? 'active' : '' ?>">
      <span class="si">🗂️</span> Categories
    </a>
    <a href="<?= BASE_URL ?>/admin/categories/add.php" class="sidebar-link <?= ($activeNav??'')==='add-category' ? 'active' : '' ?>">
      <span class="si">➕</span> Add Category
    </a>

    <div class="sidebar-section">Settings</div>
    <a href="<?= BASE_URL ?>/admin/settings.php" class="sidebar-link <?= ($activeNav??'')==='settings' ? 'active' : '' ?>">
      <span class="si">⚙️</span> Site Settings
    </a>
    <a href="<?= BASE_URL ?>/admin/change-password.php" class="sidebar-link <?= ($activeNav??'')==='password' ? 'active' : '' ?>">
      <span class="si">🔑</span> Change Password
    </a>
    <a href="<?= BASE_URL ?>/admin/logout.php" class="sidebar-link" onclick="return confirm('Logout?')">
      <span class="si">🚪</span> Logout
    </a>
  </nav>
  <div class="sidebar-footer">
    <div style="font-size:11px;color:var(--text-dim)">Bike Accessories India © <?= date('Y') ?></div>
  </div>
</aside>

<!-- ══ MAIN ══ -->
<div class="admin-main">
  <!-- Top Bar -->
  <div class="admin-topbar">
    <button id="sidebar-toggle" type="button" style="background:none; display:none;  border:none;color:var(--chrome-light);font-size:22px;cursor:pointer;">☰</button>
    <div class="admin-topbar-title"><?= htmlspecialchars($pageTitle ?? 'Dashboard') ?></div>
    <div class="admin-topbar-actions">
      <a href="<?= BASE_URL ?>/admin/products/add.php" class="btn btn-red btn-sm">+ Add Product</a>
      <a href="<?= BASE_URL ?>/index.php" class="btn btn-ghost btn-sm" target="_blank">View Store →</a>
    </div>
  </div>
  <!-- Content -->
  <div class="admin-content">
