<!DOCTYPE html>
<html>
<head>
    <title>PHP - Aplikasi Todolist</title>
    <link href="/assets/vendor/bootstrap-5.3.8-dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<div class="container-fluid p-5">
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <h1>Todo List</h1>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTodo">Tambah Data</button>
            </div>
            <hr />

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= $_SESSION['error'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['error']); // Hapus pesan error setelah ditampilkan ?>
            <?php endif; ?>

            <ul class="nav nav-pills mb-3">
                <li class="nav-item">
                    <a class="nav-link <?php if ($filter === 'all') echo 'active'; ?>" 
                       href="index.php">Semua</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php if ($filter === 'finished') echo 'active'; ?>" 
                       href="index.php?filter=finished">Selesai</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php if ($filter === 'unfinished') echo 'active'; ?>" 
                       href="index.php?filter=unfinished">Belum Selesai</a>
                </li>
            </ul>
            
            <form action="index.php" method="GET" class="mb-3">
                <?php if ($filter !== 'all'): ?>
                    <input type="hidden" name="filter" value="<?= htmlspecialchars($filter) ?>">
                <?php endif; ?>
                <div class="input-group">
                    <input type="text" name="search" class="form-control" 
                           placeholder="Cari berdasarkan judul atau deskripsi..." 
                           value="<?= htmlspecialchars($search) ?>">
                    <button class="btn btn-outline-secondary" type="submit">Cari</button>
                </div>
            </form>
            
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Judul</th>
                        <th scope="col">Status</th>
                        <th scope="col">Tanggal Dibuat</th>
                        <th scope="col" class="text-end">Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!empty($todos)): ?>
                    <?php foreach ($todos as $i => $todo): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= htmlspecialchars($todo['title']) ?></td>
                        <td>
                            <?php if ($todo['is_finished']): ?>
                                <span class="badge bg-success">Selesai</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Belum Selesai</span>
                            <?php endif; ?>
                        </td>
                        <td><?= date('d F Y - H:i', strtotime($todo['created_at'])) ?></td>
                        
                        <td class="text-end" style="white-space: nowrap;">
                            
                            <a href="index.php?page=detail&id=<?= $todo['id'] ?>" class="btn btn-sm btn-info">
                                Detail
                            </a>
                            
                            <button class="btn btn-sm btn-warning"
                                onclick="showModalEditTodo(
                                    <?= $todo['id'] ?>, 
                                    '<?= htmlspecialchars(addslashes($todo['title'])) ?>', 
                                    '<?= htmlspecialchars(addslashes($todo['description'])) ?>', 
                                    <?= $todo['is_finished'] ? 'true' : 'false' ?>
                                )">
                                Ubah
                            </button>

                            <button class="btn btn-sm btn-danger"
                                onclick="showModalDeleteTodo(<?= $todo['id'] ?>, '<?= htmlspecialchars(addslashes($todo['title'])) ?>')">
                                Hapus
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted">
                            <?php if (!empty($search)): ?>
                                Hasil pencarian "<?= htmlspecialchars($search) ?>" tidak ditemukan.
                            <?php else: ?>
                                Belum ada data tersedia!
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="addTodo" tabindex="-1" aria-labelledby="addTodoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addTodoLabel">Tambah Data Todo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="?page=create" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="inputTitle" class="form-label">Judul</label>
                        <input type="text" name="title" class="form-control" id="inputTitle"
                            placeholder="Contoh: Belajar membuat aplikasi website sederhana" required>
                    </div>
                    <div class="mb-3">
                        <label for="inputDescription" class="form-label">Deskripsi (Opsional)</label>
                        <textarea name="description" class="form-control" id="inputDescription" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editTodo" tabindex="-1" aria-labelledby="editTodoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editTodoLabel">Ubah Data Todo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="?page=update" method="POST">
                <input name="id" type="hidden" id="inputEditTodoId">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="inputEditTitle" class="form-label">Judul</label>
                        <input type="text" name="title" class="form-control" id="inputEditTitle"
                            placeholder="Contoh: Belajar membuat aplikasi website sederhana" required>
                    </div>
                    <div class="mb-3">
                        <label for="inputEditDescription" class="form-label">Deskripsi (Opsional)</label>
                        <textarea name="description" class="form-control" id="inputEditDescription" rows="3"></textarea>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" name="is_finished" class="form-check-input" id="inputEditIsFinished">
                        <label class="form-check-label" for="inputEditIsFinished">Tandai sebagai selesai</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteTodo" tabindex="-1" aria-labelledby="deleteTodoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteTodoLabel">Hapus Data Todo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    Kamu akan menghapus todo <strong class="text-danger" id="deleteTodoTitle"></strong>.
                    Apakah kamu yakin?
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <a id="btnDeleteTodo" class="btn btn-danger">Ya, Tetap Hapus</a>
            </div>
        </div>
    </div>
</div>

<script src="/assets/vendor/bootstrap-5.3.8-dist/js/bootstrap.min.js"></script>
<script>
// ... (Javascript tetap sama) ...
function showModalEditTodo(todoId, title, description, is_finished) {
    document.getElementById("inputEditTodoId").value = todoId;
    document.getElementById("inputEditTitle").value = title;
    document.getElementById("inputEditDescription").value = description;
    document.getElementById("inputEditIsFinished").checked = is_finished; 
    var myModal = new bootstrap.Modal(document.getElementById("editTodo"));
    myModal.show();
}

function showModalDeleteTodo(todoId, title) {
    document.getElementById("deleteTodoTitle").innerText = title;
    document.getElementById("btnDeleteTodo").setAttribute("href", `?page=delete&id=${todoId}`);
    var myModal = new bootstrap.Modal(document.getElementById("deleteTodo"));
    myModal.show();
}
</script>
</body>
</html>