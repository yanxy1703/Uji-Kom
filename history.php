<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$result = $conn->query("SELECT * FROM history WHERE user_id = $user_id ORDER BY completed_at DESC");

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Histori Tugas</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Histori Tugas</h2>
        <a href="index.php">⬅ Kembali</a>

        <table border="1">
            <tr>
                <th>Tugas</th>
                <th>Tanggal Selesai</th>
                <th>Aksi</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['task_name']) ?></td>
                    <td><?= $row['completed_at'] ?></td>
                    <td>
                        <form method="POST" action="delete_history.php" onsubmit="return confirm('Hapus tugas dari histori?');">
                            <input type="hidden" name="history_id" value="<?= $row['id'] ?>">
                            <button type="submit" class="delete">Hapus</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
</html>
