<?php

require_once BASE_PATH . '/app/models/User.php';
require_once BASE_PATH . '/app/helpers/Access.php';

class AuthController {

    private $conn;
    private $userModel;

    public function __construct($conn) {
        $this->conn = $conn;
        $this->userModel = new User($conn);
    }

    public function index() {
        if (isset($_SESSION['user'])) {
            header('Location: index.php');
            exit;
        }

        if (isset($_COOKIE['ruana_user']) && isset($_COOKIE['ruana_role'])) {
            $_SESSION['user'] = $_COOKIE['ruana_user'];
            $_SESSION['role'] = $_COOKIE['ruana_role'];
            header('Location: index.php');
            exit;
        }

        $error = '';
        require_once BASE_PATH . '/app/views/auth/login.php';
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php');
            exit;
        }

        $username = trim($_POST['username']);
        $password = trim($_POST['password']);
        $remember = isset($_POST['remember']);

        if (empty($username) || empty($password)) {
            $error = 'Username dan password wajib diisi.';
            require_once BASE_PATH . '/app/views/auth/login.php';
            return;
        }

        $user = $this->userModel->findByUsername($username);

        if (!$user || $user['password'] !== $password) {
            $error = 'Username atau password salah.';
            require_once BASE_PATH . '/app/views/auth/login.php';
            return;
        }

        $_SESSION['user']    = $user['nama'];
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role']    = $user['role'];

        if ($remember) {
            setcookie('ruana_user', $user['nama'], time() + (7 * 24 * 60 * 60), '/');
            setcookie('ruana_role', $user['role'], time() + (7 * 24 * 60 * 60), '/');
        }

        header('Location: index.php');
        exit;
    }

    public function dashboard() {
        $nama    = $_SESSION['user'];
        $role    = $_SESSION['role'];
        $success = $_SESSION['success'] ?? null;
        $error   = $_SESSION['error'] ?? null;
        unset($_SESSION['success'], $_SESSION['error']);

        require_once BASE_PATH . '/app/models/Notifikasi.php';
        $notifModel  = new Notifikasi($this->conn);
        $notifList   = $notifModel->getByUser($_SESSION['user_id']);
        $notifUnread = $notifModel->countUnread($_SESSION['user_id']);

        // Hitung jumlah data untuk kartu statistik dashboard
        $totalAnggota = 0;
        $totalPemasukan = 0;
        $totalPengeluaran = 0;
        $totalDokumen = 0;

        $resAnggota = mysqli_query($this->conn, "SELECT COUNT(*) AS jumlah FROM anggota");
        if ($resAnggota) {
            $totalAnggota = mysqli_fetch_assoc($resAnggota)['jumlah'];
        }

        $resPemasukan = mysqli_query($this->conn, "SELECT SUM(jumlah) AS total FROM keuangan WHERE jenis = 'pemasukan'");
        if ($resPemasukan) {
            $totalPemasukan = mysqli_fetch_assoc($resPemasukan)['total'] ?? 0;
        }

        $resPengeluaran = mysqli_query($this->conn, "SELECT SUM(jumlah) AS total FROM keuangan WHERE jenis = 'pengeluaran'");
        if ($resPengeluaran) {
            $totalPengeluaran = mysqli_fetch_assoc($resPengeluaran)['total'] ?? 0;
        }

        $resDokumen = mysqli_query($this->conn, "SELECT COUNT(*) AS jumlah FROM dokumen");
        if ($resDokumen) {
            $totalDokumen = mysqli_fetch_assoc($resDokumen)['jumlah'];
        }

        require_once BASE_PATH . '/app/views/dashboard.php';
    }

    public function logout() {
        session_unset();
        session_destroy();
        setcookie('ruana_user', '', time() - 3600, '/');
        setcookie('ruana_role', '', time() - 3600, '/');
        header('Location: index.php');
        exit;
    }
}