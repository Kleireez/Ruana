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
  <title>Dokumen & Arsip - RUANA</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
  <link rel="stylesheet" href="../public/css/style.css">
<style>:root{--role-dark:<?= $color['dark'] ?>;--role-primary:<?= $color['primary'] ?>;}</style>
</head>
<body>

<?php if ($role !== 'dosen'): ?><div id="overlay" class="overlay"></div><?php endif; ?>

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
      <a href="index.php?page=dashboard" class="btn btn-sm rounded-2 d-flex align-items-center gap-1" style="color:rgba(255,255,255,0.72);">
        <i class="ti ti-home"></i> Dashboard
      </a>
      <a href="index.php?page=dokumen&action=index" class="btn btn-sm rounded-2 d-flex align-items-center gap-1" style="background:rgba(255,255,255,0.18); color:#fff; font-weight:500;">
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
            <?= $notifUnread > 9 ? '9+' : $notifUnread ?>
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
    <li><a class="nav-link" href="index.php?page=keuangan&action=index"><i class="ti ti-cash"></i><span class="nav-text">Keuangan</span></a></li>
    <?php endif; ?>
    <?php if (Access::menuVisible($role, Access::MODULE_DOKUMEN)): ?>
    <li><a class="nav-link active" href="index.php?page=dokumen&action=index"><i class="ti ti-file-text"></i><span class="nav-text">Dokumen & Arsip</span></a></li>
    <?php endif; ?>
    <?php if ($role === 'dosen'): ?>
    <li><a class="nav-link" href="index.php?page=dokumen&action=approval"><i class="ti ti-checkbox"></i><span class="nav-text">Approval Dokumen</span></a></li>
    <?php endif; ?>

    <li class="px-4 pt-4 pb-2"><small class="text-muted nav-text" style="font-size:11px; ">Akun</small></li>
    <li><a class="nav-link text-danger" href="index.php?page=login&action=logout"><i class="ti ti-logout"></i><span class="nav-text">Logout</span></a></li>
  </ul>
</aside>
<?php endif; ?>

<main id="content" class="<?= $role === 'dosen' ? 'container-fluid px-4' : 'content py-10' ?>" style="<?= $role === 'dosen' ? 'margin-top:80px;' : '' ?>">
  <div class="container-fluid">

    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
      <div>
        <h1 class="fs-4 mb-1">Dokumen & Arsip</h1>
        <p class="text-muted small mb-0">Kelola dokumen dan arsip organisasi</p>
      </div>
      <?php if ($role === 'sekretaris'): ?>
      <a href="index.php?page=dokumen&action=create" class="btn text-white fw-medium" style="background-color:<?= $color['primary'] ?>; border-color:<?= $color['primary'] ?>;">
        <i class="ti ti-plus me-1"></i> Upload Dokumen
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

    <?php
      $filterStatus = $_GET['status'] ?? '';
      $keyword      = trim($_GET['q'] ?? '');
      $filters = [
        ''          => 'Semua',
        'pending'   => 'Menunggu',
        'disetujui' => 'Disetujui',
        'ditolak'   => 'Ditolak',
      ];
    ?>

    <!-- Pencarian Dokumen -->
    <form method="GET" action="index.php" class="mb-3">
      <input type="hidden" name="page" value="dokumen">
      <input type="hidden" name="action" value="index">
      <?php if ($filterStatus !== ''): ?>
        <input type="hidden" name="status" value="<?= htmlspecialchars($filterStatus) ?>">
      <?php endif; ?>
      <div class="input-group input-group-sm" style="max-width:360px;">
        <span class="input-group-text bg-white border-end-0"><i class="ti ti-search text-muted"></i></span>
        <input type="text" name="q" value="<?= htmlspecialchars($keyword) ?>"
               class="form-control border-start-0"
               placeholder="Cari judul, kategori, organisasi, atau pengunggah...">
        <?php if ($keyword !== ''): ?>
          <a href="index.php?page=dokumen&action=index<?= $filterStatus ? '&status=' . $filterStatus : '' ?>"
             class="btn btn-light border" title="Reset pencarian">
            <i class="ti ti-x"></i>
          </a>
        <?php endif; ?>
        <button type="submit" class="btn text-white" style="background-color:<?= $color['primary'] ?>;">Cari</button>
      </div>
    </form>

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
      <div class="d-flex gap-2 flex-wrap">
        <?php foreach ($filters as $value => $label): ?>
          <a href="index.php?page=dokumen&action=index<?= $value ? '&status=' . $value : '' ?><?= $keyword !== '' ? '&q=' . urlencode($keyword) : '' ?>"
             class="btn btn-sm <?= $filterStatus === $value ? 'text-white' : 'btn-light' ?>"
             style="<?= $filterStatus === $value ? 'background-color:' . $color['primary'] . '; border-color:' . $color['primary'] . ';' : '' ?>">
            <?= $label ?>
          </a>
        <?php endforeach; ?>
      </div>
      <?php if ($keyword !== ''): ?>
        <span class="small text-muted">
          Hasil pencarian untuk "<strong><?= htmlspecialchars($keyword) ?></strong>": <?= count($dokumens) ?> dokumen
        </span>
      <?php endif; ?>
    </div>

    <div class="card border-0">
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table align-middle mb-0">
            <thead style="background-color:var(--ce-surface-2);">
              <tr>
                <th class="ps-4">Judul</th>
                <th>Organisasi</th>
                <th>Kategori</th>
                <th>Tanggal</th>
                <th>Diunggah Oleh</th>
                <th>Status</th>
                <th class="text-end pe-4">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($dokumens)): ?>
                <tr>
                  <td colspan="7" class="text-center text-muted py-5">
                    <i class="ti ti-file-off fs-3 d-block mb-2"></i>
                    <?php if ($keyword !== ''): ?>
                      Tidak ditemukan dokumen untuk pencarian "<?= htmlspecialchars($keyword) ?>"<?= $filterStatus ? ' dengan status "' . htmlspecialchars($filters[$filterStatus]) . '"' : '' ?>.
                    <?php elseif ($filterStatus): ?>
                      Tidak ada dokumen dengan status "<?= htmlspecialchars($filters[$filterStatus]) ?>".
                    <?php else: ?>
                      Belum ada dokumen.
                    <?php endif; ?>
                  </td>
                </tr>
              <?php else: ?>
                <?php foreach ($dokumens as $doc): ?>
                  <tr>
                    <td class="ps-4 fw-medium"><?= htmlspecialchars($doc['judul']) ?></td>
                    <td>
                      <span class="badge" style="background-color:<?= $color['bg'] ?>; color:<?= $color['primary'] ?>;">
                        <?= htmlspecialchars($doc['nama_organisasi'] ?? '-') ?>
                      </span>
                    </td>
                    <td><?= htmlspecialchars($doc['kategori']) ?></td>
                    <td><?= date('d M Y', strtotime($doc['tanggal'])) ?></td>
                    <td><?= htmlspecialchars($doc['nama_uploader'] ?? '-') ?></td>
                    <td>
                      <?php if ($doc['status_approval'] === 'disetujui'): ?>
                        <span class="badge rounded-pill" style="background-color:rgba(143,176,138,0.1); color:#9cc497;">Disetujui</span>
                      <?php elseif ($doc['status_approval'] === 'ditolak'): ?>
                        <span class="badge rounded-pill" style="background-color:rgba(194,69,58,0.1); color:#e0655a;">Ditolak</span>
                      <?php else: ?>
                        <span class="badge rounded-pill" style="background-color:rgba(217,168,74,0.1); color:#d9a84a;">Menunggu</span>
                      <?php endif; ?>
                      <?php if (!empty($doc['catatan_dosen'])): ?>
                        <div class="text-muted mt-1" style="font-size:11px;">
                          <i class="ti ti-message-2 me-1"></i><?= htmlspecialchars($doc['catatan_dosen']) ?>
                        </div>
                      <?php endif; ?>
                    </td>
                    <td class="text-end pe-4">
                      <a href="../public/uploads/<?= htmlspecialchars($doc['nama_file']) ?>" target="_blank" class="btn btn-light btn-icon btn-sm" title="Lihat">
                        <i class="ti ti-eye"></i>
                      </a>
                      <?php if ($role === 'sekretaris'): ?>
                      <a href="index.php?page=dokumen&action=delete&id=<?= $doc['id'] ?>"
                         onclick="return confirm('Yakin ingin menghapus dokumen \'<?= htmlspecialchars($doc['judul']) ?>\'?')"
                         class="btn btn-light btn-icon btn-sm text-danger" title="Hapus">
                        <i class="ti ti-trash"></i>
                      </a>
                      <?php endif; ?>
                    </td>
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