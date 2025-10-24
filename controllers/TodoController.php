<?php
require_once (__DIR__ . '/../models/TodoModel.php');

class TodoController
{
    public function index()
    {
        // 1. Tentukan filter yang aktif
        $filter = 'all'; // Default filter
        if (isset($_GET['filter'])) {
            if ($_GET['filter'] === 'finished') {
                $filter = 'finished';
            } elseif ($_GET['filter'] === 'unfinished') {
                $filter = 'unfinished';
            }
        }

        // 2. Panggil model dengan filter yang sesuai
        $todoModel = new TodoModel();
        $todos = $todoModel->getAllTodos($filter); // Teruskan $filter ke model
        
        // 3. Muat view (View akan membutuhkan $todos dan $filter)
        include (__DIR__ . '/../views/TodoView.php');
    }

    public function create()
    {
        // ... (Fungsi ini tetap sama) ...
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = $_POST['title'];
            $description = $_POST['description']; 
            $todoModel = new TodoModel();
            $todoModel->createTodo($title, $description);
        }
        header('Location: index.php');
    }

    public function update()
    {
        // ... (Fungsi ini tetap sama) ...
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $title = $_POST['title'];
            $description = $_POST['description'];
            $is_finished = isset($_POST['is_finished']); 
            $todoModel = new TodoModel();
            $todoModel->updateTodo($id, $title, $description, $is_finished);
        }
        header('Location: index.php');
    }

    public function delete()
    {
        // ... (Fungsi ini tetap sama) ...
        if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
            $id = $_GET['id'];
            $todoModel = new TodoModel();
            $todoModel->deleteTodo($id);
        }
        header('Location: index.php');
    }
}