<?php
$pageTitle = 'Add Product';
$activeNav = 'add-product';
$adminDepth = 2;
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../_layout.php';

$categories = getAllCategories(false);
$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'category_id'   => (int)($_POST['category_id'] ?? 0),
        'name'          => trim($_POST['name'] ?? ''),
        'description'   => trim($_POST['description'] ?? ''),
        'price'         => (float)($_POST['price'] ?? 0),
        'old_price'     => (float)($_POST['old_price'] ?? 0) ?: null,
        'compat'        => trim($_POST['compat'] ?? ''),
        'rating'        => max(1, min(5, (float)($_POST['rating'] ?? 4.5))),
        'reviews_count' => max(0, (int)($_POST['reviews_count'] ?? 0)),
        'is_active'     => isset($_POST['is_active']) ? 1 : 0,
        'is_featured'   => isset($_POST['is_featured']) ? 1 : 0,
        'sort_order'    => (int)($_POST['sort_order'] ?? 0),
    ];

    if (!$data['category_id']) $errors[] = 'Please select a category.';
    if (!$data['name'])        $errors[] = 'Product name is required.';
    if ($data['price'] <= 0)   $errors[] = 'Price must be greater than 0.';

    if (!$errors) {
        try {
            $productId = createProduct($data);

            // Handle image uploads
            $coverSet = false;
            if (!empty($_FILES['images']['name'][0])) {
                foreach ($_FILES['images']['name'] as $i => $fname) {
                    if (!$fname) continue;
                    $file = [
                        'name'     => $_FILES['images']['name'][$i],
                        'type'     => $_FILES['images']['type'][$i],
                        'tmp_name' => $_FILES['images']['tmp_name'][$i],
                        'error'    => $_FILES['images']['error'][$i],
                        'size'     => $_FILES['images']['size'][$i],
                    ];
                    if ($file['error'] !== UPLOAD_ERR_OK) continue;
                    try {
                        $url = uploadProductImage($file);
                        addProductImage($productId, $url, !$coverSet, $i);
                        $coverSet = true;
                    } catch (Exception $e) {
                        $errors[] = 'Image upload failed: ' . $e->getMessage();
                    }
                }
            }

            if (!$errors) {
                header('Location: ' . BASE_URL . '/admin/products/?msg=saved');
                exit;
            }
        } catch (Exception $e) {
            $errors[] = 'Error saving product: ' . $e->getMessage();
        }
    }
}

$preselectedCat = (int)($_GET['category'] ?? 0);
?>

<?php if ($errors): ?>
<div class="alert alert-danger">
  <?php foreach ($errors as $e): ?><div>❌ <?= htmlspecialchars($e) ?></div><?php endforeach; ?>
</div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data" class="admin-form">
  <div class="form-grid-2">

    <!-- LEFT COLUMN -->
    <div>
      <div class="form-card">
        <div class="form-card-title">Basic Information</div>

        <div class="form-group">
          <label>Product Name <span class="req">*</span></label>
          <input type="text" name="name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" placeholder="e.g. Hero Splendor+ Petrol Tank OEM" required>
        </div>

        <div class="form-group">
          <label>Category <span class="req">*</span></label>
          <select name="category_id" required>
            <option value="">— Select Category —</option>
            <?php foreach ($categories as $cat): ?>
            <option value="<?= $cat['id'] ?>" <?= (($_POST['category_id'] ?? $preselectedCat) == $cat['id']) ? 'selected' : '' ?>>
              <?= $cat['icon'] ?> <?= htmlspecialchars($cat['name']) ?>
            </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-group">
          <label>Description</label>
          <textarea name="description" rows="4" placeholder="Product details, features, fitment info..."><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
        </div>

        <div class="form-group">
          <label>Compatible Models</label>
          <input type="text" name="compat" value="<?= htmlspecialchars($_POST['compat'] ?? '') ?>" placeholder="e.g. Hero Splendor+, Hero HF Deluxe 2018–2024">
          <div class="form-hint">List compatible bike/scooter models</div>
        </div>
      </div>

      <div class="form-card">
        <div class="form-card-title">Pricing</div>
        <div class="form-row-2">
          <div class="form-group">
            <label>Selling Price (₹) <span class="req">*</span></label>
            <input type="number" name="price" value="<?= htmlspecialchars($_POST['price'] ?? '') ?>" min="1" step="0.01" required placeholder="e.g. 1299">
          </div>
          <div class="form-group">
            <label>Original Price (₹) <span style="color:var(--text-muted)">(optional)</span></label>
            <input type="number" name="old_price" value="<?= htmlspecialchars($_POST['old_price'] ?? '') ?>" min="0" step="0.01" placeholder="e.g. 1799">
            <div class="form-hint">Leave blank if no discount</div>
          </div>
        </div>
      </div>

      <div class="form-card">
        <div class="form-card-title">Reviews & Sorting</div>
        <div class="form-row-2">
          <div class="form-group">
            <label>Rating (1–5)</label>
            <input type="number" name="rating" value="<?= htmlspecialchars($_POST['rating'] ?? '4.5') ?>" min="1" max="5" step="0.1">
          </div>
          <div class="form-group">
            <label>Reviews Count</label>
            <input type="number" name="reviews_count" value="<?= htmlspecialchars($_POST['reviews_count'] ?? '0') ?>" min="0">
          </div>
        </div>
        <div class="form-group">
          <label>Sort Order</label>
          <input type="number" name="sort_order" value="<?= htmlspecialchars($_POST['sort_order'] ?? '0') ?>" min="0">
          <div class="form-hint">Lower number = shown first</div>
        </div>
      </div>
    </div>

    <!-- RIGHT COLUMN -->
    <div>
      <div class="form-card">
        <div class="form-card-title">Product Images</div>
        <div class="form-group">
          <label>Upload Images</label>
          <div class="img-upload-area" id="img-upload-area" onclick="document.getElementById('img-input').click()">
            <div class="img-upload-icon">📸</div>
            <div class="img-upload-text">Click to upload images</div>
            <div class="img-upload-hint">JPG, PNG, WebP up to 10MB each</div>
          </div>
          <input type="file" id="img-input" name="images[]" multiple accept="image/*" style="display:none" onchange="previewImages(this)">
          <div id="img-previews" class="img-previews"></div>
        </div>
      </div>

      <div class="form-card">
        <div class="form-card-title">Visibility</div>
        <div class="form-check-group">
          <label class="form-check">
            <input type="checkbox" name="is_active" value="1" <?= !isset($_POST['is_active']) || $_POST['is_active'] ? 'checked' : '' ?>>
            <span class="form-check-label">
              <span class="fck-title">Active</span>
              <span class="fck-desc">Show this product on the store</span>
            </span>
          </label>
          <label class="form-check">
            <input type="checkbox" name="is_featured" value="1" <?= !empty($_POST['is_featured']) ? 'checked' : '' ?>>
            <span class="form-check-label">
              <span class="fck-title">⭐ Featured</span>
              <span class="fck-desc">Show on homepage featured section</span>
            </span>
          </label>
        </div>
      </div>

      <div style="display:flex;gap:.75rem;margin-top:1rem">
        <button type="submit" class="btn btn-red" style="flex:1;justify-content:center">💾 Save Product</button>
        <a href="<?= BASE_URL ?>/admin/products/" class="btn btn-ghost">Cancel</a>
      </div>
    </div>
  </div>
</form>

<script>
function previewImages(input) {
  const container = document.getElementById('img-previews');
  container.innerHTML = '';
  Array.from(input.files).forEach((file, i) => {
    const reader = new FileReader();
    reader.onload = (e) => {
      const div = document.createElement('div');
      div.className = 'img-preview-item';
      div.innerHTML = `
        <img src="${e.target.result}" alt="Preview">
        <div class="img-preview-label">${i===0?'<span class="cover-badge">Cover</span>':''}</div>
      `;
      container.appendChild(div);
    };
    reader.readAsDataURL(file);
  });
}
</script>

<?php require_once __DIR__ . '/../_layout_end.php'; ?>
