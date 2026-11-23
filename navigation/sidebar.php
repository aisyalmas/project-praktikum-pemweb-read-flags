<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<script>
  document.body.classList.toggle('sb-collapsed', !window.matchMedia('(min-width: 992px)').matches);
</script>
<?php
  require_once __DIR__ . '/../config/config.php';
?>
<div class="d-flex">
  <nav class="sidebar d-flex flex-column flex-shrink-0 position-fixed" id="sidebar">

    <button class="toggle-btn" type="button" onclick="toggleSidebar()" aria-label="Toggle sidebar">
      <i class="bi bi-chevron-left"></i>
    </button>

    <div class="sidebar-header p-3">
      <a href="#" class="logo-link d-flex align-items-center gap-2 text-decoration-none">
        <div class="logo-icon">
          <i class="bi bi-bookmark-fill"></i>
        </div>
        <span class="logo-text fw-bold">Read Flags</span>
      </a>
    </div>

    <div class="nav flex-column px-2">
      <div class="add pb-3">
        <a class="button-add text-decoration-none" id="btn-tambah-buku">
          <i class="bi bi-plus" style="font-size: 20px;"></i>
          <span class="text-add">Add</span>
        </a>
      </div>
      <a href="<?= BASE_URL ?>/dashboard/dashboard.php" class="sidebar-link text-decoration-none <?= $currentPage == 'dashboard.php' ? 'active' : '' ?>">
        <i class="bi bi-house"></i>
        <span>Home</span>
      </a>
      <a href="<?= BASE_URL ?>/library/library.php" class="sidebar-link text-decoration-none <?= $currentPage == 'library.php' ? 'active' : '' ?>">
        <i class="bi bi-book"></i>
        <span>Library</span>
      </a>
      <a href="<?= BASE_URL ?>/review/review.php" class="sidebar-link text-decoration-none <?= $currentPage == 'review.php' ? 'active' : '' ?>">
        <i class="bi bi-journal"></i>
        <span>Reviews</span>
      </a>
      <a href="<?= BASE_URL ?>/profile/profile.php" class="sidebar-link text-decoration-none <?= $currentPage == 'profile.php' ? 'active' : '' ?>">
        <i class="bi bi-person"></i>
        <span>Profile</span>
      </a>
    </div>

    <!-- [TAMBAH] logout di dasar sidebar -->
    <div class="sidebar-footer mt-auto px-2 pb-3">
      <a href="<?= BASE_URL ?>/auth/logout.php" class="sidebar-link logout-link text-decoration-none" aria-label="Logout">
        <i class="bi bi-box-arrow-left"></i>
        <span>Logout</span>
      </a>
    </div>
  </nav>
</div>