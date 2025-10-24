<?php
session_start(); // MEMULAI SESSION (PENTING UNTUK ERROR HANDLING)

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
    case 'index':
    default:
        $controller->index();
        break;
}