<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/functions.php';

include __DIR__ . '/../../includes/auth.php';

// Check admin session for conditional display of admin links
if (session_status() === PHP_SESSION_NONE) session_start();
$_isAdmin = !empty($_SESSION['admin_id']) && !empty($_SESSION['admin_user']);

// Get slug: from query string OR from filename
$slug = $_GET['slug'] ?? '';
if (!$slug) {
    $slug = basename($_SERVER['PHP_SELF'], '.php');
}

$cat = getCategoryBySlug($slug);
if (!$cat) {
    http_response_code(404);
    $depth      = 2;
    $pageTitle  = '404 – Category Not Found';
    $categories = getAllCategories();
    include __DIR__ . '/../../includes/header.php';
    echo '<div class="container" style="padding:5rem 0;text-align:center"><h1 style="color:var(--red)">Category Not Found</h1><a href="../../index.php" class="btn btn-red" style="margin-top:1.5rem">← Back to Home</a></div>';
    include __DIR__ . '/../../includes/footer.php';
    exit;
}

$sort     = $_GET['sort'] ?? 'default';
$products = getProducts(['category_id' => $cat['id'], 'sort' => $sort]);

$depth      = 2;
$pageTitle  = $cat['name'];
$activePage = 'category';
$categories = getAllCategories();

include __DIR__ . '/../../includes/header.php';
?>

<!-- CATEGORY HERO -->
<section class="cat-hero">
  <div class="container">
    <div class="breadcrumb">
      <a href="../../index.php">Home</a>
      <span class="sep">›</span>
      <span style="color:var(--text)"><?= htmlspecialchars($cat['name']) ?></span>
    </div>
    <div class="cat-hero-inner reveal-l">
      <div class="cat-hero-icon"><?= $cat['icon'] ?></div>
      <div>
        <div class="cat-hero-title"><em><?= strtoupper(htmlspecialchars($cat['name'])) ?></em></div>
        <div class="cat-hero-desc">
          <?= htmlspecialchars($cat['description'] ?: 'Genuine OEM and aftermarket ' . strtolower($cat['name']) . ' for all major bike and scooter models. Quality tested, warranty backed, fast delivery.') ?>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- PRODUCTS -->
<section style="padding:3rem 0 5rem;background:var(--black)">
  <div class="container">

    <!-- Sort bar -->
    <div class="sort-bar reveal">
      <div class="sort-left">
        Showing <strong><?= count($products) ?></strong> products in <strong><?= htmlspecialchars($cat['name']) ?></strong>
      </div>
      <form method="GET" style="display:flex;align-items:center;gap:8px">
        <input type="hidden" name="slug" value="<?= htmlspecialchars($slug) ?>">
        <select class="sort-select" name="sort" onchange="this.form.submit()">
          <option value="default"   <?= $sort==='default'    ? 'selected' : '' ?>>Default</option>
          <option value="price-asc" <?= $sort==='price-asc'  ? 'selected' : '' ?>>Price: Low to High</option>
          <option value="price-desc"<?= $sort==='price-desc' ? 'selected' : '' ?>>Price: High to Low</option>
          <option value="rating"    <?= $sort==='rating'     ? 'selected' : '' ?>>Top Rated</option>
          <option value="newest"    <?= $sort==='newest'     ? 'selected' : '' ?>>Newest First</option>
        </select>
      </form>
    </div>

    <!-- Grid -->
    <div class="products-grid">
      <?php if ($products): ?>
        <?php foreach ($products as $i => $p):
          $pct    = $p['old_price'] > 0 ? round(($p['old_price'] - $p['price']) / $p['old_price'] * 100) : 0;
          $imgUrl = !empty($p['first_image']) ? htmlspecialchars($p['first_image']) : '../../assets/images/placeholder.svg';
          $stars  = str_repeat('★', round($p['rating'])) . str_repeat('☆', 5 - round($p['rating']));
        ?>
        <div class="pcard reveal" style="transition-delay:<?= $i * 0.04 ?>s">
          <a href="../products/view.php?slug=<?= $p['slug'] ?>">
            <div class="pcard-img">
              <img src="<?= $imgUrl ?>" alt="<?= htmlspecialchars($p['name']) ?>" loading="lazy" onerror="this.src='../../assets/images/placeholder.svg'">
              <?php if ($pct > 0): ?>
              <div class="pcard-badge">🔥 -<?= $pct ?>%</div>
              <?php endif; ?>
              <div class="pcard-wish"
                onclick="event.preventDefault();event.stopPropagation();toggleWish('<?= $p['id'] ?>','<?= addslashes($p['name']) ?>',<?= $p['price'] ?>,'<?= $imgUrl ?>',this)"
              >♡</div>
            </div>
          </a>
          <div class="pcard-body">
            <div class="pcard-name">
              <a href="../products/view.php?slug=<?= $p['slug'] ?>" style="color:inherit"><?= htmlspecialchars($p['name']) ?></a>
            </div>
            <?php if ($p['compat']): ?>
            <div class="pcard-compat">⚙ <?= htmlspecialchars($p['compat']) ?></div>
            <?php endif; ?>
            <div class="pcard-stars"><?= $stars ?> <span style="color:var(--text-muted);font-size:11px">(<?= $p['reviews_count'] ?>)</span></div>
            <div class="pcard-footer">
              <div>
                <?php if ($p['old_price'] > 0): ?>
                <div class="pcard-price-old">₹<?= number_format($p['old_price'], 0) ?></div>
                <?php endif; ?>
                <div class="pcard-price">₹<?= number_format($p['price'], 0) ?></div>
                <?php if ($pct > 0): ?><div class="pcard-save">Save <?= $pct ?>%</div><?php endif; ?>
              </div>
              <button class="pcard-add"
                onclick="addToCart('<?= $p['id'] ?>','<?= addslashes($p['name']) ?>',<?= $p['price'] ?>,'<?= $imgUrl ?>',this)"
                title="Add to Cart">+</button>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="empty-box">
          <span class="empty-icon">📦</span>
          <h3 style="font-family:'Barlow Condensed',sans-serif;font-size:28px;font-weight:800;color:var(--chrome-light);margin-bottom:.75rem">No Products Yet</h3>
          <p style="margin-bottom:1.5rem">Be the first to add a product in this category.</p>
          <a href="../../admin/products/add.php?category=<?= $cat['id'] ?>" class="btn btn-red">+ Add Product</a>
        </div>
      <?php endif; ?>
    </div>

    <?php if ($_isAdmin): ?>
    <div class="add-banner reveal" onclick="location.href='../../admin/products/add.php?category=<?= $cat['id'] ?>'">
      <div style="font-size:36px;margin-bottom:.5rem">➕</div>
      <div style="font-family:'Barlow Condensed',sans-serif;font-size:22px;font-weight:800;color:var(--chrome-light);margin-bottom:.4rem">
        ADD A PRODUCT TO THIS CATEGORY
      </div>
      <div style="font-size:13px;color:var(--text-muted)">Click to add a new <?= htmlspecialchars($cat['name']) ?> via the Admin Panel</div>
    </div>
    <?php endif; ?>
  </div>
</section>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
