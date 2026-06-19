  </div><!-- /admin-content -->
</div><!-- /admin-main -->
</div><!-- /admin-layout -->

<script>
(function () {
  var sidebar  = document.getElementById('admin-sidebar');
  var overlay  = document.getElementById('sidebar-overlay');
  var toggle   = document.getElementById('sidebar-toggle');
  var closeBtn = document.getElementById('closeSidebar');

  function openSidebar() {
    sidebar.classList.add('open');
    overlay.classList.add('active');
    document.body.style.overflow = 'hidden';
  }

  function closeSidebar() {
    sidebar.classList.remove('open');
    overlay.classList.remove('active');
    document.body.style.overflow = '';
  }

  // Hamburger opens sidebar
  if (toggle) {
    toggle.addEventListener('click', function (e) {
      e.stopPropagation();
      if (sidebar.classList.contains('open')) {
        closeSidebar();
      } else {
        openSidebar();
      }
    });
  }

  // ✕ button inside sidebar closes it
  if (closeBtn) {
    closeBtn.addEventListener('click', closeSidebar);
  }

  // Overlay click closes sidebar
  if (overlay) {
    overlay.addEventListener('click', closeSidebar);
  }

  // Close sidebar when a nav link is clicked (navigating away)
  var links = sidebar ? sidebar.querySelectorAll('.sidebar-link') : [];
  links.forEach(function (link) {
    link.addEventListener('click', function () {
      if (window.innerWidth <= 900) closeSidebar();
    });
  });

  // Escape key closes sidebar
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') closeSidebar();
  });
})();
</script>
</body>
</html>
