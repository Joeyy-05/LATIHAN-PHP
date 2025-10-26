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

    public function detail()
    {
        // ... (Fungsi detail tetap sama seperti Poin 5) ...
        if (!isset($_GET['id'])) {
            $_SESSION['error'] = 'Error: ID Todo tidak ditemukan.';
            header('Location: index.php');
            return;
        }
        $id = $_GET['id'];
        $todoModel = new TodoModel();
        $todo = $todoModel->getTodoById($id);
        if (!$todo) {
            $_SESSION['error'] = 'Error: Data Todo tidak ditemukan.';
            header('Location: index.php');
            return;
        }
        include (__DIR__ . '/../views/TodoDetailView.php');
    }

    /**
     * PENAMBAHAN BARU: Fungsi untuk Sorting (POIN 6)
     * Ini adalah endpoint API, bukan halaman.
     */
    public function updateOrder()
    {
        // Hanya izinkan metode POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            // Baca data JSON mentah dari body request
            $json = file_get_contents('php://input');
            
            // Decode JSON menjadi array PHP
            $data = json_decode($json, true);

            // Periksa apakah data 'order' ada dan merupakan array
            if (isset($data['order']) && is_array($data['order'])) {
                $todoModel = new TodoModel();
                $success = $todoModel->updateTodoOrder($data['order']);

                // Kirim respons JSON kembali ke JavaScript
                header('Content-Type: application/json');
                if ($success) {
                    echo json_encode(['success' => true, 'message' => 'Urutan disimpan.']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Gagal menyimpan urutan.']);
                }
            } else {
                // Data tidak valid
                header('Content-Type: application/json', true, 400); // Bad Request
                echo json_encode(['success' => false, 'message' => 'Data order tidak valid.']);
            }
        } else {
            // Metode tidak diizinkan
            header('Content-Type: application/json', true, 405); // Method Not Allowed
            echo json_encode(['success' => false, 'message' => 'Metode tidak diizinkan.']);
        }
        // Hentikan eksekusi skrip karena ini adalah API endpoint
        exit;
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