<!DOCTYPE html>
<html>
<head>
    <title>Detail Todo - Aplikasi Todolist</title>
    <link href="/assets/vendor/bootstrap-5.3.8-dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<div class="container-fluid p-5">
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <h1>Detail Todo</h1>
                <a href="index.php" class="btn btn-secondary">Kembali ke Daftar</a>
            </div>
            <hr />

            <h3 class="card-title mb-3"><?= htmlspecialchars($todo['title']) ?></h3>

            <p class="card-text">
                <?php if (!empty($todo['description'])): ?>
                    <?= nl2br(htmlspecialchars($todo['description'])) // nl2br untuk menghargai baris baru ?>
                <?php else: ?>
                    <em class="text-muted">Tidak ada deskripsi.</em>
                <?php endif; ?>
            </p>
            
            <hr />

            <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex justify-content-between">
                    <strong>Status:</strong>
                    <span>
                        <?php if ($todo['is_finished']): ?>
                            <span class="badge bg-success">Selesai</span>
                        <?php else: ?>
                            <span class="badge bg-danger">Belum Selesai</span>
                        <?php endif; ?>
                    </span>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <strong>Dibuat pada:</strong>
                    <span><?= date('d F Y - H:i', strtotime($todo['created_at'])) ?></span>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <strong>Terakhir diperbarui:</strong>
                    <span><?= date('d F Y - H:i', strtotime($todo['updated_at'])) ?></span>
                </li>
            </ul>

        </div>
    </div>
</div>

<script src="/assets/vendor/bootstrap-5.3.8-dist/js/bootstrap.min.js"></script>
</body>
</html>