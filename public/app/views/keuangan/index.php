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
  <title>Keuangan - RUANA</title>
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
    <span class="badge rounded-pill" style="background-color:<?= $color['bg'] ?>; color:<?= $color['primary'] ?>;">
      <?= htmlspecialchars(ucfirst($role)) ?>
    </span>
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
    <li><a class="nav-link active" href="index.php?page=keuangan&action=index"><i class="ti ti-cash"></i><span class="nav-text">Keuangan</span></a></li>
    <?php endif; ?>
    <?php if (Access::menuVisible($role, Access::MODULE_DOKUMEN)): ?>
    <li><a class="nav-link" href="index.php?page=dokumen&action=index"><i class="ti ti-file-text"></i><span class="nav-text">Dokumen & Arsip</span></a></li>
    <?php endif; ?>

    <li class="px-4 pt-4 pb-2"><small class="text-muted nav-text" style="font-size:11px; ">Akun</small></li>
    <li><a class="nav-link text-danger" href="index.php?page=login&action=logout"><i class="ti ti-logout"></i><span class="nav-text">Logout</span></a></li>
  </ul>
</aside>

<main id="content" class="content py-10">
  <div class="container-fluid">

    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
      <div>
        <h1 class="fs-4 mb-1">Keuangan</h1>
        <p class="text-muted small mb-0">Catatan pemasukan dan pengeluaran organisasi</p>
      </div>
      <?php if ($role === 'bendahara'): ?>
      <a href="index.php?page=keuangan&action=create" class="btn text-white fw-medium" style="background-color:<?= $color['primary'] ?>; border-color:<?= $color['primary'] ?>;">
        <i class="ti ti-plus me-1"></i> Tambah Transaksi
      </a>
      <?php endif; ?>
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

    <!-- Kartu ringkasan -->
    <div class="row g-3 mb-4">
      <div class="col-md-4">
        <div class="card border-0 p-4" style="background-color:rgba(143,176,138,0.08);">
          <div class="d-flex align-items-center gap-3">
            <div class="icon-shape icon-md rounded-2 text-white" style="background-color:#8fb08a;">
              <i class="ti ti-trending-up fs-5"></i>
            </div>
            <div>
              <p class="mb-1 small text-muted">Total Pemasukan</p>
              <h5 class="fw-bold mb-0">Rp <?= number_format($totalPemasukan, 0, ',', '.') ?></h5>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card border-0 p-4" style="background-color:rgba(194,69,58,0.08);">
          <div class="d-flex align-items-center gap-3">
            <div class="icon-shape icon-md rounded-2 text-white" style="background-color:#c2453a;">
              <i class="ti ti-trending-down fs-5"></i>
            </div>
            <div>
              <p class="mb-1 small text-muted">Total Pengeluaran</p>
              <h5 class="fw-bold mb-0">Rp <?= number_format($totalPengeluaran, 0, ',', '.') ?></h5>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card border-0 p-4" style="background-color:<?= $color['bg'] ?>;">
          <div class="d-flex align-items-center gap-3">
            <div class="icon-shape icon-md rounded-2 text-white" style="background-color:<?= $color['primary'] ?>;">
              <i class="ti ti-wallet fs-5"></i>
            </div>
            <div>
              <p class="mb-1 small text-muted">Saldo</p>
              <h5 class="fw-bold mb-0 <?= $saldo < 0 ? 'text-danger' : '' ?>">
                Rp <?= number_format($saldo, 0, ',', '.') ?>
              </h5>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Tabel transaksi -->
    <div class="card border-0">
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table align-middle mb-0">
            <thead style="background-color:var(--ce-surface-2);">
              <tr>
                <th class="ps-4">#</th>
                <th>Tanggal</th>
                <th>Keterangan</th>
                <th>Jenis</th>
                <th>Jumlah</th>
                <th>Dicatat Oleh</th>
                <?php if ($role === 'bendahara'): ?>
                <th class="text-end pe-4">Aksi</th>
                <?php endif; ?>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($transaksi)): ?>
                <tr>
                  <td colspan="7" class="text-center text-muted py-5">
                    <i class="ti ti-cash-off fs-3 d-block mb-2"></i>
                    Belum ada transaksi.
                  </td>
                </tr>
              <?php else: ?>
                <?php foreach ($transaksi as $i => $t): ?>
                  <tr>
                    <td class="ps-4"><?= $i + 1 ?></td>
                    <td><?= date('d M Y', strtotime($t['tanggal'])) ?></td>
                    <td class="fw-medium"><?= htmlspecialchars($t['keterangan']) ?></td>
                    <td>
                      <?php if ($t['jenis'] === 'pemasukan'): ?>
                        <span class="badge rounded-pill" style="background-color:rgba(143,176,138,0.1); color:#9cc497;">Pemasukan</span>
                      <?php else: ?>
                        <span class="badge rounded-pill" style="background-color:rgba(194,69,58,0.1); color:#e0655a;">Pengeluaran</span>
                      <?php endif; ?>
                    </td>
                    <td class="fw-medium <?= $t['jenis'] === 'pemasukan' ? 'text-success' : 'text-danger' ?>">
                      <?= $t['jenis'] === 'pengeluaran' ? '- ' : '+ ' ?>Rp <?= number_format($t['jumlah'], 0, ',', '.') ?>
                    </td>
                    <td class="text-muted small"><?= htmlspecialchars($t['dicatat_oleh'] ?? '-') ?></td>
                    <?php if ($role === 'bendahara'): ?>
                    <td class="text-end pe-4">
                      <a href="index.php?page=keuangan&action=delete&id=<?= $t['id'] ?>"
                         onclick="return confirm('Hapus transaksi \'<?= htmlspecialchars($t['keterangan']) ?>\'?')"
                         class="btn btn-light btn-icon btn-sm text-danger" title="Hapus">
                        <i class="ti ti-trash"></i>
                      </a>
                    </td>
                    <?php endif; ?>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

  </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  const sidebar   = document.getElementById('sidebar');
  const content   = document.getElementById('content');
  const topbar    = document.getElementById('topbar');
  const toggleBtn = document.getElementById('toggleBtn');
  const mobileBtn = document.getElementById('mobileBtn');
  const overlay   = document.getElementById('overlay');

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