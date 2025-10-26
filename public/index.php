<?php
session_start(); // (Sudah ada dari Poin 4)

require_once (__DIR__ . '/../controllers/TodoController.php');

$page = isset($_GET['page']) ? $_GET['page'] : 'index';
$controller = new TodoController();

switch ($page) {
    case 'create':
        $controller->create();
        break;
    case 'update':
        $controller->update();
        break;
    case 'delete':
        $controller->delete();
        break;
    case 'detail':
        $controller->detail();
        break;

    // PENAMBAHAN BARU: Route untuk Sorting (POIN 6)
    case 'updateOrder':
        $controller->updateOrder(); // Kita akan buat fungsi 'updateOrder()' ini
        break;
    
    case 'index':
    default:
        $controller->index();
        break;
}