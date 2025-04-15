<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $history_id = $_POST['history_id'];
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("DELETE FROM history WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $history_id, $user_id);
    if ($stmt->execute()) {
        echo "<script>
                alert('Histori berhasil dihapus!');
                window.location.href = 'history.php';
              </script>";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}
?>
