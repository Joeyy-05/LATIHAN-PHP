<?php
require_once (__DIR__ . '/../models/TodoModel.php');

class TodoController
{
    public function index()
    {
        // 1. Tentukan filter yang aktif (dari Poin 2)
        $filter = 'all'; // Default filter
        if (isset($_GET['filter'])) {
            if ($_GET['filter'] === 'finished') {
                $filter = 'finished';
            } elseif ($_GET['filter'] === 'unfinished') {
                $filter = 'unfinished';
            }
        }

        // 2. Tentukan kata kunci pencarian (dari Poin 3)
        $search = ''; // Default tidak ada pencarian
        if (isset($_GET['search'])) {
            $search = trim($_GET['search']); // trim() untuk menghapus spasi berlebih
        }

        // 3. Panggil model dengan filter DAN search yang sesuai
        $todoModel = new TodoModel();
        $todos = $todoModel->getAllTodos($filter, $search); // Teruskan $filter dan $search
        
        // 4. Muat view (View akan membutuhkan $todos, $filter, dan $search)
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
        // Redirect untuk membersihkan POST dan GET
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
        // Redirect untuk membersihkan POST
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