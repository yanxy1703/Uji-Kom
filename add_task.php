<?php
session_start();
include 'koneksi.php';

// Pastikan pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Pastikan request berasal dari metode POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $task_name = htmlspecialchars($_POST['task_name']);
    $deadline = $_POST['deadline'];
    $priority = $_POST['priority'];
    $user_id = $_SESSION['user_id']; // Ambil user_id dari session

    // Debugging: Periksa apakah data dikirim dengan benar
    if (empty($task_name) || empty($deadline) || empty($priority)) {
        die("Error: Data tidak lengkap.");
    }

    // Simpan tugas ke database dengan user_id
    $stmt = $conn->prepare("INSERT INTO tasks (task_name, deadline, priority, user_id) VALUES (?, ?, ?, ?)");
    if (!$stmt) {
        die("Error SQL: " . $conn->error);
    }
    $stmt->bind_param("sssi", $task_name, $deadline, $priority, $user_id);

    if ($stmt->execute()) {
        echo "<script>
                alert('Tugas berhasil ditambahkan!');
                window.location.href = 'index.php';
              </script>";
    } else {
        die("Gagal menambahkan tugas: " . $stmt->error);
    }

    $stmt->close();
    $conn->close();
}
?>
