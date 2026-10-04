<?php

class Notifikasi {

    private $conn;
    private $table = 'notifikasi';

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Ambil semua notif milik user (max 10 terbaru)
    public function getByUser($user_id) {
        $user_id = (int) $user_id;
        $query = "SELECT * FROM {$this->table}
                  WHERE user_id = $user_id
                  ORDER BY created_at DESC
                  LIMIT 10";
        $result = mysqli_query($this->conn, $query);
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    // Hitung notif yang belum dibaca
    public function countUnread($user_id) {
        $user_id = (int) $user_id;
        $result = mysqli_query($this->conn,
            "SELECT COUNT(*) AS total FROM {$this->table}
             WHERE user_id = $user_id AND is_read = 0"
        );
        return mysqli_fetch_assoc($result)['total'] ?? 0;
    }

    // Buat notif baru
    public function create($user_id, $judul, $pesan, $link = '') {
        $user_id = (int) $user_id;
        $judul   = mysqli_real_escape_string($this->conn, $judul);
        $pesan   = mysqli_real_escape_string($this->conn, $pesan);
        $link    = mysqli_real_escape_string($this->conn, $link);

        $query = "INSERT INTO {$this->table} (user_id, judul, pesan, link)
                  VALUES ($user_id, '$judul', '$pesan', '$link')";
        return mysqli_query($this->conn, $query);
    }

    // Tandai semua notif user sebagai sudah dibaca
    public function markAllRead($user_id) {
        $user_id = (int) $user_id;
        return mysqli_query($this->conn,
            "UPDATE {$this->table} SET is_read = 1 WHERE user_id = $user_id"
        );
    }

    // Tandai satu notif sebagai sudah dibaca
    public function markRead($id) {
        $id = (int) $id;
        return mysqli_query($this->conn,
            "UPDATE {$this->table} SET is_read = 1 WHERE id = $id"
        );
    }

    // Hapus semua notif user
    public function deleteAll($user_id) {
        $user_id = (int) $user_id;
        return mysqli_query($this->conn,
            "DELETE FROM {$this->table} WHERE user_id = $user_id"
        );
    }
}