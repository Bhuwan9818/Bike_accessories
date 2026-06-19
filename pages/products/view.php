<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/functions.php';

$slug    = $_GET['slug'] ?? basename($_SERVER['PHP_SELF'], '.php');
$product = getProductBySlug($slug);

if (!$product) {
    http_response_code(404);
    $depth      = 2;
    $pageTitle  = 'Product Not Found';
    $categories = getAllCategories();
    include __DIR__ . '/../../includes/header.php';
    echo '<div class="container" style="padding:5rem 0;text-align:center"><h1 style="color:var(--red)">Product Not Found</h1><a href="../../index.php" class="btn btn-red" style="margin-top:1.5rem">← Back to Home</a></div>';
    include __DIR__ . '/../../includes/footer.php';
    exit;
}

$images  = $product['images'];
$cat     = getCategoryById((int)$product['category_id']);
$pct     = $product['old_price'] > 0 ? round(($product['old_price'] - $product['price']) / $product['old_price'] * 100) : 0;
$stars   = str_repeat('★', round($product['rating'])) . str_repeat('☆', 5 - round($product['rating']));

$related = getProducts(['category_id' => $product['category_id'], 'limit' => 5]);
$related = array_filter($related, fn($r) => $r['id'] != $product['id']);
$related = array_slice(array_values($related), 0, 4);

$imgUrls = array_map(fn($img) => htmlspecialchars($img['image_path']), $images);
$firstImg = !empty($imgUrls) ? $imgUrls[0] : '../../assets/images/placeholder.svg';

$depth      = 2;
$pageTitle  = $product['name'];
$activePage = '';
$categories = getAllCategories();

include __DIR__ . '/../../includes/header.php';
?>

<section class="pd-wrap">
<div class="container">

  <!-- Breadcrumb -->
  <div class="breadcrumb reveal">
    <a href="../../index.php">Home</a>
    <span class="sep">›</span>
    <?php if ($cat): ?>
    <a href="../categories/view.php?slug=<?= $cat['slug'] ?>"><?= htmlspecialchars($cat['name']) ?></a>
    <span class="sep">›</span>
    <?php endif; ?>
    <span style="color:var(--text)"><?= htmlspecialchars($product['name']) ?></span>
  </div>

  <div class="pd-grid">

    <!-- GALLERY -->
    <div class="gallery reveal-l">
      <div class="main-wrap">
        <?php if (!empty($imgUrls)): ?>
        <img id="main-img" src="<?= $firstImg ?>" alt="<?= htmlspecialchars($product['name']) ?>">
        <?php if (count($imgUrls) > 1): ?>
        <button class="g-arr g-prev" onclick="prevImg()">&#8249;</button>
        <button class="g-arr g-next" onclick="nextImg()">&#8250;</button>
        <?php endif; ?>
        <div class="img-ctr" id="img-ctr">1 / <?= count($imgUrls) ?></div>
        <?php else: ?>
        <div class="img-placeholder" style="width:100%;height:100%;font-size:80px;display:flex;align-items:center;justify-content:center">
          <?= $cat ? $cat['icon'] : '🔧' ?>
        </div>
        <?php endif; ?>
      </div>

      <?php if (count($imgUrls) > 1): ?>
      <div class="thumbs">
        <?php foreach ($imgUrls as $i => $imgUrl): ?>
        <div class="thumb <?= $i===0 ? 'active' : '' ?>" onclick="setImg(<?= $i ?>)">
          <img src="<?= $imgUrl ?>" loading="lazy" alt="View <?= $i+1 ?>" onerror="this.parentNode.style.display='none'">
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>

    <!-- PRODUCT INFO -->
    <div class="pd-info reveal-r">
      <div class="pd-top">
        <span class="pd-brand">Bike Accessories India · OEM Certified</span>
        <span class="pd-stock">✔ In Stock</span>
        <?php if ($product['is_featured']): ?>
        <span style="background:var(--red);color:#fff;font-size:10px;font-weight:800;letter-spacing:1.5px;text-transform:uppercase;padding:4px 12px;border-radius:20px">⭐ FEATURED</span>
        <?php endif; ?>
      </div>

      <h1 class="pd-title"><?= htmlspecialchars($product['name']) ?></h1>

      <?php if ($cat): ?>
      <div class="pd-cat">
        <a href="../categories/view.php?slug=<?= $cat['slug'] ?>"><?= $cat['icon'] ?> <?= htmlspecialchars($cat['name']) ?></a>
      </div>
      <?php endif; ?>

      <div class="pd-rating">
        <span class="pd-stars"><?= $stars ?></span>
        <span class="pd-rating-txt"><?= $product['rating'] ?> (<?= $product['reviews_count'] ?> reviews)</span>
      </div>

      <div class="pd-price-wrap">
        <div class="pd-price">₹<?= number_format($product['price'], 0) ?></div>
        <?php if ($product['old_price'] > 0): ?>
        <div class="pd-old-price">₹<?= number_format($product['old_price'], 0) ?></div>
        <?php if ($pct > 0): ?><div class="pd-discount">-<?= $pct ?>% OFF</div><?php endif; ?>
        <?php endif; ?>
      </div>

      <?php if ($product['compat']): ?>
      <div class="pd-compat-box">
        <div class="pd-compat-title">⚙ Compatible With</div>
        <div class="pd-compat-list"><?= htmlspecialchars($product['compat']) ?></div>
      </div>
      <?php endif; ?>

      <?php if ($product['description']): ?>
      <div class="pd-desc"><?= nl2br(htmlspecialchars($product['description'])) ?></div>
      <?php endif; ?>

      <!-- CTA Buttons -->
      <div class="pd-ctas">
        <button class="btn btn-red btn-lg"
          onclick="addToCart('<?= $product['id'] ?>','<?= addslashes($product['name']) ?>',<?= $product['price'] ?>,'<?= $firstImg ?>',this)">
          🛒 Add to Cart
        </button>
        <button class="btn btn-ghost btn-lg"
          onclick="toggleWish('<?= $product['id'] ?>','<?= addslashes($product['name']) ?>',<?= $product['price'] ?>,'<?= $firstImg ?>',this)">
          ♡ Wishlist
        </button>
      </div>

      <!-- Guarantees -->
      <div class="pd-guarantees">
        <div class="pd-guarantee-item"><span>🚚</span> Free shipping above ₹<?= setting('free_shipping_above','999') ?></div>
        <div class="pd-guarantee-item"><span>✅</span> OEM Certified Part</div>
        <div class="pd-guarantee-item"><span>🔄</span> 30-day easy returns</div>
        <div class="pd-guarantee-item"><span>🔒</span> Secure payment</div>
      </div>

      <div style="margin-top:1.5rem">
        <a href="../../admin/products/edit.php?id=<?= $product['id'] ?>" class="btn btn-ghost btn-sm">✏️ Edit in Admin</a>
      </div>
    </div>
  </div>

  <!-- Related Products -->
  <?php if ($related): ?>
  <div style="margin-top:4rem">
    <div style="margin-bottom:1.5rem">
      <div class="sh-eye">You Might Also Like</div>
      <h2 class="sh-title" style="font-size:clamp(24px,4vw,36px)">RELATED <em>PRODUCTS</em></h2>
    </div>
    <div class="products-grid">
      <?php foreach ($related as $i => $p):
        $rpct   = $p['old_price'] > 0 ? round(($p['old_price'] - $p['price']) / $p['old_price'] * 100) : 0;
        $rimgUrl = !empty($p['first_image']) ? htmlspecialchars($p['first_image']) : '../../assets/images/placeholder.svg';
        $rstars  = str_repeat('★', round($p['rating'])) . str_repeat('☆', 5 - round($p['rating']));
      ?>
      <div class="pcard reveal" style="transition-delay:<?= $i * 0.06 ?>s">
        <a href="view.php?slug=<?= $p['slug'] ?>">
          <div class="pcard-img">
            <img src="<?= $rimgUrl ?>" alt="<?= htmlspecialchars($p['name']) ?>" loading="lazy" onerror="this.src='../../assets/images/placeholder.svg'">
            <?php if ($rpct > 0): ?><div class="pcard-badge">🔥 -<?= $rpct ?>%</div><?php endif; ?>
          </div>
        </a>
        <div class="pcard-body">
          <div class="pcard-name">
            <a href="view.php?slug=<?= $p['slug'] ?>" style="color:inherit"><?= htmlspecialchars($p['name']) ?></a>
          </div>
          <div class="pcard-stars"><?= $rstars ?></div>
          <div class="pcard-footer">
            <div>
              <?php if ($p['old_price'] > 0): ?><div class="pcard-price-old">₹<?= number_format($p['old_price'], 0) ?></div><?php endif; ?>
              <div class="pcard-price">₹<?= number_format($p['price'], 0) ?></div>
            </div>
            <button class="pcard-add" onclick="addToCart('<?= $p['id'] ?>','<?= addslashes($p['name']) ?>',<?= $p['price'] ?>,'<?= $rimgUrl ?>',this)" title="Add to Cart">+</button>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>
</div>
</section>

<script>
const imgs = <?= json_encode($imgUrls) ?>;
let cur = 0;
function setImg(i) {
  cur = i;
  document.getElementById('main-img').src = imgs[i];
  document.querySelectorAll('.thumb').forEach((t,j) => t.classList.toggle('active', i===j));
  const ctr = document.getElementById('img-ctr');
  if (ctr) ctr.textContent = (i+1) + ' / ' + imgs.length;
}
function nextImg() { setImg((cur + 1) % imgs.length); }
function prevImg() { setImg((cur - 1 + imgs.length) % imgs.length); }
</script>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
