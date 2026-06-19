<?php
$pageTitle = 'Add Category';
$activeNav = 'add-category';
$adminDepth = 2;
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../_layout.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'name'        => trim($_POST['name'] ?? ''),
        'icon'        => trim($_POST['icon'] ?? '🔧'),
        'description' => trim($_POST['description'] ?? ''),
        'sort_order'  => (int)($_POST['sort_order'] ?? 0),
        'is_active'   => isset($_POST['is_active']) ? 1 : 0,
    ];

    if (!$data['name']) $errors[] = 'Category name is required.';

    if (!$errors) {
        try {
            createCategory($data);
            header('Location: ' . BASE_URL . '/admin/categories/?msg=saved');
            exit;
        } catch (Exception $e) {
            $errors[] = 'Error: ' . $e->getMessage();
        }
    }
}

$emojiOptions = ['⛽','🏍️','🛵','🪑','💨','💡','🔩','🎯','🔧','⚙️','🛞','🔦','🪝','🧲','🛡️','🔑','🪛','🔨','📦','🏁'];
?>

<?php if ($errors): ?>
<div class="alert alert-danger">
  <?php foreach ($errors as $e): ?><div>❌ <?= htmlspecialchars($e) ?></div><?php endforeach; ?>
</div>
<?php endif; ?>

<form method="POST" class="admin-form">
  <div class="form-grid-2">
    <div>
      <div class="form-card">
        <div class="form-card-title">Category Details</div>

        <div class="form-group">
          <label>Category Name <span class="req">*</span></label>
          <input type="text" name="name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" placeholder="e.g. Petrol Tank" required autofocus>
        </div>

        <div class="form-group">
          <label>Icon (Emoji)</label>
          <div style="display:flex;gap:.5rem;align-items:center">
            <input type="text" name="icon" id="icon-input" value="<?= htmlspecialchars($_POST['icon'] ?? '🔧') ?>" maxlength="10" style="width:80px;font-size:24px;text-align:center">
            <div style="font-size:12px;color:var(--text-muted)">Pick below or type your own:</div>
          </div>
          <div class="emoji-picker">
            <?php foreach ($emojiOptions as $em): ?>
            <button type="button" class="emoji-opt" onclick="document.getElementById('icon-input').value='<?= $em ?>'"><?= $em ?></button>
            <?php endforeach; ?>
          </div>
        </div>

        <div class="form-group">
          <label>Description</label>
          <textarea name="description" rows="3" placeholder="Brief description of this category..."><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
        </div>

        <div class="form-group">
          <label>Sort Order</label>
          <input type="number" name="sort_order" value="<?= htmlspecialchars($_POST['sort_order'] ?? '0') ?>" min="0">
          <div class="form-hint">Lower number = shown first on store</div>
        </div>
      </div>
    </div>

    <div>
      <div class="form-card">
        <div class="form-card-title">Visibility</div>
        <div class="form-check-group">
          <label class="form-check">
            <input type="checkbox" name="is_active" value="1" <?= !isset($_POST['is_active']) || $_POST['is_active'] ? 'checked' : '' ?>>
            <span class="form-check-label">
              <span class="fck-title">Active</span>
              <span class="fck-desc">Show this category on the store</span>
            </span>
          </label>
        </div>
      </div>

      <div class="form-card">
        <div class="form-card-title">Preview</div>
        <div style="background:var(--black);border:1px solid var(--border);border-radius:10px;padding:1.5rem;text-align:center">
          <div style="font-size:48px" id="preview-icon">🔧</div>
          <div style="font-family:'Barlow Condensed',sans-serif;font-size:18px;font-weight:800;color:var(--chrome-light);margin-top:.5rem" id="preview-name">Category Name</div>
          <div style="font-size:12px;color:var(--text-muted);margin-top:.25rem">0+ products</div>
        </div>
      </div>

      <div style="display:flex;gap:.75rem;margin-top:1rem">
        <button type="submit" class="btn btn-red" style="flex:1;justify-content:center">💾 Save Category</button>
        <a href="<?= BASE_URL ?>/admin/categories/" class="btn btn-ghost">Cancel</a>
      </div>
    </div>
  </div>
</form>

<script>
const nameInput = document.querySelector('input[name="name"]');
const iconInput = document.getElementById('icon-input');
const previewIcon = document.getElementById('preview-icon');
const previewName = document.getElementById('preview-name');

nameInput.addEventListener('input', () => {
  previewName.textContent = nameInput.value || 'Category Name';
});
iconInput.addEventListener('input', () => {
  previewIcon.textContent = iconInput.value || '🔧';
});
document.querySelectorAll('.emoji-opt').forEach(btn => {
  btn.addEventListener('click', () => {
    previewIcon.textContent = iconInput.value;
  });
});
</script>

<?php require_once __DIR__ . '/../_layout_end.php'; ?>
