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
        // ... (Query dasar dan WHERE tetap sama seperti Poin 3) ...
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

        // MODIFIKASI (POIN 6): Mengurutkan berdasarkan sort_order
        $query .= ' ORDER BY sort_order ASC';
        
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
        // ... (Fungsi ini tetap sama) ...
        $query = 'SELECT * FROM todo WHERE title ILIKE $1 LIMIT 1';
        $result = pg_query_params($this->conn, $query, [$title]);
        if ($result && pg_num_rows($result) > 0) { return pg_fetch_assoc($result); }
        return null; 
    }

    public function getTodoById($id)
    {
        // ... (Fungsi ini tetap sama) ...
        $query = 'SELECT * FROM todo WHERE id = $1 LIMIT 1';
        $result = pg_query_params($this->conn, $query, [$id]);
        if ($result && pg_num_rows($result) > 0) {
            $row = pg_fetch_assoc($result);
            $row['is_finished'] = ($row['is_finished'] === 't');
            return $row;
        }
        return null; 
    }


    public function createTodo($title, $description)
    {
        // MODIFIKASI (POIN 6): Mendapatkan sort_order berikutnya
        // Kita atur urutan baru sebagai (nilai max saat ini) + 1
        $orderQuery = 'SELECT COALESCE(MAX(sort_order), 0) + 1 AS next_order FROM todo';
        $orderResult = pg_query($this->conn, $orderQuery);
        $next_order = pg_fetch_assoc($orderResult)['next_order'];

        // Menggunakan 'title', 'description', dan 'sort_order'
        $query = 'INSERT INTO todo (title, description, sort_order) VALUES ($1, $2, $3)';
        $result = pg_query_params($this->conn, $query, [$title, $description, $next_order]);
        return $result !== false;
    }

    /**
     * PENAMBAHAN BARU: Fungsi untuk menyimpan urutan (POIN 6)
     * Menerima array ID todo dalam urutan yang baru.
     */
    public function updateTodoOrder($id_order_array)
    {
        // Memulai transaksi
        pg_query($this->conn, 'BEGIN');
        
        try {
            // Loop sebanyak array ID dan update 'sort_order' satu per satu
            // Urutan (index) array (mulai dari 0) akan menjadi 'sort_order' baru
            foreach ($id_order_array as $index => $id) {
                // Pastikan $id adalah integer
                $id = (int)$id; 
                // $index adalah urutan baru (0, 1, 2, ...)
                $sort_order = $index; 
                
                $query = 'UPDATE todo SET sort_order = $1 WHERE id = $2';
                pg_query_params($this->conn, $query, [$sort_order, $id]);
            }
            // Jika semua berhasil, commit transaksi
            pg_query($this->conn, 'COMMIT');
            return true;
        } catch (Exception $e) {
            // Jika ada error, batalkan (rollback)
            pg_query($this->conn, 'ROLLBACK');
            return false;
        }
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