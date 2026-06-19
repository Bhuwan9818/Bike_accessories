<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

$depth      = 0;
$pageTitle  = 'Home';
$activePage = 'home';
$categories = getAllCategories();
$featured   = getProducts(['featured' => true, 'limit' => 8]);
if (count($featured) < 8) {
    $featured = getProducts(['limit' => 8]);
}
$stats = getDashboardStats();

// Hero image: first product image available
$heroImg = '';
foreach ($featured as $p) {
    if (!empty($p['first_image'])) { $heroImg = $p['first_image']; break; }
}

include __DIR__ . '/includes/header.php';
?>

<!-- ══ HERO ══ -->
<section class="hero">
  <div class="hero-bg"></div>
  <div class="hero-grid"></div>
  <div class="hero-glow"></div>
  <div class="container">
    <div class="hero-inner">
      <div class="reveal-l">
        <div class="hero-eye">India's #1 Bike Parts Marketplace</div>
        <h1 class="hero-title">
          <span class="ht-o">GENUINE</span>
          <span class="ht-r">SPARE</span>
          <span class="ht-w">PARTS</span>
        </h1>
        <p class="hero-desc">Premium spare parts across <?= count($categories) ?> categories — Petrol Tanks, Fiber Kits, Body Parts, Seats, Silencers, Headlights, Shockers &amp; Speedometers. OEM certified, delivered fast.</p>
        <div class="hero-ctas">
          <a href="#categories" class="btn btn-red btn-lg">Shop by Category →</a>
        </div>
        <div class="hero-stats">
          <div>
            <div class="hs-num"><?= $stats['categories'] ?><sup>+</sup></div>
            <div class="hs-label">Categories</div>
          </div>
          <div class="hs-divider"></div>
          <div>
            <div class="hs-num"><?= $stats['products'] ?><sup>+</sup></div>
            <div class="hs-label">Products</div>
          </div>
          <div class="hs-divider"></div>
          <div>
            <div class="hs-num">800<sup>+</sup></div>
            <div class="hs-label">Bike Models</div>
          </div>
        </div>
      </div>

      <div class="hero-visual reveal-r">
        <div class="hero-orbit">
          <div class="h-ring"></div>
          <div class="h-ring2"></div>
          <div class="h-glow-c"></div>
          <div class="hero-main-img">
            <?php if ($heroImg): ?>
              <img src="<?= htmlspecialchars($heroImg) ?>" alt="Featured Part">
            <?php else: ?>
              <div class="hero-main-img-placeholder">🏍️</div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- MARQUEE -->
<div class="marquee-wrap">
  <div class="marquee-track">
    <?php
    $items = ['Free Shipping Above ₹' . setting('free_shipping_above','999'), 'OEM Certified Parts', 'Hero · Bajaj · Honda · TVS · Royal Enfield', 'Same Day Dispatch', '12-Month Warranty', 'COD Available', 'Pan-India Delivery'];
    foreach (array_merge($items,$items) as $item):
    ?><span class="mi"><span class="mdot"></span><?= $item ?></span><?php endforeach; ?>
  </div>
</div>

<!-- ══ CATEGORIES ══ -->
<section class="section" id="categories" style="background:var(--dark)">
  <div class="container">
    <div class="reveal" style="margin-bottom:2rem">
      <div class="sh-eye">Browse All Categories</div>
      <h2 class="sh-title">SHOP BY <em>CATEGORY</em></h2>
    </div>
    <?php if ($categories): ?>
    <div class="cats-grid">
      <?php foreach ($categories as $i => $cat): ?>
      <a href="pages/categories/view.php?slug=<?= $cat['slug'] ?>"
         class="cat-card <?= $i === 0 ? 'featured' : '' ?> reveal"
         style="transition-delay:<?= $i * 0.05 ?>s">
        <div class="cat-icon"><?= $cat['icon'] ?></div>
        <div class="cat-name"><?= htmlspecialchars($cat['name']) ?></div>
        <div class="cat-count"><?= $cat['product_count'] ?>+ products</div>
        <span class="cat-arr">↗</span>
      </a>
      <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div style="text-align:center;padding:3rem;color:var(--text-muted)">
      No categories yet. <a href="admin/" style="color:var(--red)">Add from Admin Panel →</a>
    </div>
    <?php endif; ?>
  </div>
</section>

<!-- ══ FEATURED PRODUCTS ══ -->
<section class="section" style="background:var(--black)">
  <div class="container">
    <div style="display:flex;align-items:flex-end;justify-content:space-between;gap:1rem;flex-wrap:wrap;margin-bottom:2rem" class="reveal">
      <div>
        <div class="sh-eye">Trending This Week</div>
        <h2 class="sh-title">FEATURED <em>PRODUCTS</em></h2>
      </div>
      <a href="#categories" class="btn btn-ghost btn-sm">View All →</a>
    </div>
    <?php if ($featured): ?>
    <div class="home-pg">
      <?php foreach ($featured as $i => $p):
        $pct     = $p['old_price'] > 0 ? round(($p['old_price'] - $p['price']) / $p['old_price'] * 100) : 0;
        $imgUrl  = !empty($p['first_image']) ? htmlspecialchars($p['first_image']) : 'assets/images/placeholder.svg';
        $stars   = str_repeat('★', round($p['rating'])) . str_repeat('☆', 5 - round($p['rating']));
      ?>
      <div class="pcard reveal" style="transition-delay:<?= $i * 0.05 ?>s">
        <a href="pages/products/view.php?slug=<?= $p['slug'] ?>">
          <div class="pcard-img">
            <img src="<?= $imgUrl ?>" alt="<?= htmlspecialchars($p['name']) ?>" loading="lazy" onerror="this.src='assets/images/placeholder.svg'">
            <?php if ($pct > 0): ?>
            <div class="pcard-badge">🔥 -<?= $pct ?>%</div>
            <?php endif; ?>
            <div class="pcard-wish"
              onclick="event.preventDefault();event.stopPropagation();toggleWish('<?= $p['id'] ?>','<?= addslashes($p['name']) ?>',<?= $p['price'] ?>,'<?= $imgUrl ?>',this)"
            >♡</div>
          </div>
        </a>
        <div class="pcard-body">
          <div class="pcard-cat"><?= htmlspecialchars($p['category_name']) ?></div>
          <div class="pcard-name">
            <a href="pages/products/view.php?slug=<?= $p['slug'] ?>" style="color:inherit"><?= htmlspecialchars($p['name']) ?></a>
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
              <?php if ($pct > 0): ?>
              <div class="pcard-save">Save <?= $pct ?>%</div>
              <?php endif; ?>
            </div>
            <button class="pcard-add"
              onclick="addToCart('<?= $p['id'] ?>','<?= addslashes($p['name']) ?>',<?= $p['price'] ?>,'<?= $imgUrl ?>',this)"
              title="Add to Cart">+</button>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div style="text-align:center;padding:3rem;color:var(--text-muted)">
      No products yet. <a href="admin/" style="color:var(--red)">Add from Admin Panel →</a>
    </div>
    <?php endif; ?>
  </div>
</section>

<!-- ══ WHY US ══ -->
<section class="section" style="background:var(--dark)" id="why-us">
  <div class="container">
    <div class="reveal" style="margin-bottom:2rem">
      <div class="sh-eye">Why Riders Choose Us</div>
      <h2 class="sh-title">THE <em>BAI</em> ADVANTAGE</h2>
    </div>
    <div class="why-grid">
      <div class="why-item reveal" style="transition-delay:.04s">
        <span class="why-icon">🚚</span>
        <div class="why-title">Pan-India Delivery</div>
        <p class="why-desc">Free shipping above ₹<?= setting('free_shipping_above','999') ?>. Same-day dispatch before 2 PM. Delivered in 2–5 working days across all 28 states.</p>
      </div>
      <div class="why-item reveal" style="transition-delay:.08s">
        <span class="why-icon">✅</span>
        <div class="why-title">OEM Certified Parts</div>
        <p class="why-desc">Every part sourced directly from authorised channels with brand warranty and genuine authenticity seal.</p>
      </div>
      <div class="why-item reveal" style="transition-delay:.12s">
        <span class="why-icon">🔄</span>
        <div class="why-title">30-Day Returns</div>
        <p class="why-desc">Wrong fitment? Return unopened parts within 30 days for a full refund or hassle-free exchange.</p>
      </div>
      <div class="why-item reveal" style="transition-delay:.16s">
        <span class="why-icon">🔒</span>
        <div class="why-title">Secure Payments</div>
        <p class="why-desc">UPI, Net Banking, Cards, EMI &amp; COD. All transactions encrypted with 256-bit SSL.</p>
      </div>
    </div>
  </div>
</section>

<!-- ══ NEWSLETTER ══ -->
<section class="nl-section">
  <div class="nl-inner">
    <span class="nl-icon">📬</span>
    <h2 class="nl-title">EXCLUSIVE DEALS</h2>
    <p class="nl-desc">Early access to flash sales, new arrivals, and model-specific guides — straight to your inbox.</p>
    <div class="nl-form">
      <input type="email" class="nl-input" placeholder="Enter your email address" id="nl-email">
      <button class="nl-btn" onclick="
        const e=document.getElementById('nl-email');
        if(e.value){this.textContent='Subscribed ✓';this.style.background='#1a6b35';this.style.color='#fff';e.disabled=true;}
      ">Subscribe</button>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
