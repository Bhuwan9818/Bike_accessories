<?php
$pageTitle = 'Site Settings';
$activeNav = 'settings';
$adminDepth = 1;
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/_layout.php';

$success = '';
$errors  = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fields = ['site_name','site_tagline','contact_phone','contact_email','whatsapp','free_shipping_above'];
    foreach ($fields as $key) {
        $val = trim($_POST[$key] ?? '');
        db()->prepare("INSERT INTO site_settings (setting_key, setting_value) VALUES (?,?) ON DUPLICATE KEY UPDATE setting_value=?")->execute([$key, $val, $val]);
    }

    // Logo upload
    if (!empty($_FILES['logo']['name']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['image/jpeg','image/png','image/webp','image/svg+xml','image/gif'];
        if (!in_array($_FILES['logo']['type'], $allowed)) {
            $errors[] = 'Invalid logo file type.';
        } else {
            $logoDir = __DIR__ . '/../uploads/';
            if (!is_dir($logoDir)) mkdir($logoDir, 0755, true);
            $ext  = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));
            $name = 'logo.' . $ext;
            if (move_uploaded_file($_FILES['logo']['tmp_name'], $logoDir . $name)) {
                $logoUrl = BASE_URL . '/uploads/' . $name;
                db()->prepare("INSERT INTO site_settings (setting_key, setting_value) VALUES ('logo_path',?) ON DUPLICATE KEY UPDATE setting_value=?")->execute([$logoUrl, $logoUrl]);
            } else {
                $errors[] = 'Logo upload failed.';
            }
        }
    }

    if (!$errors) {
        header('Location: ' . BASE_URL . '/admin/settings.php?msg=saved');
        exit;
    }
}

$msg = $_GET['msg'] ?? '';
?>

<?php if ($msg === 'saved'): ?>
<div class="alert alert-success">✅ Settings saved successfully.</div>
<?php endif; ?>
<?php if ($errors): ?>
<div class="alert alert-danger"><?php foreach($errors as $e): ?><div>❌ <?= htmlspecialchars($e) ?></div><?php endforeach; ?></div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data" class="admin-form">
  <div class="form-grid-2">
    <div>
      <div class="form-card">
        <div class="form-card-title">Store Identity</div>

        <div class="form-group">
          <label>Store Name</label>
          <input type="text" name="site_name" value="<?= htmlspecialchars(setting('site_name','Bike Accessories India')) ?>">
        </div>
        <div class="form-group">
          <label>Tagline</label>
          <input type="text" name="site_tagline" value="<?= htmlspecialchars(setting('site_tagline',"India's #1 Bike Parts Marketplace")) ?>">
        </div>
        <div class="form-group">
          <label>Store Logo</label>
          <?php $logo = setting('logo_path'); if ($logo): ?>
          <div style="margin-bottom:.75rem"><img src="<?= htmlspecialchars($logo) ?>" style="max-height:60px;max-width:200px;border-radius:6px;border:1px solid var(--border);padding:4px;background:#fff"></div>
          <?php endif; ?>
          <input type="file" name="logo" accept="image/*">
          <div class="form-hint">PNG, JPG, SVG, WebP. Recommended: 200×50px transparent PNG</div>
        </div>
      </div>

      <div class="form-card">
        <div class="form-card-title">Contact & Shipping</div>
        <div class="form-group">
          <label>Phone Number</label>
          <input type="text" name="contact_phone" value="<?= htmlspecialchars(setting('contact_phone','1800-123-BIKE')) ?>" placeholder="1800-123-BIKE">
        </div>
        <div class="form-group">
          <label>Email Address</label>
          <input type="email" name="contact_email" value="<?= htmlspecialchars(setting('contact_email','help@bikeaccessories.in')) ?>">
        </div>
        <div class="form-group">
          <label>WhatsApp Number</label>
          <input type="text" name="whatsapp" value="<?= htmlspecialchars(setting('whatsapp','+91-9999999999')) ?>" placeholder="+91-9999999999">
          <div class="form-hint">Include country code e.g. +919876543210</div>
        </div>
        <div class="form-group">
          <label>Free Shipping Above (₹)</label>
          <input type="number" name="free_shipping_above" value="<?= htmlspecialchars(setting('free_shipping_above','999')) ?>" min="0">
        </div>
      </div>
    </div>

    <div>
      <div class="form-card">
        <div class="form-card-title">Live Preview</div>
        <div style="background:var(--black);border:1px solid var(--border);border-radius:10px;padding:1.5rem">
          <div style="font-family:'Barlow Condensed',sans-serif;font-size:20px;font-weight:800;color:var(--chrome-light)" id="prev-name"><?= htmlspecialchars(setting('site_name','Bike Accessories India')) ?></div>
          <div style="font-size:13px;color:var(--text-muted);margin-top:.25rem" id="prev-tagline"><?= htmlspecialchars(setting('site_tagline',"India's #1 Bike Parts Marketplace")) ?></div>
          <div style="margin-top:1rem;padding-top:1rem;border-top:1px solid var(--border);font-size:13px;color:var(--text-muted)">
            📞 <span id="prev-phone"><?= htmlspecialchars(setting('contact_phone')) ?></span><br>
            ✉️ <span id="prev-email"><?= htmlspecialchars(setting('contact_email')) ?></span><br>
            🚚 Free shipping above ₹<span id="prev-ship"><?= htmlspecialchars(setting('free_shipping_above','999')) ?></span>
          </div>
        </div>
      </div>

      <div class="form-card">
        <div class="form-card-title">Database Info</div>
        <?php $stats = getDashboardStats(); ?>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem">
          <div style="background:var(--black);border:1px solid var(--border);border-radius:8px;padding:1rem;text-align:center">
            <div style="font-size:24px;font-weight:800;color:var(--red)"><?= $stats['products'] ?></div>
            <div style="font-size:12px;color:var(--text-muted)">Products</div>
          </div>
          <div style="background:var(--black);border:1px solid var(--border);border-radius:8px;padding:1rem;text-align:center">
            <div style="font-size:24px;font-weight:800;color:var(--red)"><?= $stats['categories'] ?></div>
            <div style="font-size:12px;color:var(--text-muted)">Categories</div>
          </div>
          <div style="background:var(--black);border:1px solid var(--border);border-radius:8px;padding:1rem;text-align:center">
            <div style="font-size:24px;font-weight:800;color:var(--red)"><?= $stats['featured'] ?></div>
            <div style="font-size:12px;color:var(--text-muted)">Featured</div>
          </div>
          <div style="background:var(--black);border:1px solid var(--border);border-radius:8px;padding:1rem;text-align:center">
            <div style="font-size:24px;font-weight:800;color:var(--red)"><?= $stats['images'] ?></div>
            <div style="font-size:12px;color:var(--text-muted)">Images</div>
          </div>
        </div>
      </div>

      <div style="margin-top:1rem">
        <button type="submit" class="btn btn-red" style="width:100%;justify-content:center">💾 Save Settings</button>
      </div>
    </div>
  </div>
</form>

<script>
['site_name','site_tagline','contact_phone','contact_email','free_shipping_above'].forEach(key => {
  const inp = document.querySelector(`[name="${key}"]`);
  const map = {site_name:'prev-name',site_tagline:'prev-tagline',contact_phone:'prev-phone',contact_email:'prev-email',free_shipping_above:'prev-ship'};
  if (inp && map[key]) inp.addEventListener('input', () => { document.getElementById(map[key]).textContent = inp.value; });
});
</script>

<?php require_once __DIR__ . '/_layout_end.php'; ?>
