<!-- LONCENG NOTIFIKASI -->
<div class="dropdown">
  <button class="btn btn-light btn-icon btn-sm position-relative" type="button" data-bs-toggle="dropdown" id="notifBtn"
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
        <span class="badge rounded-pill" style="background-color:<?= $color['bg'] ?>; color:<?= $color['primary'] ?>; font-size:10px;">
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
            <div class="icon-shape rounded-circle flex-shrink-0 d-flex align-items-center justify-content-center"
                 style="width:32px; height:32px; background:<?= $color['bg'] ?>;">
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