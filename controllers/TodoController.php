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

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title']); // trim() untuk validasi
            $description = $_POST['description']; 
            
            $todoModel = new TodoModel();

            // VALIDASI (POIN 4)
            $existingTodo = $todoModel->getTodoByTitle($title);
            
            if ($existingTodo) {
                // Jika judul sudah ada, atur pesan error ke session
                $_SESSION['error'] = 'Gagal! Judul todo "' . htmlspecialchars($title) . '" sudah ada.';
            } else {
                // Jika tidak ada, baru buat data
                $todoModel->createTodo($title, $description);
            }
        }
        header('Location: index.php'); // Selalu redirect kembali
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $title = trim($_POST['title']); // trim() untuk validasi
            $description = $_POST['description'];
            $is_finished = isset($_POST['is_finished']); 
            
            $todoModel = new TodoModel();

            // VALIDASI (POIN 4)
            $existingTodo = $todoModel->getTodoByTitle($title);
            
            // Cek: Apakah judul sudah ada DAN ID-nya BEDA dengan yang sedang di-edit
            if ($existingTodo && $existingTodo['id'] != $id) {
                // Jika ya, itu duplikat
                $_SESSION['error'] = 'Gagal! Judul todo "' . htmlspecialchars($title) . '" sudah ada.';
            } else {
                // Jika tidak, aman untuk update
                $todoModel->updateTodo($id, $title, $description, $is_finished);
            }
        }
        header('Location: index.php'); // Selalu redirect kembali
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