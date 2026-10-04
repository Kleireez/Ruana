<?php
/**
 * Access.php
 * -----------------------------------------------------------------
 * Pusat aturan hak akses (RBAC sederhana) untuk sistem RUANA.
 *
 * Aturan berdasarkan dokumen requirement (Bab 7 - Kebutuhan Pengguna):
 *   - ketua      : akses SELURUH fitur (anggota, keuangan, dokumen)
 *   - sekretaris : akses fitur ANGGOTA saja
 *   - bendahara  : akses fitur KEUANGAN saja
 *   - dosen      : akses fitur DOKUMEN saja (approval)
 *   - anggota    : role default tanpa modul pengelolaan (hanya dashboard)
 *
 * Tabel ini sengaja dipisah dari controller/router supaya jika suatu
 * saat aturan berubah, cukup diubah di SATU tempat ini saja.
 * -----------------------------------------------------------------
 */

class Access {

    // Daftar modul yang ada di sistem
    const MODULE_ANGGOTA  = 'anggota';
    const MODULE_KEUANGAN = 'keuangan';
    const MODULE_DOKUMEN  = 'dokumen';

    /**
     * Peta role -> daftar modul yang boleh diakses.
     * 'ketua' diberi tanda khusus '*' yang berarti akses ke semua modul.
     */
    private static $map = [
        'ketua'      => ['*'],
        'sekretaris' => [self::MODULE_ANGGOTA, self::MODULE_DOKUMEN],
        'bendahara'  => [self::MODULE_KEUANGAN],
        'dosen'      => [self::MODULE_DOKUMEN],
        'anggota'    => [], // anggota biasa: tidak ada modul pengelolaan (hanya dashboard)
    ];

    /**
     * Cek apakah sebuah role boleh mengakses modul tertentu.
     */
    public static function canAccess(string $role, string $module): bool {
        $role = strtolower(trim($role));

        if (!isset(self::$map[$role])) {
            return false; // role tidak dikenal -> tolak
        }

        $allowed = self::$map[$role];

        if (in_array('*', $allowed, true)) {
            return true; // ketua: akses semua modul
        }

        return in_array($module, $allowed, true);
    }

    /**
     * Ambil daftar modul yang boleh diakses oleh sebuah role.
     * Berguna untuk menampilkan menu/sidebar secara dinamis.
     *
     * @return string[] daftar nama modul, atau ['*'] untuk semua modul
     */
    public static function allowedModules(string $role): array {
        $role = strtolower(trim($role));
        return self::$map[$role] ?? [];
    }

    /**
     * Helper untuk dipakai di view: true jika role berhak ke modul ini.
     */
    public static function menuVisible(string $role, string $module): bool {
        return self::canAccess($role, $module);
    }

    /**
     * Jalankan pengecekan akses untuk satu request.
     * Jika TIDAK berhak: set pesan error di session, redirect ke
     * dashboard, lalu hentikan eksekusi (exit).
     *
     * Dipanggil dari router (public/index.php) SEBELUM controller
     * dijalankan, supaya tidak bisa ditembus lewat akses URL langsung.
     */
    public static function guard(string $module): void {
        $role = $_SESSION['role'] ?? null;

        if (!$role || !self::canAccess($role, $module)) {
            $_SESSION['error'] = 'Anda tidak memiliki hak akses untuk membuka halaman tersebut.';
            header('Location: index.php?page=dashboard');
            exit;
        }
    }
}