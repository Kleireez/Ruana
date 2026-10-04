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
  <title><?= isset($anggota['id']) ? 'Edit' : 'Tambah' ?> Anggota - RUANA</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
  <link rel="stylesheet" href="../public/css/style.css?v=<?= @filemtime(BASE_PATH . '/public/css/style.css') ?: time() ?>">
<style>:root{--role-dark:<?= $color['dark'] ?>;--role-primary:<?= $color['primary'] ?>;}</style>
</head>
<body>

<div id="overlay" class="overlay"></div>

<!-- TOPBAR -->
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
    <span class="logo-text fw-semibold">RUANA</span>
  </div>
  <ul class="nav flex-column mt-2">
    <li class="px-4 py-2"><small class="text-muted nav-text" style="font-size:11px; ">Menu Utama</small></li>
    <li><a class="nav-link" href="index.php?page=dashboard"><i class="ti ti-home"></i><span class="nav-text">Dashboard</span></a></li>
    <?php if (Access::menuVisible($role, Access::MODULE_ANGGOTA)): ?>
    <li><a class="nav-link active" href="index.php?page=anggota&action=index"><i class="ti ti-users"></i><span class="nav-text">Data Anggota</span></a></li>
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

<!-- MAIN CONTENT -->
<main id="content" class="content py-10">
  <div class="container-fluid">

    <div class="mb-4">
      <h1 class="fs-4 mb-1"><?= isset($anggota['id']) ? 'Edit Anggota' : 'Tambah Anggota' ?></h1>
      <p class="text-muted small mb-0">
        <a href="index.php?page=anggota&action=index" class="text-decoration-none text-muted">
          <i class="ti ti-arrow-left"></i> Kembali ke Data Anggota
        </a>
      </p>
    </div>

    <?php if (!empty($error)): ?>
      <div class="alert alert-danger py-2 small d-flex align-items-center gap-2">
        <i class="ti ti-alert-circle"></i> <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <div class="card border-0" style="max-width:680px;">
      <div class="card-body p-4">

        <?php
          $isEdit = isset($anggota['id']);
          $action = $isEdit
            ? 'index.php?page=anggota&action=update&id=' . $anggota['id']
            : 'index.php?page=anggota&action=store';
        ?>

        <form action="<?= $action ?>" method="POST">

          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label small fw-medium">NIM <span class="text-danger">*</span></label>
              <input type="text" name="nim" class="form-control"
                     value="<?= htmlspecialchars($anggota['nim'] ?? '') ?>"
                     placeholder="Contoh: 2555301064" required autofocus>
            </div>

            <div class="col-md-6">
              <label class="form-label small fw-medium">Nama Lengkap <span class="text-danger">*</span></label>
              <input type="text" name="nama" class="form-control"
                     value="<?= htmlspecialchars($anggota['nama'] ?? '') ?>"
                     placeholder="Nama lengkap anggota" required>
            </div>

            <div class="col-md-6">
              <label class="form-label small fw-medium">Email</label>
              <input type="email" name="email" class="form-control"
                     value="<?= htmlspecialchars($anggota['email'] ?? '') ?>"
                     placeholder="email@pcr.ac.id">
            </div>

            <div class="col-md-6">
              <label class="form-label small fw-medium">No. HP</label>
              <input type="text" name="no_hp" class="form-control"
                     value="<?= htmlspecialchars($anggota['no_hp'] ?? '') ?>"
                     placeholder="08xxxxxxxxxx">
            </div>

            <div class="col-md-6">
              <label class="form-label small fw-medium">Jabatan</label>
              <input type="text" name="jabatan" class="form-control"
                     value="<?= htmlspecialchars($anggota['jabatan'] ?? '') ?>"
                     placeholder="Contoh: Ketua, Sekretaris, Anggota">
            </div>

            <div class="col-md-6">
              <label class="form-label small fw-medium">Status</label>
              <?php $status = $anggota['status'] ?? 'aktif'; ?>
              <select name="status" class="form-select">
                <option value="aktif" <?= $status === 'aktif' ? 'selected' : '' ?>>Aktif</option>
                <option value="tidak aktif" <?= $status === 'tidak aktif' ? 'selected' : '' ?>>Tidak Aktif</option>
              </select>
            </div>
          </div>

          <div class="d-flex gap-2 mt-4">
            <button type="submit" class="btn text-white fw-medium" style="background-color:<?= $color['primary'] ?>; border-color:<?= $color['primary'] ?>;">
              <i class="ti ti-device-floppy me-1"></i> Simpan
            </button>
            <a href="index.php?page=anggota&action=index" class="btn btn-light fw-medium">
              Batal
            </a>
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
</script>
</body>
</html>