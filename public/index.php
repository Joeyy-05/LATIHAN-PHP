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
    
    // PENAMBAHAN BARU: Route untuk Detail (POIN 5)
    case 'detail':
        $controller->detail(); // Kita akan buat fungsi 'detail()' ini di Controller
        break;
    
    case 'index':
    default:
        $controller->index();
        break;
}