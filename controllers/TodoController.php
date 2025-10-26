<?php
require_once (__DIR__ . '/../models/TodoModel.php');

class TodoController
{
    public function index()
    {
        // ... (Fungsi index tetap sama seperti Poin 3) ...
        $filter = 'all'; 
        if (isset($_GET['filter'])) {
            if ($_GET['filter'] === 'finished') {
                $filter = 'finished';
            } elseif ($_GET['filter'] === 'unfinished') {
                $filter = 'unfinished';
            }
        }
        $search = ''; 
        if (isset($_GET['search'])) {
            $search = trim($_GET['search']); 
        }
        $todoModel = new TodoModel();
        $todos = $todoModel->getAllTodos($filter, $search); 
        include (__DIR__ . '/../views/TodoView.php');
    }

    /**
     * PENAMBAHAN BARU: Fungsi untuk Detail (POIN 5)
     */
    public function detail()
    {
        // 1. Pastikan ID ada di URL
        if (!isset($_GET['id'])) {
            $_SESSION['error'] = 'Error: ID Todo tidak ditemukan.';
            header('Location: index.php');
            return;
        }

        $id = $_GET['id'];
        $todoModel = new TodoModel();
        
        // 2. Ambil data todo berdasarkan ID
        $todo = $todoModel->getTodoById($id);

        // 3. Cek apakah todo ditemukan
        if (!$todo) {
            $_SESSION['error'] = 'Error: Data Todo tidak ditemukan.';
            header('Location: index.php');
            return;
        }

        // 4. Jika ditemukan, muat file view baru (TodoDetailView.php)
        //    dan kirimkan data $todo ke view tersebut.
        include (__DIR__ . '/../views/TodoDetailView.php');
    }

    public function create()
    {
        // ... (Fungsi create tetap sama seperti Poin 4) ...
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title']); 
            $description = $_POST['description']; 
            $todoModel = new TodoModel();
            $existingTodo = $todoModel->getTodoByTitle($title);
            
            if ($existingTodo) {
                $_SESSION['error'] = 'Gagal! Judul todo "' . htmlspecialchars($title) . '" sudah ada.';
            } else {
                $todoModel->createTodo($title, $description);
            }
        }
        header('Location: index.php');
    }

    public function update()
    {
        // ... (Fungsi update tetap sama seperti Poin 4) ...
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $title = trim($_POST['title']); 
            $description = $_POST['description'];
            $is_finished = isset($_POST['is_finished']); 
            $todoModel = new TodoModel();
            $existingTodo = $todoModel->getTodoByTitle($title);
            
            if ($existingTodo && $existingTodo['id'] != $id) {
                $_SESSION['error'] = 'Gagal! Judul todo "' . htmlspecialchars($title) . '" sudah ada.';
            } else {
                $todoModel->updateTodo($id, $title, $description, $is_finished);
            }
        }
        header('Location: index.php');
    }

    public function delete()
    {
        // ... (Fungsi delete tetap sama) ...
        if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
            $id = $_GET['id'];
            $todoModel = new TodoModel();
            $todoModel->deleteTodo($id);
        }
        header('Location: index.php');
    }
}   