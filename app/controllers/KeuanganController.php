<?php

require_once BASE_PATH . '/app/models/Keuangan.php';
require_once BASE_PATH . '/app/models/Notifikasi.php';
require_once BASE_PATH . '/app/helpers/Access.php';

class KeuanganController {

    private $conn;
    private $keuanganModel;
    private $notifModel;

    public function __construct($conn) {
        Access::guard(Access::MODULE_KEUANGAN);
        $this->conn          = $conn;
        $this->keuanganModel = new Keuangan($conn);
        $this->notifModel    = new Notifikasi($conn);
    }

    public function index() {
        $transaksi        = $this->keuanganModel->getAll();
        $totalPemasukan   = $this->keuanganModel->totalPemasukan();
        $totalPengeluaran = $this->keuanganModel->totalPengeluaran();
        $saldo            = $totalPemasukan - $totalPengeluaran;
        $nama             = $_SESSION['user'];
        $role             = $_SESSION['role'];
        $success          = $_SESSION['success'] ?? null;
        $error            = $_SESSION['error'] ?? null;
        $notifList        = $this->notifModel->getByUser($_SESSION['user_id']);
        $notifUnread      = $this->notifModel->countUnread($_SESSION['user_id']);
        unset($_SESSION['success'], $_SESSION['error']);

        require_once BASE_PATH . '/app/views/keuangan/index.php';
    }

    public function create() {
        if ($_SESSION['role'] !== 'bendahara') {
            $_SESSION['error'] = 'Hanya bendahara yang dapat menambah transaksi.';
            header('Location: index.php?page=keuangan&action=index');
            exit;
        }

        $error       = '';
        $nama        = $_SESSION['user'];
        $role        = $_SESSION['role'];
        $notifList   = $this->notifModel->getByUser($_SESSION['user_id']);
        $notifUnread = $this->notifModel->countUnread($_SESSION['user_id']);

        require_once BASE_PATH . '/app/views/keuangan/form.php';
    }

    public function store() {
        if ($_SESSION['role'] !== 'bendahara') {
            $_SESSION['error'] = 'Hanya bendahara yang dapat menambah transaksi.';
            header('Location: index.php?page=keuangan&action=index');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=keuangan&action=create');
            exit;
        }

        $jenis      = trim($_POST['jenis'] ?? '');
        $keterangan = trim($_POST['keterangan'] ?? '');
        $jumlah     = $_POST['jumlah'] ?? 0;
        $tanggal    = trim($_POST['tanggal'] ?? '');
        $nama       = $_SESSION['user'];
        $role       = $_SESSION['role'];
        $notifList  = $this->notifModel->getByUser($_SESSION['user_id']);
        $notifUnread = $this->notifModel->countUnread($_SESSION['user_id']);

        if (empty($jenis) || empty($keterangan) || empty($jumlah) || empty($tanggal)) {
            $error = 'Semua field wajib diisi.';
            require_once BASE_PATH . '/app/views/keuangan/form.php';
            return;
        }

        if (!in_array($jenis, ['pemasukan', 'pengeluaran'], true)) {
            $error = 'Jenis transaksi tidak valid.';
            require_once BASE_PATH . '/app/views/keuangan/form.php';
            return;
        }

        if (!is_numeric($jumlah) || $jumlah <= 0) {
            $error = 'Jumlah harus berupa angka lebih dari 0.';
            require_once BASE_PATH . '/app/views/keuangan/form.php';
            return;
        }

        $data = [
            'jenis'      => $jenis,
            'keterangan' => $keterangan,
            'jumlah'     => $jumlah,
            'tanggal'    => $tanggal,
            'user_id'    => $_SESSION['user_id'],
        ];

        $this->keuanganModel->create($data);
        $_SESSION['success'] = 'Transaksi berhasil dicatat.';
        header('Location: index.php?page=keuangan&action=index');
        exit;
    }

    public function delete() {
        if ($_SESSION['role'] !== 'bendahara') {
            $_SESSION['error'] = 'Hanya bendahara yang dapat menghapus transaksi.';
            header('Location: index.php?page=keuangan&action=index');
            exit;
        }

        $id = (int) ($_GET['id'] ?? 0);
        if ($id) {
            $this->keuanganModel->delete($id);
            $_SESSION['success'] = 'Transaksi berhasil dihapus.';
        }

        header('Location: index.php?page=keuangan&action=index');
        exit;
    }
}