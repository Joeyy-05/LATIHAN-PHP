<?php
require_once (__DIR__ . '/../config.php');

class TodoModel
{
    private $conn;

    public function __construct()
    {
        // ... (Fungsi construct tetap sama) ...
        $this->conn = pg_connect('host=' . DB_HOST . ' port=' . DB_PORT . ' dbname=' . DB_NAME . ' user=' . DB_USER . ' password=' . DB_PASSWORD);
        if (!$this->conn) {
            die('Koneksi database gagal');
        }
    }

    public function getAllTodos($filter = 'all', $search = '')
    {
        // ... (Fungsi getAllTodos tetap sama seperti Poin 3) ...
        $query = 'SELECT id, title, description, is_finished, created_at, updated_at 
                  FROM todo';
        $params = [];
        $whereClauses = [];
        $paramCounter = 1; 

        if ($filter === 'finished') {
            $whereClauses[] = 'is_finished = $' . $paramCounter++;
            $params[] = 't'; 
        } elseif ($filter === 'unfinished') {
            $whereClauses[] = 'is_finished = $' . $paramCounter++;
            $params[] = 'f'; 
        }

        if (!empty($search)) {
            $whereClauses[] = '(title ILIKE $' . $paramCounter . ' OR description ILIKE $' . $paramCounter . ')';
            $params[] = '%' . $search . '%'; 
        }

        if (!empty($whereClauses)) {
            $query .= ' WHERE ' . implode(' AND ', $whereClauses);
        }

        $query .= ' ORDER BY id ASC';
        
        if (empty($params)) {
            $result = pg_query($this->conn, $query);
        } else {
            $result = pg_query_params($this->conn, $query, $params);
        }
        
        $todos = [];
        if ($result && pg_num_rows($result) > 0) {
            while ($row = pg_fetch_assoc($result)) {
                $row['is_finished'] = ($row['is_finished'] === 't'); 
                $todos[] = $row;
            }
        }
        return $todos;
    }

    public function getTodoByTitle($title)
    {
        // ... (Fungsi getTodoByTitle tetap sama seperti Poin 4) ...
        $query = 'SELECT * FROM todo WHERE title ILIKE $1 LIMIT 1';
        $result = pg_query_params($this->conn, $query, [$title]);
        
        if ($result && pg_num_rows($result) > 0) {
            return pg_fetch_assoc($result); 
        }
        return null; 
    }

    /**
     * PENAMBAHAN BARU: Fungsi untuk Detail (POIN 5)
     * Mengambil satu todo berdasarkan ID-nya.
     */
    public function getTodoById($id)
    {
        $query = 'SELECT * FROM todo WHERE id = $1 LIMIT 1';
        $result = pg_query_params($this->conn, $query, [$id]);

        if ($result && pg_num_rows($result) > 0) {
            $row = pg_fetch_assoc($result);
            // Konversi boolean 't'/'f'
            $row['is_finished'] = ($row['is_finished'] === 't');
            return $row;
        }
        return null; // Kembalikan null jika ID tidak ditemukan
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