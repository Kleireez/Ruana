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
?>

<!DOCTYPE html>
<html lang="id" data-bs-theme="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Upload Dokumen - RUANA</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
  <link rel="stylesheet" href="../public/css/style.css">
<style>:root{--role-dark:<?= $color['dark'] ?>;--role-primary:<?= $color['primary'] ?>;}</style>
</head>
<body>

<div id="overlay" class="overlay"></div>

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
    <span class="small text-muted">Halo, <strong><?= htmlspecialchars($nama) ?></strong></span>
    <a href="index.php?page=login&action=logout" class="btn btn-light btn-icon btn-sm ms-2" title="Logout">
      <i class="ti ti-logout"></i>
    </a>
  </div>
</nav>

<aside id="sidebar" class="sidebar">
  <div class="logo-area">
    <i class="ti ti-flower" style="color:<?= $color['primary'] ?>; font-size:20px;"></i>
    <span class="logo-text fw-semibold">RUANA</span>
  </div>
  <ul class="nav flex-column mt-2">
    <li class="px-4 py-2"><small class="text-muted nav-text" style="font-size:11px; ">Menu Utama</small></li>
    <li><a class="nav-link" href="index.php?page=dashboard"><i class="ti ti-home"></i><span class="nav-text">Dashboard</span></a></li>
    <?php if (Access::menuVisible($role, Access::MODULE_ANGGOTA)): ?>
    <li><a class="nav-link" href="index.php?page=anggota&action=index"><i class="ti ti-users"></i><span class="nav-text">Data Anggota</span></a></li>
    <?php endif; ?>
    <?php if (Access::menuVisible($role, Access::MODULE_KEUANGAN)): ?>
    <li><a class="nav-link" href="index.php?page=keuangan&action=index"><i class="ti ti-cash"></i><span class="nav-text">Keuangan</span></a></li>
    <?php endif; ?>
    <?php if (Access::menuVisible($role, Access::MODULE_DOKUMEN)): ?>
    <li><a class="nav-link active" href="index.php?page=dokumen&action=index"><i class="ti ti-file-text"></i><span class="nav-text">Dokumen & Arsip</span></a></li>
    <?php endif; ?>

    <li class="px-4 pt-4 pb-2"><small class="text-muted nav-text" style="font-size:11px; ">Akun</small></li>
    <li><a class="nav-link text-danger" href="index.php?page=login&action=logout"><i class="ti ti-logout"></i><span class="nav-text">Logout</span></a></li>
  </ul>
</aside>

<main id="content" class="content py-10">
  <div class="container-fluid">

    <div class="mb-4 d-flex align-items-center gap-2">
      <a href="index.php?page=dokumen&action=index" class="btn btn-light btn-icon btn-sm">
        <i class="ti ti-arrow-left"></i>
      </a>
      <div>
        <h1 class="fs-4 mb-1">Upload Dokumen</h1>
        <p class="text-muted small mb-0">Unggah proposal, surat, atau LPJ organisasi</p>
      </div>
    </div>

    <?php if (!empty($error)): ?>
      <div class="alert alert-danger py-2 small d-flex align-items-center gap-2">
        <i class="ti ti-alert-circle"></i> <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <div class="card border-0" style="max-width: 600px;">
      <div class="card-body p-4">
        <form action="index.php?page=dokumen&action=store" method="POST" enctype="multipart/form-data">

          <div class="mb-3">
            <label class="form-label small fw-medium">Nama Organisasi</label>
            <input type="text" name="nama_organisasi" class="form-control" placeholder="Contoh: ITSA, BEM, HIMA TI"
              value="<?= isset($_POST['nama_organisasi']) ? htmlspecialchars($_POST['nama_organisasi']) : '' ?>" required>
          </div>

          <div class="mb-3">
            <label class="form-label small fw-medium">Judul Dokumen</label>
            <input type="text" name="judul" class="form-control" placeholder="Contoh: Proposal Seminar IT 2026"
              value="<?= isset($_POST['judul']) ? htmlspecialchars($_POST['judul']) : '' ?>" required>
          </div>

          <div class="mb-3">
            <label class="form-label small fw-medium">Kategori</label>
            <select name="kategori" class="form-select" required>
              <option value="">Pilih kategori</option>
              <option value="Proposal">Proposal</option>
              <option value="Surat Masuk">Surat Masuk</option>
              <option value="Surat Keluar">Surat Keluar</option>
              <option value="LPJ">LPJ</option>
              <option value="Lainnya">Lainnya</option>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label small fw-medium">Tanggal Dokumen</label>
            <input type="date" name="tanggal" class="form-control"
              value="<?= isset($_POST['tanggal']) ? htmlspecialchars($_POST['tanggal']) : date('Y-m-d') ?>" required>
          </div>

          <div class="mb-4">
            <label class="form-label small fw-medium">File Dokumen</label>
            <input type="file" name="file" class="form-control" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required>
            <div class="form-text">Format yang didukung: PDF, DOC, DOCX, JPG, PNG</div>
          </div>

          <div class="d-flex gap-2">
            <button type="submit" class="btn text-white fw-medium" style="background-color:<?= $color['primary'] ?>; border-color:<?= $color['primary'] ?>;">
              <i class="ti ti-upload me-1"></i> Unggah Dokumen
            </button>
            <a href="index.php?page=dokumen&action=index" class="btn btn-light">Batal</a>
          </div>

        </form>
      </div>
    </div>

  </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  const sidebar  = document.getElementById('sidebar');
  const content  = document.getElementById('content');
  const topbar   = document.getElementById('topbar');
  const toggleBtn = document.getElementById('toggleBtn');
  const mobileBtn = document.getElementById('mobileBtn');
  const overlay  = document.getElementById('overlay');

  if (toggleBtn) toggleBtn.addEventListener('click', () => {
    sidebar.classList.toggle('collapsed');
    content.classList.toggle('full');
    topbar.classList.toggle('full');
  });
  if (mobileBtn) mobileBtn.addEventListener('click', () => {
    sidebar.classList.add('mobile-show');
    overlay.classList.add('show');
  });
  if (overlay) overlay.addEventListener('click', () => {
    sidebar.classList.remove('mobile-show');
    overlay.classList.remove('show');
  });
</script>
</body>
</html>