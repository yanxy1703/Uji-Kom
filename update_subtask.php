<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $subtask_id = (int)$_POST['id'];
    $user_id = $_SESSION['user_id'];

    // Pastikan subtask benar-benar milik pengguna
    $query = $conn->prepare("SELECT s.status FROM subtasks s 
                             JOIN tasks t ON s.task_id = t.id 
                             WHERE s.id = ? AND t.user_id = ?");
    $query->bind_param("ii", $subtask_id, $user_id);
    $query->execute();
    $result = $query->get_result();

    if ($row = $result->fetch_assoc()) {
        $new_status = ($row['status'] == 'Selesai') ? 'Belum Selesai' : 'Selesai';

        // Update status subtask
        $update = $conn->prepare("UPDATE subtasks SET status = ? WHERE id = ?");
        $update->bind_param("si", $new_status, $subtask_id);
        $update->execute();

        // Redirect tanpa alert agar tidak mengganggu
        header("Location: index.php");
        exit();
    }
}

// Jika tidak valid, tetap redirect ke index.php
header("Location: index.php");
exit();
?>
