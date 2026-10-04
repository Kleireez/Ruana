<?php

require_once BASE_PATH . '/app/models/Dokumen.php';
require_once BASE_PATH . '/app/models/Notifikasi.php';
require_once BASE_PATH . '/app/helpers/Access.php';

class DokumenController {

    private $conn;
    private $dokumenModel;
    private $notifModel;

    public function __construct($conn) {
        Access::guard(Access::MODULE_DOKUMEN);
        $this->conn        = $conn;
        $this->dokumenModel = new Dokumen($conn);
        $this->notifModel   = new Notifikasi($conn);
    }

    // Tampilkan daftar dokumen
    public function index() {
        $filterStatus = $_GET['status'] ?? '';
        $keyword      = trim($_GET['q'] ?? '');
        $dokumens = $this->dokumenModel->getAll($filterStatus, $keyword);
        $nama     = $_SESSION['user'];
        $role     = $_SESSION['role'];
        $success  = $_SESSION['success'] ?? null;
        $error    = $_SESSION['error'] ?? null;
        unset($_SESSION['success'], $_SESSION['error']);

        // Ambil notifikasi untuk topbar
        $notifList   = $this->notifModel->getByUser($_SESSION['user_id']);
        $notifUnread = $this->notifModel->countUnread($_SESSION['user_id']);

        require_once BASE_PATH . '/app/views/dokumen/index.php';
    }

    // Form upload dokumen (sekretaris)
    public function create() {
        if ($_SESSION['role'] !== 'sekretaris') {
            $_SESSION['error'] = 'Hanya sekretaris yang dapat mengunggah dokumen.';
            header('Location: index.php?page=dokumen&action=index');
            exit;
        }

        $error = '';
        $nama  = $_SESSION['user'];
        $role  = $_SESSION['role'];
        $notifList   = $this->notifModel->getByUser($_SESSION['user_id']);
        $notifUnread = $this->notifModel->countUnread($_SESSION['user_id']);

        require_once BASE_PATH . '/app/views/dokumen/form.php';
    }

    // Simpan dokumen baru + kirim notif ke semua dosen
    public function store() {
        if ($_SESSION['role'] !== 'sekretaris') {
            $_SESSION['error'] = 'Hanya sekretaris yang dapat mengunggah dokumen.';
            header('Location: index.php?page=dokumen&action=index');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=dokumen&action=create');
            exit;
        }

        $judul           = trim($_POST['judul'] ?? '');
        $kategori        = trim($_POST['kategori'] ?? '');
        $tanggal         = trim($_POST['tanggal'] ?? '');
        $nama_organisasi = trim($_POST['nama_organisasi'] ?? '');
        $nama            = $_SESSION['user'];
        $role            = $_SESSION['role'];
        $notifList       = $this->notifModel->getByUser($_SESSION['user_id']);
        $notifUnread     = $this->notifModel->countUnread($_SESSION['user_id']);

        if (empty($judul) || empty($kategori) || empty($tanggal) || empty($nama_organisasi)) {
            $error = 'Semua field wajib diisi.';
            require_once BASE_PATH . '/app/views/dokumen/form.php';
            return;
        }

        if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            $error = 'File wajib diunggah.';
            require_once BASE_PATH . '/app/views/dokumen/form.php';
            return;
        }

        $file     = $_FILES['file'];
        $ekstensi = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed  = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];

        if (!in_array($ekstensi, $allowed)) {
            $error = 'Format file tidak didukung. Gunakan PDF, DOC, DOCX, JPG, atau PNG.';
            require_once BASE_PATH . '/app/views/dokumen/form.php';
            return;
        }

        $uploadDir = BASE_PATH . '/public/uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        $nama_file   = time() . '_' . preg_replace('/\s+/', '_', $file['name']);
        $upload_path = $uploadDir . $nama_file;

        if (!move_uploaded_file($file['tmp_name'], $upload_path)) {
            $error = 'Gagal mengunggah file. Coba lagi.';
            require_once BASE_PATH . '/app/views/dokumen/form.php';
            return;
        }

        $data = [
            'judul'           => $judul,
            'kategori'        => $kategori,
            'nama_file'       => $nama_file,
            'tanggal'         => $tanggal,
            'user_id'         => $_SESSION['user_id'],
            'nama_organisasi' => $nama_organisasi,
        ];

        $this->dokumenModel->create($data);

        // Kirim notif ke semua dosen
        $dosenList = mysqli_query($this->conn, "SELECT id FROM users WHERE role = 'dosen'");
        while ($dosen = mysqli_fetch_assoc($dosenList)) {
            $this->notifModel->create(
                $dosen['id'],
                'Dokumen Baru Menunggu Approval',
                "{$_SESSION['user']} dari {$nama_organisasi} mengajukan \"{$judul}\".",
                'index.php?page=dokumen&action=approval'
            );
        }

        $_SESSION['success'] = 'Dokumen berhasil diunggah.';
        header('Location: index.php?page=dokumen&action=index');
        exit;
    }

    // Halaman approval (dosen)
    public function approval() {
        if ($_SESSION['role'] !== 'dosen') {
            $_SESSION['error'] = 'Hanya dosen yang dapat mengakses halaman approval.';
            header('Location: index.php?page=dokumen&action=index');
            exit;
        }

        // Tandai notif approval sebagai sudah dibaca saat halaman dibuka
        $this->notifModel->markAllRead($_SESSION['user_id']);

        $dokumens    = $this->dokumenModel->getPending();
        $nama        = $_SESSION['user'];
        $role        = $_SESSION['role'];
        $success     = $_SESSION['success'] ?? null;
        $notifList   = $this->notifModel->getByUser($_SESSION['user_id']);
        $notifUnread = $this->notifModel->countUnread($_SESSION['user_id']);
        unset($_SESSION['success']);

        require_once BASE_PATH . '/app/views/dokumen/approval.php';
    }

    // Proses approve/tolak + kirim notif ke sekretaris
    public function approve() {
        if ($_SESSION['role'] !== 'dosen') {
            $_SESSION['error'] = 'Hanya dosen yang dapat melakukan approval.';
            header('Location: index.php?page=dokumen&action=index');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=dokumen&action=approval');
            exit;
        }

        $id      = (int) ($_POST['id'] ?? 0);
        $status  = $_POST['status'] ?? '';
        $catatan = trim($_POST['catatan'] ?? '');

        if (!in_array($status, ['disetujui', 'ditolak'], true)) {
            header('Location: index.php?page=dokumen&action=approval');
            exit;
        }

        $this->dokumenModel->updateApproval($id, $status, $catatan, $_SESSION['user_id']);

        // Ambil data dokumen untuk notif
        $doc = $this->dokumenModel->findById($id);
        if ($doc) {
            $statusLabel = $status === 'disetujui' ? 'disetujui ✓' : 'ditolak ✗';
            $pesan = "Dokumen \"{$doc['judul']}\" {$statusLabel} oleh {$_SESSION['user']}.";
            if ($catatan) $pesan .= " Catatan: {$catatan}";

            // Kirim notif ke uploader dokumen
            $this->notifModel->create(
                $doc['user_id'],
                'Status Dokumen Diperbarui',
                $pesan,
                'index.php?page=dokumen&action=index'
            );

            // Kirim notif ke semua ketua juga
            $ketuaList = mysqli_query($this->conn, "SELECT id FROM users WHERE role = 'ketua'");
            while ($ketua = mysqli_fetch_assoc($ketuaList)) {
                $this->notifModel->create(
                    $ketua['id'],
                    'Dokumen ' . ucfirst($status),
                    "Dokumen \"{$doc['judul']}\" telah {$statusLabel} oleh dosen.",
                    'index.php?page=dokumen&action=index'
                );
            }
        }

        $_SESSION['success'] = 'Status dokumen berhasil diperbarui.';
        header('Location: index.php?page=dokumen&action=approval');
        exit;
    }

    // Hapus dokumen (sekretaris)
    public function delete() {
        if ($_SESSION['role'] !== 'sekretaris') {
            $_SESSION['error'] = 'Hanya sekretaris yang dapat menghapus dokumen.';
            header('Location: index.php?page=dokumen&action=index');
            exit;
        }

        $id = (int) ($_GET['id'] ?? 0);
        if ($id) {
            $this->dokumenModel->delete($id);
            $_SESSION['success'] = 'Dokumen berhasil dihapus.';
        }

        header('Location: index.php?page=dokumen&action=index');
        exit;
    }

    // Tandai semua notif sebagai sudah dibaca
    public function readNotif() {
        $this->notifModel->markAllRead($_SESSION['user_id']);
        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'index.php?page=dokumen&action=index'));
        exit;
    }
}