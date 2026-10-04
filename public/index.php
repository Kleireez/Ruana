<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

define('BASE_PATH', dirname(__DIR__));

require_once BASE_PATH . '/config/database.php';

$page   = isset($_GET['page'])   ? $_GET['page']   : 'home';
$action = isset($_GET['action']) ? $_GET['action'] : 'index';

switch ($page) {

    // Halaman utama - kalau belum login tampil login, kalau sudah login tampil dashboard
    case 'home':
    case 'dashboard':
        if (!isset($_SESSION['user'])) {
            require_once BASE_PATH . '/app/controllers/AuthController.php';
            $controller = new AuthController($conn);
            $controller->index();
        } else {
            require_once BASE_PATH . '/app/controllers/AuthController.php';
            $controller = new AuthController($conn);
            $controller->dashboard();
        }
        break;

    case 'login':
        require_once BASE_PATH . '/app/controllers/AuthController.php';
        $controller = new AuthController($conn);
        if ($action === 'login') {
            $controller->login();
        } elseif ($action === 'logout') {
            $controller->logout();
        } else {
            $controller->index();
        }
        break;

    case 'anggota':
        if (!isset($_SESSION['user'])) { header('Location: index.php'); exit; }
        require_once BASE_PATH . '/app/controllers/AnggotaController.php';
        $controller = new AnggotaController($conn);
        if ($action === 'store')       $controller->store();
        elseif ($action === 'edit')    $controller->edit();
        elseif ($action === 'update')  $controller->update();
        elseif ($action === 'delete')  $controller->delete();
        elseif ($action === 'create')  $controller->create();
        else                           $controller->index();
        break;

    case 'keuangan':
        if (!isset($_SESSION['user'])) { header('Location: index.php'); exit; }
        require_once BASE_PATH . '/app/controllers/KeuanganController.php';
        $controller = new KeuanganController($conn);
        if ($action === 'store')       $controller->store();
        elseif ($action === 'delete')  $controller->delete();
        elseif ($action === 'create')  $controller->create();
        else                           $controller->index();
        break;

    case 'dokumen':
        if (!isset($_SESSION['user'])) { header('Location: index.php'); exit; }
        require_once BASE_PATH . '/app/controllers/DokumenController.php';
        $controller = new DokumenController($conn);
        if ($action === 'create')        $controller->create();
        elseif ($action === 'store')     $controller->store();
        elseif ($action === 'approval')  $controller->approval();
        elseif ($action === 'approve')   $controller->approve();
        elseif ($action === 'delete')    $controller->delete();
        elseif ($action === 'readNotif') $controller->readNotif();
        else                             $controller->index();
        break;

    default:
        header('Location: index.php');
        exit;
}