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

    /**
     * Modifikasi: Fungsi ini sekarang menerima parameter filter.
     * @param string $filter ('all', 'finished', 'unfinished')
     */
    public function getAllTodos($filter = 'all')
    {
        // Query dasar
        $query = 'SELECT id, title, description, is_finished, created_at, updated_at 
                  FROM todo';
        
        $params = [];
        
        // Menambahkan WHERE clause berdasarkan filter
        if ($filter === 'finished') {
            $query .= ' WHERE is_finished = $1';
            $params[] = 't'; // 't' untuk true di PostgreSQL
        } elseif ($filter === 'unfinished') {
            $query .= ' WHERE is_finished = $1';
            $params[] = 'f'; // 'f' untuk false di PostgreSQL
        }
        // Jika filter 'all', tidak ada WHERE clause ditambahkan.

        // (Kita akan ubah ORDER BY ini nanti untuk Poin 6: Sorting)
        $query .= ' ORDER BY id ASC';
        
        // Menjalankan query
        if (empty($params)) {
            $result = pg_query($this->conn, $query);
        } else {
            $result = pg_query_params($this->conn, $query, $params);
        }
        
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
        // ... (Fungsi ini tetap sama) ...
        $query = 'INSERT INTO todo (title, description) VALUES ($1, $2)';
        $result = pg_query_params($this->conn, $query, [$title, $description]);
        return $result !== false;
    }

    public function updateTodo($id, $title, $description, $is_finished)
    {
        // ... (Fungsi ini tetap sama) ...
        $query = 'UPDATE todo SET title=$1, description=$2, is_finished=$3 WHERE id=$4';
        $is_finished_db = $is_finished ? 't' : 'f';
        $result = pg_query_params($this->conn, $query, [$title, $description, $is_finished_db, $id]);
        return $result !== false;
    }

    public function deleteTodo($id)
    {
        // ... (Fungsi ini tetap sama) ...
        $query = 'DELETE FROM todo WHERE id=$1';
        $result = pg_query_params($this->conn, $query, [$id]);
        return $result !== false;
    }

    public function updateTodoStatus($id, $is_finished)
    {
        // ... (Fungsi ini tetap sama) ...
        $query = 'UPDATE todo SET is_finished=$1 WHERE id=$2';
        $is_finished_db = $is_finished ? 't' : 'f';
        $result = pg_query_params($this->conn, $query, [$is_finished_db, $id]);
        return $result !== false;
    }
}