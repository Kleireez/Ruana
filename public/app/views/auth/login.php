<!DOCTYPE html>
<html lang="id" data-bs-theme="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Masuk - RUANA</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
  <link rel="stylesheet" href="../public/css/style.css">
</head>
<body>

<div class="container-fluid p-0">
  <div class="row g-0 min-vh-100">

    <!-- Sisi kiri: monolit -->
    <div class="col-lg-6 login-stage">
      <?= file_get_contents(BASE_PATH . '/public/images/ilustrasi-login.svg') ?>
      <div class="login-caption">
        <p>Untuk mereka yang datang setelah kita.</p>
        <small>Data anggota, keuangan, dan dokumen organisasi dalam satu arsip.</small>
      </div>
    </div>

    <!-- Sisi kanan: form login -->
    <div class="col-lg-6 d-flex align-items-center justify-content-center p-4">
      <div style="max-width:380px; width:100%;">

        <div class="text-center mb-4">
          <i class="ti ti-flower mb-2 d-inline-block" style="color:var(--ce-gold); font-size:30px;"></i>
          <h1 class="login-brand">RUANA</h1>
          <div class="login-rule"></div>
          <p class="text-muted small mb-0">Sistem Administrasi Organisasi Mahasiswa</p>
        </div>

        <?php if (!empty($error)): ?>
          <div class="alert alert-danger py-2 small d-flex align-items-center gap-2">
            <i class="ti ti-alert-circle"></i>
            <?= htmlspecialchars($error) ?>
          </div>
        <?php endif; ?>

        <form action="index.php?page=login&action=login" method="POST">
          <div class="mb-3">
            <label class="form-label small">Username</label>
            <div class="input-group">
              <span class="input-group-text"><i class="ti ti-user"></i></span>
              <input type="text" name="username" class="form-control border-start-0" placeholder="Masukkan username"
                value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>" required autofocus>
            </div>
          </div>

          <div class="mb-4">
            <label class="form-label small">Password</label>
            <div class="input-group">
              <span class="input-group-text"><i class="ti ti-lock"></i></span>
              <input type="password" name="password" id="password" class="form-control border-start-0" placeholder="Masukkan password" required>
              <button class="btn btn-outline-secondary" type="button" id="togglePassword" aria-label="Tampilkan password">
                <i class="ti ti-eye" id="eyeIcon"></i>
              </button>
            </div>
          </div>

          <button type="submit" class="btn w-100 fw-medium py-2" style="background-color:var(--ce-gold); border-color:var(--ce-gold); color:var(--ce-ink);">
            <i class="ti ti-login me-1"></i> Masuk
          </button>
        </form>

      </div>
    </div>

  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  document.getElementById('togglePassword').addEventListener('click', function () {
    const pwd = document.getElementById('password');
    const icon = document.getElementById('eyeIcon');
    if (pwd.type === 'password') {
      pwd.type = 'text';
      icon.className = 'ti ti-eye-off';
    } else {
      pwd.type = 'password';
      icon.className = 'ti ti-eye';
    }
  });
</script>
</body>
</html>
