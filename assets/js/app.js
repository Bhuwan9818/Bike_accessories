/* app.js — Bike Accessories India */
'use strict';

/* ══════════════════════════════════
   CART
══════════════════════════════════ */
function getCart() {
  try { return JSON.parse(localStorage.getItem('bai_cart') || '[]'); } catch(e) { return []; }
}
function saveCart(c) { localStorage.setItem('bai_cart', JSON.stringify(c)); }

function updateCartUI() {
  const c = getCart();
  const count = c.reduce((a,i) => a + i.qty, 0);
  const total = c.reduce((a,i) => a + i.price * i.qty, 0);
  document.querySelectorAll('#cart-count').forEach(el => el.textContent = count);
  const ti = document.getElementById('cart-total');
  if (ti) ti.textContent = '₹' + total.toLocaleString('en-IN');
  const ci = document.getElementById('cart-items');
  if (!ci) return;
  if (!c.length) {
    ci.innerHTML = `<div class="cart-empty"><span class="cart-empty-icon">🛒</span>Your cart is empty</div>`;
    return;
  }
  ci.innerHTML = c.map(item => `
    <div class="cart-row">
      <img src="${item.img}" alt="${item.name}" onerror="this.src='${window.BAI_ROOT||''}assets/images/placeholder.svg'">
      <div class="cart-row-info">
        <div class="cart-row-name">${item.name}</div>
        <div class="cart-row-price">₹${item.price.toLocaleString('en-IN')}</div>
      </div>
      <div class="cart-row-controls">
        <button class="qty-ctrl-btn" onclick="changeCartQty('${item.id}',-1)">−</button>
        <span style="min-width:20px;text-align:center;font-weight:700">${item.qty}</span>
        <button class="qty-ctrl-btn" onclick="changeCartQty('${item.id}',1)">+</button>
        <button class="cart-del-btn" onclick="removeFromCart('${item.id}')">✕</button>
      </div>
    </div>`).join('');
}

window.addToCart = function(id, name, price, img, btn) {
  const c = getCart();
  const ex = c.find(i => i.id === id);
  if (ex) ex.qty++; else c.push({ id, name, price, img, qty: 1 });
  saveCart(c); updateCartUI();
  if (btn) {
    const o = btn.innerHTML;
    btn.innerHTML = '✔'; btn.style.background = '#1a6b35';
    setTimeout(() => { btn.innerHTML = o; btn.style.background = ''; }, 1400);
  }
  showToast('Added to cart!', 'success');
};
window.changeCartQty = function(id, d) {
  const c = getCart(); const i = c.find(x => x.id === id);
  if (i) { i.qty += d; if (i.qty <= 0) c.splice(c.indexOf(i), 1); }
  saveCart(c); updateCartUI();
};
window.removeFromCart = function(id) {
  saveCart(getCart().filter(i => i.id !== id)); updateCartUI();
};
window.toggleCart = function() {
  const d = document.getElementById('cart-drawer');
  const o = document.getElementById('cart-overlay');
  const open = d.classList.contains('open');
  d.classList.toggle('open', !open);
  if (o) o.style.display = open ? 'none' : 'block';
  if (!open) updateCartUI();
};

/* ══════════════════════════════════
   WISHLIST
══════════════════════════════════ */
function getWish() {
  try { return JSON.parse(localStorage.getItem('bai_wish') || '[]'); } catch(e) { return []; }
}
function saveWish(w) { localStorage.setItem('bai_wish', JSON.stringify(w)); }

window.toggleWish = function(id, name, price, img, btn) {
  let w = getWish(); const ex = w.find(x => x.id === id);
  if (ex) {
    w = w.filter(x => x.id !== id);
    if (btn) { btn.textContent = '♡'; btn.style.color = ''; btn.style.borderColor = ''; }
    showToast('Removed from wishlist');
  } else {
    w.push({ id, name, price, img });
    if (btn) { btn.textContent = '♥'; btn.style.color = 'var(--red)'; btn.style.borderColor = 'var(--red)'; }
    showToast('Added to wishlist!', 'success');
  }
  saveWish(w);
};
window.isWished = function(id) { return getWish().some(x => x.id === id); };

/* ══════════════════════════════════
   SEARCH (AJAX → api/search.php)
══════════════════════════════════ */
let searchTimer = null;

function doSearch(q, resultEl) {
  if (!q || q.length < 2) { if (resultEl) resultEl.classList.remove('open'); return; }
  clearTimeout(searchTimer);
  searchTimer = setTimeout(async () => {
    try {
      const r = await fetch(`${window.BAI_ROOT || ''}api/search.php?q=${encodeURIComponent(q)}`);
      const data = await r.json();
      if (!resultEl) return;
      if (!data.length) {
        resultEl.innerHTML = '<div style="padding:1rem;color:var(--text-muted);font-size:13px">No products found</div>';
      } else {
        resultEl.innerHTML = data.map(p => `
          <a href="${window.BAI_ROOT || ''}pages/products/view.php?slug=${p.slug}" class="search-item">
            <img src="${p.cover || (window.BAI_ROOT||'')+'assets/images/placeholder.svg'}" alt="${p.name}" onerror="this.src='${window.BAI_ROOT||''}assets/images/placeholder.svg'">
            <div>
              <div class="search-item-name">${p.name}</div>
              <div class="search-item-price">₹${Number(p.price).toLocaleString('en-IN')}</div>
            </div>
          </a>`).join('');
      }
      resultEl.classList.add('open');
    } catch(e) {}
  }, 280);
}

/* ══════════════════════════════════
   TOAST
══════════════════════════════════ */
let toastTimer;
window.showToast = function(msg, type = '') {
  let t = document.getElementById('bai-toast');
  if (!t) {
    t = document.createElement('div');
    t.id = 'bai-toast';
    t.className = 'toast';
    document.body.appendChild(t);
  }
  t.textContent = (type === 'success' ? '✅ ' : type === 'error' ? '❌ ' : '') + msg;
  t.className = 'toast show ' + type;
  clearTimeout(toastTimer);
  toastTimer = setTimeout(() => { t.className = 'toast'; }, 3000);
};

/* ══════════════════════════════════
   GALLERY (product page)
══════════════════════════════════ */
window.initGallery = function(imgs) {
  let cur = 0;
  window.setImg = function(i) {
    cur = i;
    const el = document.getElementById('main-img');
    if (!el) return;
    el.style.opacity = '0'; el.style.transform = 'scale(.97)';
    setTimeout(() => {
      el.src = imgs[i];
      el.style.opacity = '1'; el.style.transform = 'scale(1)';
    }, 150);
    document.querySelectorAll('.thumb').forEach((t, j) => t.classList.toggle('active', j === i));
    const ctr = document.getElementById('img-ctr');
    if (ctr) ctr.textContent = (i + 1) + ' / ' + imgs.length;
  };
  window.prevImg = function() { window.setImg((cur - 1 + imgs.length) % imgs.length); };
  window.nextImg = function() { window.setImg((cur + 1) % imgs.length); };
};

/* ══════════════════════════════════
   QTY (product page)
══════════════════════════════════ */
window.changeQty = function(d) {
  const el = document.getElementById('qty');
  if (el) el.textContent = Math.max(1, parseInt(el.textContent) + d);
};

/* ══════════════════════════════════
   SCROLL REVEAL
══════════════════════════════════ */
const revealObs = new IntersectionObserver(entries => {
  entries.forEach(e => {
    if (e.isIntersecting) { e.target.classList.add('visible'); revealObs.unobserve(e.target); }
  });
}, { threshold: .06, rootMargin: '0px 0px -24px 0px' });

function initReveal() {
  document.querySelectorAll('.reveal,.reveal-l,.reveal-r').forEach(el => {
    if (!el.classList.contains('visible')) revealObs.observe(el);
  });
}

/* ══════════════════════════════════
   NAV
══════════════════════════════════ */
window.closeSearch = function() {
  const ov = document.getElementById('search-overlay');
  if (ov) ov.style.display = 'none';
};
document.addEventListener('keydown', e => { if (e.key === 'Escape') { closeSearch(); } });

/* ══════════════════════════════════
   INIT
══════════════════════════════════ */
document.addEventListener('DOMContentLoaded', () => {
  // Cart UI
  updateCartUI();

  // Scroll → nav shadow
  window.addEventListener('scroll', () => {
    document.getElementById('main-nav')?.classList.toggle('scrolled', window.scrollY > 20);
  });

  // Hamburger
  const ham = document.getElementById('hamburger');
  const mob = document.getElementById('mobile-menu');
  if (ham && mob) {
    ham.addEventListener('click', () => {
      ham.classList.toggle('open');
      mob.classList.toggle('open');
      document.body.style.overflow = mob.classList.contains('open') ? 'hidden' : '';
    });
    mob.querySelectorAll('a').forEach(l => l.addEventListener('click', () => {
      ham.classList.remove('open');
      mob.classList.remove('open');
      document.body.style.overflow = '';
    }));
  }

  // Nav search
  const navInput = document.getElementById('nav-search-input');
  const navDrop  = document.getElementById('search-dropdown');
  if (navInput && navDrop) {
    navInput.addEventListener('input', () => doSearch(navInput.value, navDrop));
    document.addEventListener('click', e => {
      if (!e.target.closest('#nav-search-wrap')) navDrop.classList.remove('open');
    });
  }

  // Wishlist: restore state on page load
  const wb = document.getElementById('wish-btn');
  if (wb && wb.dataset.pid && isWished(wb.dataset.pid)) {
    wb.textContent = '♥'; wb.style.color = 'var(--red)'; wb.style.borderColor = 'var(--red)';
  }

  // Reveal
  initReveal();
});

function openSidebar() {
  const box = document.getElementById("admin-sidebar");
  const overlay = document.getElementById("sidebar-overlay");
  if (!box) return;
  const isOpen = box.classList.contains("open");
  box.classList.toggle("open", !isOpen);
  if (overlay) overlay.classList.toggle("active", !isOpen);
  document.body.style.overflow = !isOpen ? "hidden" : "";
}

// Close when clicking outside (overlay handles tap, this handles keyboard/misc)
document.addEventListener("click", (event) => {
  const box = document.getElementById("admin-sidebar");
  if (!box || !box.classList.contains("open")) return;
  if (event.target.closest("#admin-sidebar") || event.target.closest("#sidebar-toggle")) return;
  closeSidebar();
});

function closeSidebar() {
  const box = document.getElementById("admin-sidebar");
  const overlay = document.getElementById("sidebar-overlay");
  if (box) box.classList.remove("open");
  if (overlay) overlay.classList.remove("active");
  document.body.style.overflow = "";
}