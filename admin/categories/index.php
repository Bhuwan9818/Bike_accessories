<?php
$pageTitle = 'Categories';
$activeNav = 'categories';
$adminDepth = 2;
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../_layout.php';

// Handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    deleteCategory((int)$_POST['delete_id']);
    header('Location: ' . BASE_URL . '/admin/categories/?msg=deleted');
    exit;
}

// Handle toggle active
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_id'])) {
    $cat = getCategoryById((int)$_POST['toggle_id']);
    if ($cat) {
        updateCategory((int)$_POST['toggle_id'], ['is_active' => $cat['is_active'] ? 0 : 1]);
    }
    header('Location: ' . BASE_URL . '/admin/categories/?msg=updated');
    exit;
}

$categories = getAllCategories(false);
$msg = $_GET['msg'] ?? '';
?>

<?php if ($msg === 'deleted'): ?>
<div class="alert alert-success">✅ Category deleted.</div>
<?php elseif ($msg === 'updated'): ?>
<div class="alert alert-success">✅ Category updated.</div>
<?php elseif ($msg === 'saved'): ?>
<div class="alert alert-success">✅ Category saved successfully.</div>
<?php endif; ?>

<div class="table-toolbar">
  <div style="color:var(--text-muted);font-size:13px"><?= count($categories) ?> categories total</div>
  <a href="<?= BASE_URL ?>/admin/categories/add.php" class="btn btn-red btn-sm">➕ Add Category</a>
</div>

<?php if ($categories): ?>
<div class="cats-manage-grid">
  <?php foreach ($categories as $cat): ?>
  <div class="cat-manage-card <?= !$cat['is_active'] ? 'inactive' : '' ?>">
    <div class="cmc-header">
      <div class="cmc-icon"><?= $cat['icon'] ?></div>
      <div class="cmc-meta">
        <div class="cmc-name"><?= htmlspecialchars($cat['name']) ?></div>
        <div class="cmc-slug">slug: <?= $cat['slug'] ?></div>
      </div>
      <div class="cmc-badge">
        <?= $cat['product_count'] ?> products
      </div>
    </div>
    <?php if ($cat['description']): ?>
    <div class="cmc-desc"><?= htmlspecialchars(substr($cat['description'], 0, 100)) ?><?= strlen($cat['description']) > 100 ? '…' : '' ?></div>
    <?php endif; ?>
    <div class="cmc-footer">
      <form method="POST" style="display:inline">
        <input type="hidden" name="toggle_id" value="<?= $cat['id'] ?>">
        <button type="submit" class="status-badge <?= $cat['is_active'] ? 'status-active' : 'status-inactive' ?>" style="border:none;cursor:pointer;background:none;padding:0">
          <?= $cat['is_active'] ? '✅ Active' : '⛔ Hidden' ?>
        </button>
      </form>
      <div style="display:flex;gap:.4rem;margin-left:auto">
        <a href="<?= BASE_URL ?>/admin/categories/edit.php?id=<?= $cat['id'] ?>" class="btn btn-ghost btn-xs">✏️ Edit</a>
        <a href="<?= BASE_URL ?>/pages/categories/<?= $cat['slug'] ?>.php" class="btn btn-ghost btn-xs" target="_blank">👁 View</a>
        <form method="POST" style="display:inline" onsubmit="return confirm('Delete category &quot;<?= addslashes($cat['name']) ?>&quot; and all its products?')">
          <input type="hidden" name="delete_id" value="<?= $cat['id'] ?>">
          <button type="submit" class="btn btn-danger btn-xs">🗑</button>
        </form>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>
<?php else: ?>
<div class="empty-state">
  <div class="empty-icon">🗂️</div>
  <div style="margin-bottom:1rem">No categories yet.</div>
  <a href="<?= BASE_URL ?>/admin/categories/add.php" class="btn btn-red">➕ Add First Category</a>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/../_layout_end.php'; ?>
