<?php
// includes/footer.php
if (!isset($depth)) $depth = 0;
$root = str_repeat('../', $depth);
$categories = getAllCategories();
?>

<footer>
  <div class="container">
    <div class="footer-grid">
      <div>
        <div class="footer-brand">BIKE <span>ACCESSORIES</span> INDIA</div>
        <p class="footer-desc">India's trusted marketplace for genuine bike &amp; scooter spare parts. 50,000+ happy riders, 28 states served.</p>
        <div class="social-row">
          <span class="soc-btn">📱</span>
          <span class="soc-btn">📸</span>
          <span class="soc-btn">▶️</span>
          <span class="soc-btn">🔵</span>
        </div>
      </div>
      <div>
        <div class="footer-h">Categories</div>
        <ul class="footer-ul">
          <?php foreach ($categories as $cat): ?>
          <li><a href="<?= $root ?>pages/categories/view.php?slug=<?= $cat['slug'] ?>"><?= $cat['icon'] ?> <?= htmlspecialchars($cat['name']) ?></a></li>
          <?php endforeach; ?>
        </ul>
      </div>
      <div>
        <div class="footer-h">Support</div>
        <ul class="footer-ul">
          <li><a href="#">Track Order</a></li>
          <li><a href="#">Returns &amp; Refunds</a></li>
          <li><a href="#">Fitment Guide</a></li>
          <li><a href="#">Warranty Claims</a></li>
        </ul>
      </div>
      <div>
        <div class="footer-h">Contact</div>
        <ul class="footer-ul">
          <li><a href="tel:<?= setting('contact_phone') ?>">📞 <?= setting('contact_phone') ?></a></li>
          <li><a href="mailto:<?= setting('contact_email') ?>">✉️ <?= setting('contact_email') ?></a></li>
          <li>🕐 Mon–Sat, 9AM–7PM</li>
          <li><a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', setting('whatsapp')) ?>" target="_blank" style="color:var(--red);margin-top:6px;display:inline-block">WhatsApp →</a></li>
        </ul>
      </div>
    </div>
    <div class="footer-bottom">
      <span>© <?= date('Y') ?> Bike Accessories India. All rights reserved.</span>
      <div class="footer-legal">
        <a href="#">Privacy Policy</a>
        <a href="#">Terms of Service</a>
      </div>
    </div>
  </div>
</footer>

<script src="<?= $root ?>assets/js/app.js"></script>
</body>
</html>
