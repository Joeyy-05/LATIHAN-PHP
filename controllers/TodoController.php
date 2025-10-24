<?php
require_once (__DIR__ . '/../models/TodoModel.php');

class TodoController
{
    public function index()
    {
        $todoModel = new TodoModel();
        $todos = $todoModel->getAllTodos();
        include (__DIR__ . '/../views/TodoView.php');
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Mengambil 'title' dan 'description' dari form (sebelumnya 'activity')
            $title = $_POST['title'];
            $description = $_POST['description']; // Menambahkan field baru
            
            $todoModel = new TodoModel();
            
            // Memanggil fungsi model yang baru
            $todoModel->createTodo($title, $description);
        }
        header('Location: index.php');
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            
            // Mengambil data baru dari form
            $title = $_POST['title'];
            $description = $_POST['description'];
            
            // Cara menangani Checkbox (boolean)
            // Jika checkbox 'is_finished' dicentang, 'isset' akan true.
            // Jika tidak dicentang, 'isset' akan false.
            $is_finished = isset($_POST['is_finished']); 
            
            $todoModel = new TodoModel();
            
            // Memanggil fungsi update model yang baru
            $todoModel->updateTodo($id, $title, $description, $is_finished);
        }
        header('Location: index.php');
    }

    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
            $id = $_GET['id'];
            $todoModel = new TodoModel();
            $todoModel->deleteTodo($id);
        }
        header('Location: index.php');
    }
}