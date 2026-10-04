<?php

class Dokumen {

    private $conn;
    private $table = 'dokumen';

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Ambil semua dokumen beserta nama pengunggah & penyetuju
    // $status : kosong/null = semua, atau 'pending'/'disetujui'/'ditolak' untuk filter
    // $keyword: opsional, mencari di judul, kategori, nama organisasi, dan nama pengunggah
    public function getAll($status = null, $keyword = null) {
        $query = "SELECT d.*, u.nama AS nama_uploader, a.nama AS nama_approver
                  FROM {$this->table} d
                  LEFT JOIN users u ON d.user_id = u.id
                  LEFT JOIN users a ON d.approved_by = a.id";

        $where = [];

        if (!empty($status) && in_array($status, ['pending', 'disetujui', 'ditolak'], true)) {
            $status = mysqli_real_escape_string($this->conn, $status);
            $where[] = "d.status_approval = '$status'";
        }

        if (!empty(trim((string) $keyword))) {
            $kw = mysqli_real_escape_string($this->conn, trim($keyword));
            $where[] = "(d.judul LIKE '%$kw%' OR d.kategori LIKE '%$kw%' OR d.nama_organisasi LIKE '%$kw%' OR u.nama LIKE '%$kw%')";
        }

        if (!empty($where)) {
            $query .= " WHERE " . implode(' AND ', $where);
        }

        $query .= " ORDER BY d.created_at DESC";

        $result = mysqli_query($this->conn, $query);
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    // Ambil satu dokumen berdasarkan id
    public function findById($id) {
        $id = (int) $id;
        $query = "SELECT d.*, u.nama AS nama_uploader, a.nama AS nama_approver
                  FROM {$this->table} d
                  LEFT JOIN users u ON d.user_id = u.id
                  LEFT JOIN users a ON d.approved_by = a.id
                  WHERE d.id = $id LIMIT 1";
        $result = mysqli_query($this->conn, $query);
        return mysqli_num_rows($result) > 0 ? mysqli_fetch_assoc($result) : null;
    }

    // Ambil dokumen berstatus pending saja (untuk halaman approval dosen)
    public function getPending() {
        $query = "SELECT d.*, u.nama AS nama_uploader
                  FROM {$this->table} d
                  LEFT JOIN users u ON d.user_id = u.id
                  WHERE d.status_approval = 'pending'
                  ORDER BY d.created_at DESC";
        $result = mysqli_query($this->conn, $query);
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    // Simpan dokumen baru (dipanggil sekretaris setelah upload file)
    public function create($data) {
        $judul           = mysqli_real_escape_string($this->conn, $data['judul']);
        $kategori        = mysqli_real_escape_string($this->conn, $data['kategori']);
        $nama_file       = mysqli_real_escape_string($this->conn, $data['nama_file']);
        $tanggal         = mysqli_real_escape_string($this->conn, $data['tanggal']);
        $nama_organisasi = mysqli_real_escape_string($this->conn, $data['nama_organisasi']);
        $user_id         = (int) $data['user_id'];

        $query = "INSERT INTO {$this->table} (user_id, nama_organisasi, judul, kategori, nama_file, tanggal, status_approval)
                  VALUES ($user_id, '$nama_organisasi', '$judul', '$kategori', '$nama_file', '$tanggal', 'pending')";
        return mysqli_query($this->conn, $query);
    }

    // Dosen menyetujui atau menolak dokumen
    public function updateApproval($id, $status, $catatan, $approved_by) {
        $id          = (int) $id;
        $status      = mysqli_real_escape_string($this->conn, $status);
        $catatan     = mysqli_real_escape_string($this->conn, $catatan);
        $approved_by = (int) $approved_by;

        $query = "UPDATE {$this->table}
                  SET status_approval = '$status',
                      catatan_dosen   = '$catatan',
                      approved_by     = $approved_by,
                      approved_at     = NOW()
                  WHERE id = $id";
        return mysqli_query($this->conn, $query);
    }

    // Hapus dokumen beserta file fisiknya
    public function delete($id) {
        $id  = (int) $id;
        $doc = $this->findById($id);

        if ($doc) {
            $file = BASE_PATH . '/public/uploads/' . $doc['nama_file'];
            if (file_exists($file)) {
                unlink($file);
            }
        }

        $query = "DELETE FROM {$this->table} WHERE id = $id";
        return mysqli_query($this->conn, $query);
    }
}