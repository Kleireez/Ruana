<?php

require_once BASE_PATH . '/app/models/Anggota.php';
require_once BASE_PATH . '/app/models/Notifikasi.php';
require_once BASE_PATH . '/app/helpers/Access.php';

class AnggotaController {

    private $conn;
    private $anggotaModel;
    private $notifModel;

    public function __construct($conn) {
        // Pertahanan berlapis: meski router sudah memvalidasi akses,
        // controller tetap menolak akses jika dipanggil dari jalur lain.
        Access::guard(Access::MODULE_ANGGOTA);

        $this->conn = $conn;
        $this->anggotaModel = new Anggota($conn);
        $this->notifModel   = new Notifikasi($conn);
    }

    // Tampilkan daftar anggota
    public function index() {
        $anggota = $this->anggotaModel->getAll();
        $nama    = $_SESSION['user'];
        $role    = $_SESSION['role'];
        $success = $_SESSION['success'] ?? null;
        $error   = $_SESSION['error'] ?? null;
        unset($_SESSION['success'], $_SESSION['error']);

        $notifList   = $this->notifModel->getByUser($_SESSION['user_id']);
        $notifUnread = $this->notifModel->countUnread($_SESSION['user_id']);
        require_once BASE_PATH . '/app/views/anggota/index.php';
    }

    // Tampilkan form tambah anggota
    public function create() {
        $anggota = null;
        $error = '';
        $nama  = $_SESSION['user'];
        $role  = $_SESSION['role'];
        $notifList   = $this->notifModel->getByUser($_SESSION['user_id']);
        $notifUnread = $this->notifModel->countUnread($_SESSION['user_id']);

        require_once BASE_PATH . '/app/views/anggota/form.php';
    }

    // Simpan anggota baru
    public function store() {
        if ($_SESSION['role'] !== 'sekretaris' && $_SESSION['role'] !== 'ketua') {
            $_SESSION['error'] = 'Hanya sekretaris dan ketua yang dapat menambahkan anggota.';
            header('Location: index.php?page=anggota&action=index');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=anggota&action=index');
            exit;
        }

        $data = [
            'user_id' => $_SESSION['user_id'],
            'nim'     => trim($_POST['nim']),
            'nama'    => trim($_POST['nama']),
            'email'   => trim($_POST['email']),
            'no_hp'   => trim($_POST['no_hp']),
            'jabatan' => trim($_POST['jabatan']),
            'status'  => $_POST['status'],
        ];

        if (empty($data['nim']) || empty($data['nama'])) {
            $error   = 'NIM dan Nama wajib diisi.';
            $anggota = $data;
            $nama    = $_SESSION['user'];
            $role    = $_SESSION['role'];
            require_once BASE_PATH . '/app/views/anggota/form.php';
            return;
        }

        if ($this->anggotaModel->nimExists($data['nim'])) {
            $error   = 'NIM sudah terdaftar, gunakan NIM lain.';
            $anggota = $data;
            $nama    = $_SESSION['user'];
            $role    = $_SESSION['role'];
            require_once BASE_PATH . '/app/views/anggota/form.php';
            return;
        }

        $this->anggotaModel->create($data);

        $_SESSION['success'] = 'Anggota baru berhasil ditambahkan.';
        header('Location: index.php?page=anggota&action=index');
        exit;
    }

    // Tampilkan form edit anggota
    public function edit() {
        $id      = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        $anggota = $this->anggotaModel->findById($id);

        if (!$anggota) {
            $_SESSION['error'] = 'Data anggota tidak ditemukan.';
            header('Location: index.php?page=anggota&action=index');
            exit;
        }

        $error = '';
        $nama  = $_SESSION['user'];
        $role  = $_SESSION['role'];
        $notifList   = $this->notifModel->getByUser($_SESSION['user_id']);
        $notifUnread = $this->notifModel->countUnread($_SESSION['user_id']);

        require_once BASE_PATH . '/app/views/anggota/form.php';
    }

    // Update data anggota
    public function update() {
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$id) {
            header('Location: index.php?page=anggota&action=index');
            exit;
        }

        $data = [
            'nim'     => trim($_POST['nim']),
            'nama'    => trim($_POST['nama']),
            'email'   => trim($_POST['email']),
            'no_hp'   => trim($_POST['no_hp']),
            'jabatan' => trim($_POST['jabatan']),
            'status'  => $_POST['status'],
        ];

        if (empty($data['nim']) || empty($data['nama'])) {
            $error   = 'NIM dan Nama wajib diisi.';
            $anggota = array_merge(['id' => $id], $data);
            $nama    = $_SESSION['user'];
            $role    = $_SESSION['role'];
            require_once BASE_PATH . '/app/views/anggota/form.php';
            return;
        }

        if ($this->anggotaModel->nimExists($data['nim'], $id)) {
            $error   = 'NIM sudah digunakan oleh anggota lain.';
            $anggota = array_merge(['id' => $id], $data);
            $nama    = $_SESSION['user'];
            $role    = $_SESSION['role'];
            require_once BASE_PATH . '/app/views/anggota/form.php';
            return;
        }

        $this->anggotaModel->update($id, $data);

        $_SESSION['success'] = 'Data anggota berhasil diperbarui.';
        header('Location: index.php?page=anggota&action=index');
        exit;
    }

    // Hapus anggota
    public function delete() {
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

        if ($id) {
            $this->anggotaModel->delete($id);
            $_SESSION['success'] = 'Anggota berhasil dihapus.';
        }

        header('Location: index.php?page=anggota&action=index');
        exit;
    }
}