</main>
</div>
<script src="<?= asset('js/admin.js') ?>"></script>
<script>
(function () {
  var toggle = document.getElementById('admin-menu-toggle');
  var sidebar = document.getElementById('admin-sidebar');
  var overlay = document.getElementById('admin-overlay');
  if (!toggle || !sidebar) return;
  function close() {
    sidebar.classList.remove('open');
    if (overlay) overlay.classList.remove('show');
  }
  function open() {
    sidebar.classList.add('open');
    if (overlay) overlay.classList.add('show');
  }
  toggle.addEventListener('click', function () {
    if (sidebar.classList.contains('open')) close();
    else open();
  });
  if (overlay) overlay.addEventListener('click', close);
})();
</script>
</body>
</html>
