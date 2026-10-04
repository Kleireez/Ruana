<?php

class User {

    private $conn;
    private $table = 'users';

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Cari user berdasarkan username
    public function findByUsername($username) {
        $username = mysqli_real_escape_string($this->conn, $username);
        $query = "SELECT * FROM {$this->table} WHERE username = '$username' LIMIT 1";
        $result = mysqli_query($this->conn, $query);

        if (mysqli_num_rows($result) > 0) {
            return mysqli_fetch_assoc($result);
        }

        return null;
    }

    // Ambil semua user
    public function getAll() {
        $query = "SELECT id, nama, username, role, created_at FROM {$this->table} ORDER BY created_at DESC";
        $result = mysqli_query($this->conn, $query);
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    // Ambil user berdasarkan id
    public function findById($id) {
        $id = (int) $id;
        $query = "SELECT * FROM {$this->table} WHERE id = $id LIMIT 1";
        $result = mysqli_query($this->conn, $query);

        if (mysqli_num_rows($result) > 0) {
            return mysqli_fetch_assoc($result);
        }

        return null;
    }
}