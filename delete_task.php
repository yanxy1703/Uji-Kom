<?php
session_start();
include 'koneksi.php';

// Pastikan pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Pastikan request menggunakan metode POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['task_id']) || empty($_POST['task_id'])) {
        die("Error: task_id tidak dikirim.");
    }

    $task_id = (int) $_POST['task_id'];
    $user_id = $_SESSION['user_id'];

    // Debugging: Pastikan task_id dan user_id benar
    if ($task_id <= 0) {
        die("Error: task_id tidak valid.");
    }

    // Hapus semua subtasks terkait terlebih dahulu
    $stmt = $conn->prepare("DELETE FROM subtasks WHERE task_id = ? AND user_id = ?");
    if (!$stmt) {
        die("Error SQL (subtasks): " . $conn->error);
    }
    $stmt->bind_param("ii", $task_id, $user_id);
    if (!$stmt->execute()) {
        die("Gagal menghapus subtask: " . $stmt->error);
    }
    $stmt->close();

    // Hapus task utama
    $stmt = $conn->prepare("DELETE FROM tasks WHERE id = ? AND user_id = ?");
    if (!$stmt) {
        die("Error SQL (tasks): " . $conn->error);
    }
    $stmt->bind_param("ii", $task_id, $user_id);
    if (!$stmt->execute()) {
        die("Gagal menghapus tugas: " . $stmt->error);
    }
    $stmt->close();
    $conn->close();

    // Redirect ke index.php
    echo "<script>
            alert('Tugas berhasil dihapus!');
            window.location.href = 'index.php';
          </script>";
} else {
    // Jika bukan metode POST, kembali ke index.php
    header("Location: index.php");
    exit();
}
?>
