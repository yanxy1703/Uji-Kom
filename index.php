<?php
session_start();

// Koneksi database
include 'koneksi.php';

// Redirect jika belum login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Ambil data user
$user_query = $conn->query("SELECT username FROM users WHERE id = $user_id");
if (!$user_query) {
    die("Error mengambil data user: " . $conn->error);
}
$user = $user_query->fetch_assoc();

// Ambil daftar tugas
$result = $conn->query("SELECT * FROM tasks WHERE user_id = $user_id ORDER BY created_at DESC");
if (!$result) {
    die("Error mengambil daftar tugas: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>To-Do List</title>
    <link rel="stylesheet" href="style.css">
    <style>
        <?php include "style.css"; ?>
        .delete-subtask-btn {
            background: none;
            border: none;
            color: red;
            cursor: pointer;
            font-size: 0.9rem;
            padding: 0;
            margin-left: 10px;
        }
        .delete-subtask-btn:hover {
            text-decoration: underline;
        }
        .history-btn {
            background: #4CAF50;
            color: white;
            padding: 5px 10px;
            border: none;
            cursor: pointer;
            margin-top: 5px;
        }
        .history-btn:hover {
            background: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>To-Do List</h2>
            <div class="user-menu">
                <button class="user-btn">ðŸ‘¤ <?= htmlspecialchars($user['username']) ?></button>
                <a href="history.php" class="history-btn">ðŸ“œ Histori</a>
                <a href="logout.php" class="logout-btn" onclick="return confirmLogout()">Logout</a>
            </div>
        </div>

        <!-- Form tambah tugas -->
        <form method="POST" action="add_task.php" class="form-container">
            <input type="text" name="task_name" placeholder="Tambah tugas..." required>
            <input type="date" name="deadline" required>
            <select name="priority">
                <option value="Low">Low</option>
                <option value="Medium" selected>Medium</option>
                <option value="High">High</option>
            </select>
            <button type="submit">Tambah Tugas</button>
        </form>

        <h3>Daftar Tugas</h3>
        <div class="task-list">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="task-item">
                    <p><strong><?= htmlspecialchars($row['task_name']) ?></strong></p>
                    <p>Deadline: <?= $row['deadline'] ?></p>
                    <p>Status: <?= $row['status'] ?></p>
                    <p>Prioritas: <strong class="priority-<?= strtolower($row['priority']) ?>"><?= htmlspecialchars($row['priority']) ?></strong></p>

                    <!-- Form edit tugas -->
                    <form method="POST" action="edit_task.php">
                        <input type="hidden" name="task_id" value="<?= $row['id'] ?>">
                        <input type="text" name="task_name" value="<?= htmlspecialchars($row['task_name']) ?>" required>
                        <input type="date" name="deadline" value="<?= $row['deadline'] ?>" required>
                        <select name="status">
                            <option value="Belum Selesai" <?= $row['status'] == 'Belum Selesai' ? 'selected' : '' ?>>Belum Selesai</option>
                            <option value="Selesai" <?= $row['status'] == 'Selesai' ? 'selected' : '' ?>>Selesai</option>
                        </select>
                        <button type="submit">Edit</button>
                    </form>

                    <!-- List Subtask -->
                    <ul class="subtask-list">
                        <?php
                        $task_id = $row['id'];
                        $subtask_result = $conn->query("SELECT * FROM subtasks WHERE task_id = $task_id AND user_id = $user_id");
                        if ($subtask_result) {
                            while ($subtask = $subtask_result->fetch_assoc()):
                        ?>
                                <li class="subtask-item">
                                    <div class="subtask-container">
                                        <form method="POST" action="update_subtask.php" style="display:inline;">
                                            <input type="hidden" name="id" value="<?= $subtask['id'] ?>">
                                            <input type="checkbox" name="status" <?= $subtask['status'] == 'Selesai' ? 'checked' : '' ?> onchange="this.form.submit()">
                                            <span class="<?= $subtask['status'] == 'Selesai' ? 'completed' : '' ?>">
                                                <?= htmlspecialchars($subtask['subtask_name']) ?>
                                            </span>
                                        </form>
                                        <form method="POST" action="delete_subtask.php" style="display:inline;" onsubmit="return confirm('Hapus subtask ini?');">
                                            <input type="hidden" name="subtask_id" value="<?= $subtask['id'] ?>">
                                            <button type="submit" class="delete-subtask-btn">Hapus</button>
                                        </form>
                                    </div>
                                </li>
                        <?php
                            endwhile;
                        }
                        ?>
                    </ul>

                    <!-- Form tambah subtask -->
                    <form method="POST" action="add_subtask.php" class="subtask-form">
                        <input type="hidden" name="task_id" value="<?= $row['id'] ?>">
                        <input type="text" name="subtask_name" placeholder="Tambah subtask..." required>
                        <button type="submit">Tambah Subtask</button>
                    </form>

                    <!-- Tombol simpan ke histori -->
                    <?php if ($row['status'] == 'Selesai'): ?>
                        <form method="POST" action="move_to_history.php" onsubmit="return validateHistory(<?= $row['id'] ?>);">
                            <input type="hidden" name="task_id" value="<?= $row['id'] ?>">
                            <button type="submit" class="history-btn">Simpan ke Histori</button>
                        </form>
                    <?php endif; ?>

                    <!-- Hapus tugas -->
                    <form method="POST" action="delete_task.php" onsubmit="return confirm('Apakah Anda yakin ingin menghapus tugas ini?');">
                        <input type="hidden" name="task_id" value="<?= $row['id'] ?>">
                        <button type="submit" class="delete">Hapus</button>
                    </form>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <script>
        function confirmLogout() {
            return confirm("Apakah Anda yakin ingin logout?");
        }

        function validateHistory(taskId) {
            const checkboxes = document.querySelectorAll('.subtask-list input[type="checkbox"]:not(:checked)');
            if (checkboxes.length > 0) {
                alert("Harap selesaikan semua subtask sebelum menyimpan tugas ke histori.");
                return false;
            }
            return confirm("Apakah Anda yakin ingin menyimpan tugas ini ke histori?");
        }
    </script>
</body>
</html>