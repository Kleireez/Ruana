<?php require_once BASE_PATH . '/app/helpers/Access.php'; ?>
<?php
$roleColors = [
    'ketua'      => ['primary' => '#c9a45c', 'dark' => '#1f1610', 'bg' => 'rgba(201,164,92,0.12)',  'text' => '#c9a45c'],
    'sekretaris' => ['primary' => '#7fa3c7', 'dark' => '#0f1721', 'bg' => 'rgba(127,163,199,0.12)', 'text' => '#7fa3c7'],
    'bendahara'  => ['primary' => '#8fb08a', 'dark' => '#0f1a15', 'bg' => 'rgba(143,176,138,0.12)', 'text' => '#8fb08a'],
    'dosen'      => ['primary' => '#a48fc9', 'dark' => '#171221', 'bg' => 'rgba(164,143,201,0.12)', 'text' => '#a48fc9'],
    'anggota'    => ['primary' => '#b5ada0', 'dark' => '#16141a', 'bg' => 'rgba(181,173,160,0.10)', 'text' => '#b5ada0'],
];
$color = $roleColors[$role] ?? $roleColors['anggota'];

function request_is($page) {
    return ($_GET['page'] ?? '') === $page;
}

?>

<!DOCTYPE html>
<html lang="id" data-bs-theme="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dashboard - RUANA</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
  <link rel="stylesheet" href="../public/css/style.css">
<style>:root{--role-dark:<?= $color['dark'] ?>;--role-primary:<?= $color['primary'] ?>;}</style>
</head>
<body>

<?php if ($role !== 'dosen'): ?><div id="overlay" class="overlay"></div><?php endif; ?>

<!-- TOPBAR -->
<?php if ($role === 'dosen'): ?>
<!-- TOPBAR DOSEN -->
<nav class="navbar fixed-top px-4 d-flex justify-content-between align-items-center" style="height:60px; background:<?= $color['dark'] ?>; box-shadow:0 1px 8px rgba(0,0,0,0.18);">
  <div class="d-flex align-items-center gap-3">
    <div class="d-flex align-items-center gap-2">
      <i class="ti ti-flower" style="color:#fff; font-size:22px;"></i>
      <span class="fw-bold text-white" style="font-size:16px; letter-spacing:.01em;">RUANA</span>
    </div>
    <div style="width:1px; height:24px; background:rgba(255,255,255,0.2);"></div>
    <div class="d-flex gap-1">
      <a href="index.php?page=dashboard" class="btn btn-sm rounded-2 d-flex align-items-center gap-1" style="background:rgba(255,255,255,0.18); color:#fff; font-weight:500;">
        <i class="ti ti-home"></i> Dashboard
      </a>
      <a href="index.php?page=dokumen&action=index" class="btn btn-sm rounded-2 d-flex align-items-center gap-1" style="color:rgba(255,255,255,0.72);">
        <i class="ti ti-file-text"></i> Dokumen
      </a>
      <a href="index.php?page=dokumen&action=approval" class="btn btn-sm rounded-2 d-flex align-items-center gap-1" style="color:rgba(255,255,255,0.72);">
        <i class="ti ti-checkbox"></i> Approval
      </a>
    </div>
  </div>
  <div class="d-flex align-items-center gap-3">
    <div class="d-flex align-items-center gap-2">
      <div class="icon-shape icon-md rounded-circle d-flex align-items-center justify-content-center" style="background:rgba(255,255,255,0.15); width:32px; height:32px;">
        <i class="ti ti-user" style="color:#fff; font-size:16px;"></i>
      </div>
      <div>
        <p class="mb-0 fw-medium" style="color:#fff; font-size:13px;"><?= htmlspecialchars($nama) ?></p>
        <p class="mb-0" style="color:rgba(255,255,255,0.6); font-size:11px;">Dosen Pembimbing</p>
      </div>
    </div>
    <div style="width:1px; height:24px; background:rgba(255,255,255,0.2);"></div>
    <!-- LONCENG NOTIF DOSEN -->
    <div class="dropdown">
      <button class="btn btn-sm position-relative" type="button" data-bs-toggle="dropdown"
        style="color:rgba(255,255,255,0.75); border:1px solid rgba(255,255,255,0.2);"
        onclick="fetch('index.php?page=dokumen&action=readNotif')">
        <i class="ti ti-bell"></i>
        <?php if (!empty($notifUnread) && $notifUnread > 0): ?>
          <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:9px;">
            <?= $notifUnread > 9 ? '99+' : $notifUnread ?>
          </span>
        <?php endif; ?>
      </button>
      <div class="dropdown-menu dropdown-menu-end shadow border-0 p-0" style="width:320px; border-radius:12px; overflow:hidden;">
        <div class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom">
          <span class="fw-semibold small">Notifikasi</span>
          <?php if (!empty($notifUnread) && $notifUnread > 0): ?>
            <span class="badge rounded-pill" style="background:<?= $color['bg'] ?>; color:<?= $color['primary'] ?>; font-size:10px;">
              <?= $notifUnread ?> baru
            </span>
          <?php endif; ?>
        </div>
        <div style="max-height:320px; overflow-y:auto;">
          <?php if (empty($notifList)): ?>
            <div class="text-center text-muted py-4" style="font-size:13px;">
              <i class="ti ti-bell-off d-block fs-4 mb-1"></i>
              Belum ada notifikasi
            </div>
          <?php else: ?>
            <?php foreach ($notifList as $notif): ?>
              <a href="<?= htmlspecialchars($notif['link'] ?: '#') ?>"
                 class="d-flex gap-3 px-3 py-2 text-decoration-none border-bottom"
                 style="background:<?= $notif['is_read'] ? 'transparent' : $color['bg'] ?>;">
                <div class="d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width:32px; height:32px; border-radius:50%; background:<?= $color['bg'] ?>;">
                  <i class="ti ti-bell" style="color:<?= $color['primary'] ?>; font-size:14px;"></i>
                </div>
                <div>
                  <p class="mb-0 fw-medium" style="font-size:12px; color:var(--bs-body-color);"><?= htmlspecialchars($notif['judul']) ?></p>
                  <p class="mb-0 text-muted" style="font-size:11px;"><?= htmlspecialchars($notif['pesan']) ?></p>
                  <p class="mb-0" style="font-size:10px; color:<?= $color['primary'] ?>;">
                    <?= date('d M Y, H:i', strtotime($notif['created_at'])) ?>
                  </p>
                </div>
              </a>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
    <div style="width:1px; height:24px; background:rgba(255,255,255,0.2);"></div>
    <a href="index.php?page=login&action=logout" class="btn btn-sm d-flex align-items-center gap-1" style="color:rgba(255,255,255,0.75); border:1px solid rgba(255,255,255,0.2);" title="Logout">
      <i class="ti ti-logout"></i> Keluar
    </a>
  </div>
</nav>
<?php else: ?>
<nav id="topbar" class="navbar bg-white border-bottom fixed-top topbar px-3 d-flex justify-content-between">
  <div class="d-flex align-items-center gap-2">
    <button id="toggleBtn" class="d-none d-lg-inline-flex btn btn-light btn-icon btn-sm">
      <i class="ti ti-layout-sidebar-left-expand"></i>
    </button>
    <button id="mobileBtn" class="btn btn-light btn-icon btn-sm d-lg-none">
      <i class="ti ti-layout-sidebar-left-expand"></i>
    </button>
  </div>
  <div class="d-flex align-items-center gap-2">
    <span class="small text-muted">Hy, <strong><?= htmlspecialchars($nama) ?></strong></span>
    <span class="badge rounded-pill" style="background-color:<?= $color['bg'] ?>; color:<?= $color['primary'] ?>;">
      <?= htmlspecialchars(ucfirst($role)) ?>
    </span>
    <a href="index.php?page=login&action=logout" class="btn btn-light btn-icon btn-sm ms-2" title="Logout">
      <i class="ti ti-logout"></i>
    </a>
  </div>
</nav>

<!-- SIDEBAR -->
<aside id="sidebar" class="sidebar">
  <div class="logo-area">
    <i class="ti ti-flower" style="color:<?= $color['primary'] ?>; font-size:20px;"></i>
    <span class="logo-text fw-bold">RUANA</span>
  </div>
  <ul class="nav flex-column mt-2">
    <li class="px-4 py-2"><small class="text-muted nav-text" style="font-size:11px; ">Menu Utama</small></li>
    <li><a class="nav-link active" href="index.php?page=dashboard"><i class="ti ti-home"></i><span class="nav-text">Dashboard</span></a></li>
    <?php if (Access::menuVisible($role, Access::MODULE_ANGGOTA)): ?>
    <li><a class="nav-link" href="index.php?page=anggota&action=index"><i class="ti ti-users"></i><span class="nav-text">Data Anggota</span></a></li>
    <?php endif; ?>
    <?php if (Access::menuVisible($role, Access::MODULE_KEUANGAN)): ?>
    <li><a class="nav-link" href="index.php?page=keuangan&action=index"><i class="ti ti-cash"></i><span class="nav-text">Keuangan</span></a></li>
    <?php endif; ?>
    <?php if (Access::menuVisible($role, Access::MODULE_DOKUMEN)): ?>
    <li><a class="nav-link" href="index.php?page=dokumen&action=index"><i class="ti ti-file-text"></i><span class="nav-text">Dokumen & Arsip</span></a></li>
    <?php endif; ?>

    <li class="px-4 pt-4 pb-2"><small class="text-muted nav-text" style="font-size:11px; ">Akun</small></li>
    <li><a class="nav-link text-danger" href="index.php?page=login&action=logout"><i class="ti ti-logout"></i><span class="nav-text">Logout</span></a></li>
  </ul>
</aside>
<?php endif; ?>

<!-- MAIN CONTENT -->
<main id="content" class="<?= $role === 'dosen' ? 'container-fluid px-4' : 'content py-10' ?>" style="<?= $role === 'dosen' ? 'margin-top:80px;' : '' ?>">
  <div class="container-fluid">

    <div class="mb-4">
      <h1 class="fs-4 mb-1">Dashboard</h1>
      <p class="text-muted small mb-0">Selamat datang di sistem RUANA, <?= htmlspecialchars($nama) ?>!</p>
    </div>

    <?php if (!empty($success)): ?>
      <div class="alert alert-success py-2 small d-flex align-items-center gap-2">
        <i class="ti ti-circle-check"></i> <?= htmlspecialchars($success) ?>
      </div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
      <div class="alert alert-danger py-2 small d-flex align-items-center gap-2">
        <i class="ti ti-alert-circle"></i> <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>


    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
      <div class="col-lg-3 col-6">
        <div class="card p-4 border-0 h-100" style="background-color:<?= $color['bg'] ?>;">
          <div class="d-flex gap-3 align-items-center">
            <div class="icon-shape icon-md rounded-2 text-white" style="background-color:<?= $color['primary'] ?>;">
              <i class="ti ti-users fs-5"></i>
            </div>
            <div>
              <p class="mb-1 small text-muted">Total Anggota</p>
              <h4 class="fw-bold mb-0"><?= number_format($totalAnggota, 0, ',', '.') ?></h4>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-6">
        <div class="card p-4 border-0 h-100" style="background-color:rgba(143,176,138,0.08);">
          <div class="d-flex gap-3 align-items-center">
            <div class="icon-shape icon-md rounded-2 text-white" style="background-color:#8fb08a;">
              <i class="ti ti-trending-up fs-5"></i>
            </div>
            <div>
              <p class="mb-1 small text-muted">Pemasukan</p>
              <h4 class="fw-bold mb-0">Rp <?= number_format($totalPemasukan, 0, ',', '.') ?></h4>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-6">
        <div class="card p-4 border-0 h-100" style="background-color:rgba(194,69,58,0.08);">
          <div class="d-flex gap-3 align-items-center">
            <div class="icon-shape icon-md rounded-2 text-white" style="background-color:#c2453a;">
              <i class="ti ti-trending-down fs-5"></i>
            </div>
            <div>
              <p class="mb-1 small text-muted">Pengeluaran</p>
              <h4 class="fw-bold mb-0">Rp <?= number_format($totalPengeluaran, 0, ',', '.') ?></h4>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-6">
        <div class="card p-4 border-0 h-100" style="background-color:rgba(127,163,199,0.08);">
          <div class="d-flex gap-3 align-items-center">
            <div class="icon-shape icon-md rounded-2 text-white" style="background-color:#7fa3c7;">
              <i class="ti ti-file-text fs-5"></i>
            </div>
            <div>
              <p class="mb-1 small text-muted">Dokumen</p>
              <h4 class="fw-bold mb-0"><?= number_format($totalDokumen, 0, ',', '.') ?></h4>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Info Role -->
    <div class="row g-3">
      <div class="col-12">
        <div class="card border-0">
          <div class="card-body p-4">
            <h5 class="mb-3">Akses Anda sebagai <span style="color:<?= $color['primary'] ?>;"><?= htmlspecialchars(ucfirst($role)) ?></span></h5>
            <div class="row g-2">
              <?php if (Access::menuVisible($role, Access::MODULE_ANGGOTA)): ?>
              <div class="col-md-4">
                <a href="index.php?page=anggota&action=index" class="card border-0 p-3 text-decoration-none d-flex flex-row align-items-center gap-3" style="background:var(--ce-surface-2);">
                  <i class="ti ti-users fs-4" style="color:<?= $color['primary'] ?>;"></i>
                  <div><p class="mb-0 fw-medium small">Data Anggota</p><p class="mb-0 text-muted" style="font-size:12px;">Kelola anggota organisasi</p></div>
                </a>
              </div>
              <?php endif; ?>
              <?php if (Access::menuVisible($role, Access::MODULE_KEUANGAN)): ?>
              <div class="col-md-4">
                <a href="index.php?page=keuangan&action=index" class="card border-0 p-3 text-decoration-none d-flex flex-row align-items-center gap-3" style="background:var(--ce-surface-2);">
                  <i class="ti ti-cash fs-4" style="color:#8fb08a;"></i>
                  <div><p class="mb-0 fw-medium small">Keuangan</p><p class="mb-0 text-muted" style="font-size:12px;">Catat pemasukan & pengeluaran</p></div>
                </a>
              </div>
              <?php endif; ?>
              <?php if (Access::menuVisible($role, Access::MODULE_DOKUMEN)): ?>
              <div class="col-md-4">
                <a href="index.php?page=dokumen&action=index" class="card border-0 p-3 text-decoration-none d-flex flex-row align-items-center gap-3" style="background:var(--ce-surface-2);">
                  <i class="ti ti-file-text fs-4" style="color:#7fa3c7;"></i>
                  <div><p class="mb-0 fw-medium small">Dokumen & Arsip</p><p class="mb-0 text-muted" style="font-size:12px;">Upload & kelola dokumen</p></div>
                </a>
              </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  <?php if ($role !== 'dosen'): ?>
  const sidebar  = document.getElementById('sidebar');
  const content  = document.getElementById('content');
  const topbar   = document.getElementById('topbar');
  const toggleBtn = document.getElementById('toggleBtn');
  const mobileBtn = document.getElementById('mobileBtn');
  const overlay  = document.getElementById('overlay');

  if (toggleBtn) {
    toggleBtn.addEventListener('click', () => {
      sidebar.classList.toggle('collapsed');
      content.classList.toggle('full');
      topbar.classList.toggle('full');
    });
  }
  if (mobileBtn) {
    mobileBtn.addEventListener('click', () => {
      sidebar.classList.add('mobile-show');
      overlay.classList.add('show');
    });
  }
  if (overlay) {
    overlay.addEventListener('click', () => {
      sidebar.classList.remove('mobile-show');
      overlay.classList.remove('show');
    });
  }
  <?php endif; ?>
</script>
</body>
</html>