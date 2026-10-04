<?php

class Anggota {

    private $conn;
    private $table = 'anggota';

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Ambil semua anggota (join ke users untuk tahu siapa yang menambahkan)
    public function getAll() {
        $query = "SELECT a.*, u.nama AS dicatat_oleh
                  FROM {$this->table} a
                  LEFT JOIN users u ON a.user_id = u.id
                  ORDER BY a.created_at DESC";
        $result = mysqli_query($this->conn, $query);
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    // Ambil 1 anggota berdasarkan id
    public function findById($id) {
        $id = (int) $id;
        $query = "SELECT * FROM {$this->table} WHERE id = $id LIMIT 1";
        $result = mysqli_query($this->conn, $query);

        if (mysqli_num_rows($result) > 0) {
            return mysqli_fetch_assoc($result);
        }

        return null;
    }

    // Cek apakah NIM sudah ada (untuk validasi unique), $excludeId untuk dilewati saat update
    public function nimExists($nim, $excludeId = null) {
        $nim = mysqli_real_escape_string($this->conn, $nim);
        $query = "SELECT id FROM {$this->table} WHERE nim = '$nim'";
        if ($excludeId) {
            $query .= " AND id != " . (int) $excludeId;
        }
        $result = mysqli_query($this->conn, $query);
        return mysqli_num_rows($result) > 0;
    }

    // Tambah anggota baru
    public function create($data) {
        $user_id = (int) $data['user_id'];
        $nim     = mysqli_real_escape_string($this->conn, $data['nim']);
        $nama    = mysqli_real_escape_string($this->conn, $data['nama']);
        $email   = mysqli_real_escape_string($this->conn, $data['email']);
        $no_hp   = mysqli_real_escape_string($this->conn, $data['no_hp']);
        $jabatan = mysqli_real_escape_string($this->conn, $data['jabatan']);
        $status  = mysqli_real_escape_string($this->conn, $data['status']);

        $query = "INSERT INTO {$this->table} (user_id, nim, nama, email, no_hp, jabatan, status)
                  VALUES ($user_id, '$nim', '$nama', '$email', '$no_hp', '$jabatan', '$status')";

        return mysqli_query($this->conn, $query);
    }

    // Update data anggota
    public function update($id, $data) {
        $id      = (int) $id;
        $nim     = mysqli_real_escape_string($this->conn, $data['nim']);
        $nama    = mysqli_real_escape_string($this->conn, $data['nama']);
        $email   = mysqli_real_escape_string($this->conn, $data['email']);
        $no_hp   = mysqli_real_escape_string($this->conn, $data['no_hp']);
        $jabatan = mysqli_real_escape_string($this->conn, $data['jabatan']);
        $status  = mysqli_real_escape_string($this->conn, $data['status']);

        $query = "UPDATE {$this->table} SET
                    nim = '$nim',
                    nama = '$nama',
                    email = '$email',
                    no_hp = '$no_hp',
                    jabatan = '$jabatan',
                    status = '$status'
                  WHERE id = $id";

        return mysqli_query($this->conn, $query);
    }

    // Hapus anggota
    public function delete($id) {
        $id = (int) $id;
        $query = "DELETE FROM {$this->table} WHERE id = $id";
        return mysqli_query($this->conn, $query);
    }

    // Hitung total anggota aktif (untuk dashboard)
    public function countAktif() {
        $query = "SELECT COUNT(*) AS total FROM {$this->table} WHERE status = 'aktif'";
        $result = mysqli_query($this->conn, $query);
        $row = mysqli_fetch_assoc($result);
        return $row['total'] ?? 0;
    }
}