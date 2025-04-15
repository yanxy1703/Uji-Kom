<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $task_id = $_POST['task_id'];
    $task_name = $_POST['task_name'];
    $deadline = $_POST['deadline'];
    $status = $_POST['status'];
    $user_id = $_SESSION['user_id'];

    // Cek apakah ada subtask yang belum selesai
    $cek_subtask = $conn->prepare("SELECT COUNT(*) FROM subtasks WHERE task_id = ? AND status != 'Selesai'");
    $cek_subtask->bind_param("i", $task_id);
    $cek_subtask->execute();
    $cek_subtask->bind_result($jumlah_subtask);
    $cek_subtask->fetch();
    $cek_subtask->close();

    if ($status == 'Selesai' && $jumlah_subtask > 0) {
        echo "<script>
                alert('❗ Harap selesaikan semua subtask sebelum menyelesaikan tugas.');
                window.location.href = 'index.php';
              </script>";
        exit();
    }

    // Update status tugas
    $stmt = $conn->prepare("UPDATE tasks SET task_name = ?, deadline = ?, status = ? WHERE id = ? AND user_id = ?");
    $stmt->bind_param("sssii", $task_name, $deadline, $status, $task_id, $user_id);
    $stmt->execute();
    $stmt->close();

    // Jika tugas selesai, pindahkan ke history
    if ($status == 'Selesai') {
        $stmt = $conn->prepare("INSERT INTO history (user_id, task_name) VALUES (?, ?)");
        $stmt->bind_param("is", $user_id, $task_name);
        $stmt->execute();
        $stmt->close();

        // Hapus tugas dari tabel `tasks`
        $stmt = $conn->prepare("DELETE FROM tasks WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $task_id, $user_id);
        $stmt->execute();
        $stmt->close();

        // ✅ Pop-up konfirmasi setelah berhasil dipindahkan ke histori
        echo "<script>
                alert('✅ Tugas \"$task_name\" telah selesai dan dipindahkan ke histori.');
                window.location.href = 'index.php';
              </script>";
        exit();
    }

    echo "<script>
            alert('Tugas berhasil diperbarui!');
            window.location.href = 'index.php';
          </script>";
}
?>
