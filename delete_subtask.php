<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['subtask_id'])) {
    $subtask_id = (int)$_POST['subtask_id'];
    $user_id = $_SESSION['user_id'];

    // Pastikan subtask ini benar-benar milik pengguna yang login
    $stmt = $conn->prepare("DELETE FROM subtasks WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $subtask_id, $user_id);

    if ($stmt->execute() && $stmt->affected_rows > 0) {
        header("Location: index.php");
        exit();
    } else {
        echo "<script>alert('Gagal menghapus subtask! Pastikan subtask milik Anda.'); window.location.href = 'index.php';</script>";
        exit();
    }
}

// Jika terjadi kesalahan atau request tidak valid
header("Location: index.php");
exit();
?>
