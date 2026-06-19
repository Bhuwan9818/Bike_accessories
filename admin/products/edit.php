<?php
$pageTitle = 'Edit Product';
$activeNav = 'products';
$adminDepth = 2;
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../_layout.php';

$id = (int)($_GET['id'] ?? 0);
$product = getProductById($id);
if (!$product) {
    echo '<div class="alert alert-danger">❌ Product not found.</div>';
    require_once __DIR__ . '/../_layout_end.php';
    exit;
}

$categories = getAllCategories(false);
$images     = getProductImages($id);
$errors     = [];

// Handle image delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_img_id'])) {
    deleteProductImage((int)$_POST['delete_img_id']);
    header('Location: ' . BASE_URL . '/admin/products/edit.php?id=' . $id . '&msg=img_deleted');
    exit;
}

// Handle set cover
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['set_cover_img_id'])) {
    setCoverImage($id, (int)$_POST['set_cover_img_id']);
    header('Location: ' . BASE_URL . '/admin/products/edit.php?id=' . $id . '&msg=cover_set');
    exit;
}

// Handle main form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_product'])) {
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
        updateProduct($id, $data);

        // Handle new image uploads
        if (!empty($_FILES['images']['name'][0])) {
            $existingCount = count(getProductImages($id));
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
                    addProductImage($id, $url, $existingCount === 0 && $i === 0, $existingCount + $i);
                } catch (Exception $e) {
                    $errors[] = 'Image upload: ' . $e->getMessage();
                }
            }
        }

        if (!$errors) {
            header('Location: ' . BASE_URL . '/admin/products/edit.php?id=' . $id . '&msg=saved');
            exit;
        }
    }

    // Re-fetch product data for display
    $product = array_merge($product, $_POST);
}

$images  = getProductImages($id);
$msg     = $_GET['msg'] ?? '';
?>

<?php if ($msg === 'saved'): ?>
<div class="alert alert-success">✅ Product updated successfully.</div>
<?php elseif ($msg === 'img_deleted'): ?>
<div class="alert alert-success">✅ Image removed.</div>
<?php elseif ($msg === 'cover_set'): ?>
<div class="alert alert-success">✅ Cover image updated.</div>
<?php endif; ?>

<?php if ($errors): ?>
<div class="alert alert-danger">
  <?php foreach ($errors as $e): ?><div>❌ <?= htmlspecialchars($e) ?></div><?php endforeach; ?>
</div>
<?php endif; ?>

<!-- Breadcrumb -->
<div style="margin-bottom:1.25rem;font-size:13px;color:var(--text-muted)">
  <a href="<?= BASE_URL ?>/admin/products/" style="color:var(--text-muted)">Products</a>
  <span style="margin:0 .5rem">›</span>
  <span style="color:var(--chrome-light)"><?= htmlspecialchars($product['name']) ?></span>
</div>

<form method="POST" enctype="multipart/form-data" class="admin-form">
  <input type="hidden" name="save_product" value="1">
  <div class="form-grid-2">

    <!-- LEFT -->
    <div>
      <div class="form-card">
        <div class="form-card-title">Basic Information</div>

        <div class="form-group">
          <label>Product Name <span class="req">*</span></label>
          <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" required>
        </div>

        <div class="form-group">
          <label>Category <span class="req">*</span></label>
          <select name="category_id" required>
            <option value="">— Select Category —</option>
            <?php foreach ($categories as $cat): ?>
            <option value="<?= $cat['id'] ?>" <?= $product['category_id'] == $cat['id'] ? 'selected' : '' ?>>
              <?= $cat['icon'] ?> <?= htmlspecialchars($cat['name']) ?>
            </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-group">
          <label>Description</label>
          <textarea name="description" rows="4"><?= htmlspecialchars($product['description'] ?? '') ?></textarea>
        </div>

        <div class="form-group">
          <label>Compatible Models</label>
          <input type="text" name="compat" value="<?= htmlspecialchars($product['compat'] ?? '') ?>" placeholder="e.g. Hero Splendor+, HF Deluxe 2018–2024">
        </div>
      </div>

      <div class="form-card">
        <div class="form-card-title">Pricing</div>
        <div class="form-row-2">
          <div class="form-group">
            <label>Selling Price (₹) <span class="req">*</span></label>
            <input type="number" name="price" value="<?= htmlspecialchars($product['price']) ?>" min="1" step="0.01" required>
          </div>
          <div class="form-group">
            <label>Original Price (₹)</label>
            <input type="number" name="old_price" value="<?= htmlspecialchars($product['old_price'] ?? '') ?>" min="0" step="0.01" placeholder="Leave blank if no discount">
          </div>
        </div>
      </div>

      <div class="form-card">
        <div class="form-card-title">Reviews & Sorting</div>
        <div class="form-row-2">
          <div class="form-group">
            <label>Rating (1–5)</label>
            <input type="number" name="rating" value="<?= htmlspecialchars($product['rating']) ?>" min="1" max="5" step="0.1">
          </div>
          <div class="form-group">
            <label>Reviews Count</label>
            <input type="number" name="reviews_count" value="<?= htmlspecialchars($product['reviews_count']) ?>" min="0">
          </div>
        </div>
        <div class="form-group">
          <label>Sort Order</label>
          <input type="number" name="sort_order" value="<?= htmlspecialchars($product['sort_order'] ?? 0) ?>" min="0">
          <div class="form-hint">Lower number = shown first</div>
        </div>
      </div>
    </div>

    <!-- RIGHT -->
    <div>
      <!-- Current Images -->
      <div class="form-card">
        <div class="form-card-title">
          Product Images
          <span style="font-size:12px;font-weight:400;color:var(--text-muted)">(<?= count($images) ?> uploaded)</span>
        </div>

        <?php if ($images): ?>
        <div class="current-images-grid">
          <?php foreach ($images as $img): ?>
          <div class="cur-img-wrap <?= $img['is_cover'] ? 'is-cover' : '' ?>">
            <img src="<?= htmlspecialchars($img['image_path']) ?>" alt="Product image" onerror="this.src='<?= BASE_URL ?>/assets/images/placeholder.svg'">
            <?php if ($img['is_cover']): ?>
            <div class="cover-badge-overlay">Cover</div>
            <?php endif; ?>
            <div class="cur-img-actions">
              <?php if (!$img['is_cover']): ?>
              <form method="POST" style="display:inline">
                <input type="hidden" name="set_cover_img_id" value="<?= $img['id'] ?>">
                <button type="submit" class="img-action-btn cover-btn" title="Set as cover">⭐</button>
              </form>
              <?php endif; ?>
              <form method="POST" style="display:inline" onsubmit="return confirm('Delete this image?')">
                <input type="hidden" name="delete_img_id" value="<?= $img['id'] ?>">
                <button type="submit" class="img-action-btn del-btn" title="Delete">🗑</button>
              </form>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div style="text-align:center;padding:1.5rem;color:var(--text-muted);font-size:13px">No images uploaded yet.</div>
        <?php endif; ?>

        <div class="form-group" style="margin-top:1rem">
          <label>Add More Images</label>
          <div class="img-upload-area" onclick="document.getElementById('img-input').click()">
            <div class="img-upload-icon">📸</div>
            <div class="img-upload-text">Click to upload more images</div>
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
            <input type="checkbox" name="is_active" value="1" <?= $product['is_active'] ? 'checked' : '' ?>>
            <span class="form-check-label">
              <span class="fck-title">Active</span>
              <span class="fck-desc">Show this product on the store</span>
            </span>
          </label>
          <label class="form-check">
            <input type="checkbox" name="is_featured" value="1" <?= $product['is_featured'] ? 'checked' : '' ?>>
            <span class="form-check-label">
              <span class="fck-title">⭐ Featured</span>
              <span class="fck-desc">Show on homepage featured section</span>
            </span>
          </label>
        </div>
      </div>

      <div style="display:flex;gap:.75rem;margin-top:1rem">
        <button type="submit" class="btn btn-red" style="flex:1;justify-content:center">💾 Update Product</button>
        <a href="<?= BASE_URL ?>/pages/products/<?= $product['slug'] ?>.php" class="btn btn-ghost" target="_blank">👁 View</a>
        <a href="<?= BASE_URL ?>/admin/products/" class="btn btn-ghost">← Back</a>
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
      div.innerHTML = `<img src="${e.target.result}" alt="Preview"><div class="img-preview-label">${file.name}</div>`;
      container.appendChild(div);
    };
    reader.readAsDataURL(file);
  });
}
</script>

<?php require_once __DIR__ . '/../_layout_end.php'; ?>
