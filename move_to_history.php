<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $task_id = (int) $_POST['task_id'];
    $user_id = $_SESSION['user_id'];

    // Cek apakah ada subtask yang belum selesai
    $subtask_check = $conn->query("SELECT * FROM subtasks WHERE task_id = $task_id AND user_id = $user_id AND status != 'Selesai'");
    if ($subtask_check->num_rows > 0) {
        echo "<script>
                alert('Harap selesaikan semua subtask sebelum menyimpan tugas ke histori.');
                window.location.href = 'index.php';
              </script>";
        exit();
    }

    // Ambil data tugas sebelum dihapus
    $task_query = $conn->query("SELECT * FROM tasks WHERE id = $task_id AND user_id = $user_id");
    if ($task_query->num_rows > 0) {
        $task = $task_query->fetch_assoc();
        
        // Pindahkan ke tabel history_tasks
        $stmt = $conn->prepare("INSERT INTO history_tasks (task_name, deadline, priority, user_id, completed_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param("sssi", $task['task_name'], $task['deadline'], $task['priority'], $user_id);
        $stmt->execute();
        $stmt->close();

        // Hapus dari tabel tasks
        $conn->query("DELETE FROM tasks WHERE id = $task_id AND user_id = $user_id");
    }

    echo "<script>
            alert('Tugas telah dipindahkan ke histori.');
            window.location.href = 'index.php';
          </script>";
}
?>
