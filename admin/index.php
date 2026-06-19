<?php
$pageTitle = 'Dashboard';
$activeNav = 'dashboard';
$adminDepth = 1;
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/_layout.php';

$stats = getDashboardStats();
$recentProducts = getProducts(['limit' => 6]);
$categories = getAllCategories(false);
?>

<!-- Stats Cards -->
<div class="stats-grid">
  <div class="stat-card">
    <div class="stat-icon">🔧</div>
    <div class="stat-body">
      <div class="stat-num"><?= $stats['products'] ?></div>
      <div class="stat-label">Active Products</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon">🗂️</div>
    <div class="stat-body">
      <div class="stat-num"><?= $stats['categories'] ?></div>
      <div class="stat-label">Categories</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon">⭐</div>
    <div class="stat-body">
      <div class="stat-num"><?= $stats['featured'] ?></div>
      <div class="stat-label">Featured</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon">🖼️</div>
    <div class="stat-body">
      <div class="stat-num"><?= $stats['images'] ?></div>
      <div class="stat-label">Product Images</div>
    </div>
  </div>
</div>

<!-- Quick Actions -->
<div class="dash-section">
  <div class="section-head">
    <div class="section-title">Quick Actions</div>
  </div>
  <div style="display:flex;gap:1rem;flex-wrap:wrap">
    <a href="<?= BASE_URL ?>/admin/products/add.php" class="btn btn-red">➕ Add Product</a>
    <a href="<?= BASE_URL ?>/admin/categories/add.php" class="btn btn-ghost">➕ Add Category</a>
    <a href="<?= BASE_URL ?>/admin/products/" class="btn btn-ghost">🔧 Manage Products</a>
    <a href="<?= BASE_URL ?>/admin/categories/" class="btn btn-ghost">🗂️ Manage Categories</a>
    <a href="<?= BASE_URL ?>/admin/settings.php" class="btn btn-ghost">⚙️ Site Settings</a>
  </div>
</div>

<!-- Recent Products -->
<div class="dash-section">
  <div class="section-head">
    <div class="section-title">Recent Products</div>
    <a href="<?= BASE_URL ?>/admin/products/" class="btn btn-ghost btn-sm">View All →</a>
  </div>
  <?php if ($recentProducts): ?>
  <div class="table-wrap">
    <table class="admin-table">
      <thead>
        <tr>
          <th>Product</th>
          <th>Category</th>
          <th>Price</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($recentProducts as $p): ?>
        <tr>
          <td>
            <div style="display:flex;align-items:center;gap:.75rem">
              <?php if (!empty($p['first_image'])): ?>
              <img src="<?= htmlspecialchars($p['first_image']) ?>" style="width:44px;height:44px;object-fit:cover;border-radius:6px;border:1px solid var(--border)" onerror="this.style.display='none'">
              <?php endif; ?>
              <div>
                <div style="font-weight:600;color:var(--chrome-light)"><?= htmlspecialchars($p['name']) ?></div>
                <?php if ($p['compat']): ?><div style="font-size:12px;color:var(--text-muted)"><?= htmlspecialchars($p['compat']) ?></div><?php endif; ?>
              </div>
            </div>
          </td>
          <td><span class="cat-badge"><?= $p['category_icon'] ?? '' ?> <?= htmlspecialchars($p['category_name']) ?></span></td>
          <td>
            <div style="color:var(--red);font-weight:700">₹<?= number_format($p['price'], 0) ?></div>
            <?php if ($p['old_price']): ?><div style="font-size:11px;color:var(--text-muted);text-decoration:line-through">₹<?= number_format($p['old_price'], 0) ?></div><?php endif; ?>
          </td>
          <td>
            <span class="status-badge <?= $p['is_active'] ? 'status-active' : 'status-inactive' ?>">
              <?= $p['is_active'] ? 'Active' : 'Inactive' ?>
            </span>
            <?php if ($p['is_featured']): ?>
            <span class="status-badge status-featured">⭐ Featured</span>
            <?php endif; ?>
          </td>
          <td>
            <div style="display:flex;gap:.5rem">
              <a href="<?= BASE_URL ?>/admin/products/edit.php?id=<?= $p['id'] ?>" class="btn btn-ghost btn-xs">Edit</a>
              <a href="<?= BASE_URL ?>/pages/products/<?= $p['slug'] ?>.php" class="btn btn-ghost btn-xs" target="_blank">View</a>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php else: ?>
  <div class="empty-state">
    <div class="empty-icon">📦</div>
    <div>No products yet. <a href="<?= BASE_URL ?>/admin/products/add.php" style="color:var(--red)">Add your first product →</a></div>
  </div>
  <?php endif; ?>
</div>

<!-- Categories Overview -->
<div class="dash-section">
  <div class="section-head">
    <div class="section-title">Categories Overview</div>
    <a href="<?= BASE_URL ?>/admin/categories/" class="btn btn-ghost btn-sm">Manage →</a>
  </div>
  <div class="cats-overview-grid">
    <?php foreach ($categories as $cat): ?>
    <div class="cat-overview-card <?= !$cat['is_active'] ? 'inactive' : '' ?>">
      <div class="coc-icon"><?= $cat['icon'] ?></div>
      <div class="coc-name"><?= htmlspecialchars($cat['name']) ?></div>
      <div class="coc-count"><?= $cat['product_count'] ?> products</div>
      <a href="<?= BASE_URL ?>/admin/categories/edit.php?id=<?= $cat['id'] ?>" class="coc-edit">Edit</a>
    </div>
    <?php endforeach; ?>
    <a href="<?= BASE_URL ?>/admin/categories/add.php" class="cat-overview-card add-new-cat">
      <div class="coc-icon">➕</div>
      <div class="coc-name">Add Category</div>
    </a>
  </div>
</div>

<?php require_once __DIR__ . '/_layout_end.php'; ?>
