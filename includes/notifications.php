<?php if (!empty($_SESSION['flash'])): ?>
  <div id="toast"
       class="toast <?= htmlspecialchars($_SESSION['flash']['type']) ?>"
       role="alert" aria-live="assertive" aria-atomic="true">
    <?= htmlspecialchars($_SESSION['flash']['message']) ?>
    <div class="progress-bar"></div>
  </div>

  <script>
    const toast = document.getElementById('toast');
    toast.classList.add('show');

    setTimeout(()=>{ toast.classList.remove('show'); toast.classList.add('hide'); }, 5000);
    setTimeout(()=>{ toast?.remove(); }, 5500);
  </script>
  <?php unset($_SESSION['flash']); ?>
<?php endif; ?>
