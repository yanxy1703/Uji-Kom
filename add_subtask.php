<?php
session_start();
include 'koneksi.php';

// Pastikan pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $task_id = (int)$_POST['task_id'];
    $subtask_name = trim($_POST['subtask_name']);
    $user_id = $_SESSION['user_id']; // Ambil user_id dari session

    // Pastikan input tidak kosong
    if (!empty($task_id) && !empty($subtask_name)) {
        // Periksa apakah tugas benar-benar milik pengguna
        $stmt_check = $conn->prepare("SELECT id FROM tasks WHERE id = ? AND user_id = ?");
        $stmt_check->bind_param("ii", $task_id, $user_id);
        $stmt_check->execute();
        $stmt_check->store_result();

        if ($stmt_check->num_rows > 0) {
            // Tambahkan subtask dengan user_id
            $stmt_insert = $conn->prepare("INSERT INTO subtasks (task_id, user_id, subtask_name, status) VALUES (?, ?, ?, 'Belum Selesai')");
            $stmt_insert->bind_param("iis", $task_id, $user_id, $subtask_name);

            if ($stmt_insert->execute()) {
                echo "<script>
                        alert('Subtask berhasil ditambahkan!');
                        window.location.href = 'index.php';
                      </script>";
            } else {
                echo "<script>
                        alert('Gagal menambahkan subtask!');
                        window.location.href = 'index.php';
                      </script>";
            }
            $stmt_insert->close();
        } else {
            echo "<script>
                    alert('Tugas tidak ditemukan atau bukan milik Anda!');
                    window.location.href = 'index.php';
                  </script>";
        }
        $stmt_check->close();
    } else {
        echo "<script>
                alert('Mohon isi semua kolom!');
                window.location.href = 'index.php';
              </script>";
    }
}
?>
