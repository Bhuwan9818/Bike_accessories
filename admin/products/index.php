<?php
$pageTitle = 'Products';
$activeNav = 'products';
$adminDepth = 2;
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../_layout.php';

// Handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    deleteProduct((int)$_POST['delete_id']);
    header('Location: ' . BASE_URL . '/admin/products/?msg=deleted');
    exit;
}

// Handle toggle active
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_id'])) {
    $prod = getProductById((int)$_POST['toggle_id']);
    if ($prod) {
        updateProduct((int)$_POST['toggle_id'], ['is_active' => $prod['is_active'] ? 0 : 1]);
    }
    header('Location: ' . BASE_URL . '/admin/products/?msg=updated');
    exit;
}

// Handle toggle featured
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_featured_id'])) {
    $prod = getProductById((int)$_POST['toggle_featured_id']);
    if ($prod) {
        updateProduct((int)$_POST['toggle_featured_id'], ['is_featured' => $prod['is_featured'] ? 0 : 1]);
    }
    header('Location: ' . BASE_URL . '/admin/products/?msg=updated');
    exit;
}

$filterCat = (int)($_GET['cat'] ?? 0);
$search    = trim($_GET['q'] ?? '');
$opts = ['limit' => 100];
if ($filterCat) $opts['category_id'] = $filterCat;
if ($search)    $opts['search'] = $search;
$products   = getProducts($opts);
$categories = getAllCategories(false);
$msg = $_GET['msg'] ?? '';
?>

<?php if ($msg === 'deleted'): ?>
<div class="alert alert-success">✅ Product deleted successfully.</div>
<?php elseif ($msg === 'updated'): ?>
<div class="alert alert-success">✅ Product updated.</div>
<?php elseif ($msg === 'saved'): ?>
<div class="alert alert-success">✅ Product saved successfully.</div>
<?php endif; ?>

<!-- Toolbar -->
<div class="table-toolbar">
  <form method="GET" style="display:flex;gap:.75rem;flex-wrap:wrap;flex:1">
    <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="Search products..." class="search-input" style="max-width:260px">
    <select name="cat" class="sort-select" onchange="this.form.submit()">
      <option value="">All Categories</option>
      <?php foreach ($categories as $c): ?>
      <option value="<?= $c['id'] ?>" <?= $filterCat == $c['id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option>
      <?php endforeach; ?>
    </select>
    <button type="submit" class="btn btn-ghost btn-sm">Filter</button>
    <?php if ($search || $filterCat): ?>
    <a href="<?= BASE_URL ?>/admin/products/" class="btn btn-ghost btn-sm">Clear</a>
    <?php endif; ?>
  </form>
  <a href="<?= BASE_URL ?>/admin/products/add.php" class="btn btn-red btn-sm">➕ Add Product</a>
</div>

<?php if ($products): ?>
<div class="table-wrap">
  <table class="admin-table">
    <thead>
      <tr>
        <th>Product</th>
        <th>Category</th>
        <th>Price</th>
        <th>Rating</th>
        <th>Status</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($products as $p): ?>
      <tr>
        <td>
          <div style="display:flex;align-items:center;gap:.75rem">
            <?php if (!empty($p['first_image'])): ?>
            <img src="<?= htmlspecialchars($p['first_image']) ?>" style="width:50px;height:50px;object-fit:cover;border-radius:6px;border:1px solid var(--border);flex-shrink:0" onerror="this.style.display='none'">
            <?php else: ?>
            <div style="width:50px;height:50px;background:var(--black);border:1px solid var(--border);border-radius:6px;display:flex;align-items:center;justify-content:center;font-size:24px;flex-shrink:0"><?= $p['category_icon'] ?? '🔧' ?></div>
            <?php endif; ?>
            <div>
              <div style="font-weight:600;color:var(--chrome-light);line-height:1.3"><?= htmlspecialchars($p['name']) ?></div>
              <?php if ($p['compat']): ?><div style="font-size:11px;color:var(--text-muted);margin-top:2px">⚙ <?= htmlspecialchars($p['compat']) ?></div><?php endif; ?>
            </div>
          </div>
        </td>
        <td><span class="cat-badge"><?= $p['category_icon'] ?? '' ?> <?= htmlspecialchars($p['category_name']) ?></span></td>
        <td>
          <div style="color:var(--red);font-weight:700;font-size:15px">₹<?= number_format($p['price'], 0) ?></div>
          <?php if ($p['old_price'] > 0): ?><div style="font-size:11px;color:var(--text-muted);text-decoration:line-through">₹<?= number_format($p['old_price'], 0) ?></div><?php endif; ?>
        </td>
        <td>
          <div style="color:#f59e0b;font-size:14px"><?= str_repeat('★', round($p['rating'])) ?></div>
          <div style="font-size:11px;color:var(--text-muted)"><?= $p['rating'] ?> (<?= $p['reviews_count'] ?>)</div>
        </td>
        <td>
          <form method="POST" style="display:inline">
            <input type="hidden" name="toggle_id" value="<?= $p['id'] ?>">
            <button type="submit" class="status-badge <?= $p['is_active'] ? 'status-active' : 'status-inactive' ?>" style="border:none;cursor:pointer;background:none;padding:0">
              <?= $p['is_active'] ? '✅ Active' : '⛔ Inactive' ?>
            </button>
          </form>
          <?php if ($p['is_featured']): ?>
          <div><form method="POST" style="display:inline">
            <input type="hidden" name="toggle_featured_id" value="<?= $p['id'] ?>">
            <button type="submit" class="status-badge status-featured" style="border:none;cursor:pointer;background:none;padding:0;margin-top:3px">⭐ Featured</button>
          </form></div>
          <?php endif; ?>
        </td>
        <td>
          <div style="display:flex;gap:.4rem;flex-wrap:wrap">
            <a href="<?= BASE_URL ?>/admin/products/edit.php?id=<?= $p['id'] ?>" class="btn btn-ghost btn-xs">✏️ Edit</a>
            <a href="<?= BASE_URL ?>/pages/products/<?= $p['slug'] ?>.php" class="btn btn-ghost btn-xs" target="_blank">👁 View</a>
            <form method="POST" style="display:inline" onsubmit="return confirm('Delete this product permanently?')">
              <input type="hidden" name="delete_id" value="<?= $p['id'] ?>">
              <button type="submit" class="btn btn-danger btn-xs">🗑 Del</button>
            </form>
          </div>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<div style="padding:.75rem 0;color:var(--text-muted);font-size:13px">
  Showing <?= count($products) ?> product<?= count($products) != 1 ? 's' : '' ?>
</div>
<?php else: ?>
<div class="empty-state">
  <div class="empty-icon">📦</div>
  <div style="margin-bottom:1rem">No products found<?= $search ? " for \"$search\"" : '' ?>.</div>
  <a href="<?= BASE_URL ?>/admin/products/add.php" class="btn btn-red">➕ Add First Product</a>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/../_layout_end.php'; ?>
