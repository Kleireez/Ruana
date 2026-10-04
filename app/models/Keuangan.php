<?php

class Keuangan {

    private $conn;
    private $table = 'keuangan';

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Ambil semua transaksi
    public function getAll() {
        $query = "SELECT k.*, u.nama AS dicatat_oleh
                  FROM {$this->table} k
                  LEFT JOIN users u ON k.user_id = u.id
                  ORDER BY k.tanggal DESC, k.created_at DESC";
        $result = mysqli_query($this->conn, $query);
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    // Ambil transaksi berdasarkan id
    public function findById($id) {
        $id = (int) $id;
        $query = "SELECT * FROM {$this->table} WHERE id = $id LIMIT 1";
        $result = mysqli_query($this->conn, $query);
        return mysqli_num_rows($result) > 0 ? mysqli_fetch_assoc($result) : null;
    }

    // Total pemasukan
    public function totalPemasukan() {
        $result = mysqli_query($this->conn, "SELECT SUM(jumlah) AS total FROM {$this->table} WHERE jenis = 'pemasukan'");
        return mysqli_fetch_assoc($result)['total'] ?? 0;
    }

    // Total pengeluaran
    public function totalPengeluaran() {
        $result = mysqli_query($this->conn, "SELECT SUM(jumlah) AS total FROM {$this->table} WHERE jenis = 'pengeluaran'");
        return mysqli_fetch_assoc($result)['total'] ?? 0;
    }

    // Tambah transaksi baru
    public function create($data) {
        $jenis       = mysqli_real_escape_string($this->conn, $data['jenis']);
        $keterangan  = mysqli_real_escape_string($this->conn, $data['keterangan']);
        $jumlah      = (float) $data['jumlah'];
        $tanggal     = mysqli_real_escape_string($this->conn, $data['tanggal']);
        $user_id     = (int) $data['user_id'];

        $query = "INSERT INTO {$this->table} (user_id, jenis, keterangan, jumlah, tanggal)
                  VALUES ($user_id, '$jenis', '$keterangan', $jumlah, '$tanggal')";
        return mysqli_query($this->conn, $query);
    }

    // Hapus transaksi
    public function delete($id) {
        $id = (int) $id;
        return mysqli_query($this->conn, "DELETE FROM {$this->table} WHERE id = $id");
    }
}