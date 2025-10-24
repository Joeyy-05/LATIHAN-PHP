<?php
require_once (__DIR__ . '/../config.php');

class TodoModel
{
    private $conn;

    public function __construct()
    {
        // Inisialisasi koneksi database PostgreSQL
        $this->conn = pg_connect('host=' . DB_HOST . ' port=' . DB_PORT . ' dbname=' . DB_NAME . ' user=' . DB_USER . ' password=' . DB_PASSWORD);
        if (!$this->conn) {
            die('Koneksi database gagal');
        }
    }

    public function getAllTodos()
    {
        // Menggunakan nama kolom baru dan diurutkan berdasarkan ID.
        // (Kita akan ubah ORDER BY ini nanti untuk Poin 6: Sorting)
        $query = 'SELECT id, title, description, is_finished, created_at, updated_at 
                  FROM todo 
                  ORDER BY id ASC';
        
        $result = pg_query($this->conn, $query);
        $todos = [];
        if ($result && pg_num_rows($result) > 0) {
            while ($row = pg_fetch_assoc($result)) {
                // Konversi string 't'/'f' dari PostgreSQL ke boolean PHP (true/false)
                $row['is_finished'] = ($row['is_finished'] === 't'); 
                $todos[] = $row;
            }
        }
        return $todos;
    }

    public function createTodo($title, $description)
    {
        // Menggunakan 'title' dan 'description'
        // Kolom 'is_finished', 'created_at', 'updated_at' akan diisi oleh DEFAULT di DB
        $query = 'INSERT INTO todo (title, description) VALUES ($1, $2)';
        $result = pg_query_params($this->conn, $query, [$title, $description]);
        return $result !== false;
    }

    public function updateTodo($id, $title, $description, $is_finished)
    {
        // Memperbarui kolom-kolom baru
        // 'updated_at' akan di-handle oleh trigger 'update_timestamp' Anda
        $query = 'UPDATE todo SET title=$1, description=$2, is_finished=$3 WHERE id=$4';
        
        // Konversi boolean PHP (true/false) ke string 't'/'f' untuk PostgreSQL
        $is_finished_db = $is_finished ? 't' : 'f';
        
        $result = pg_query_params($this->conn, $query, [$title, $description, $is_finished_db, $id]);
        return $result !== false;
    }

    public function deleteTodo($id)
    {
        $query = 'DELETE FROM todo WHERE id=$1';
        $result = pg_query_params($this->conn, $query, [$id]);
        return $result !== false;
    }

    /**
     * Fungsi baru (opsional) untuk hanya mengubah status selesai/belum selesai
     * Ini akan sangat berguna untuk fitur checklist nanti.
     */
    public function updateTodoStatus($id, $is_finished)
    {
        $query = 'UPDATE todo SET is_finished=$1 WHERE id=$2';
        
        // Konversi boolean PHP (true/false) ke string 't'/'f'
        $is_finished_db = $is_finished ? 't' : 'f';
        
        $result = pg_query_params($this->conn, $query, [$is_finished_db, $id]);
        return $result !== false;
    }
}