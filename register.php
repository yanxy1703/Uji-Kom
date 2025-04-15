<?php
include 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = trim($_POST['fullname']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Validasi panjang password minimal 8 karakter
    if (strlen($password) < 8) {
        echo "<script>alert('Password harus minimal 8 karakter!'); window.location.href='register.php';</script>";
        exit();
    }

    // Cek apakah username atau email sudah digunakan
    $checkQuery = "SELECT * FROM users WHERE username = ? OR email = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('Username atau Email sudah digunakan!'); window.location.href='register.php';</script>";
        exit();
    }

    // Simpan ke database tanpa hashing (tidak disarankan untuk produksi)
    $insertQuery = "INSERT INTO users (fullname, username, email, password) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param("ssss", $fullname, $username, $email, $password);

    if ($stmt->execute()) {
        echo "<script>alert('Registrasi berhasil! Silakan login.'); window.location.href='login.php';</script>";
    } else {
        echo "<script>alert('Registrasi gagal, coba lagi!'); window.location.href='index.php';</script>";
    }

    $stmt->close();
    $conn->close();
}
?>



<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            font-family: "Trebuchet MS", Arial, sans-serif;
        }

        section {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            width: 100%;
            background: #FF6F61;
        }

        .form-box {
            position: relative;
            width: 400px;
            height: 500px;
            background: transparent;
            border-radius: 20px;
            backdrop-filter: blur(25px) brightness(90%);
            display: flex;
            justify-content: center;
            align-items: center;
        }

        h2 {
            font-size: 2em;
            color: #fff;
            text-align: center;
        }

        .inputbox {
            position: relative;
            margin: 20px 0;
            width: 310px;
            border-bottom: 2px solid #fff;
        }

        .inputbox label {
            position: absolute;
            top: 50%;
            left: 5px;
            transform: translateY(-50%);
            color: #fff;
            font-size: 1em;
            pointer-events: none;
            transition: 0.5s;
        }

        input:focus~label,
        input:valid~label {
            top: -5px;
        }

        .inputbox input {
            width: 100%;
            height: 50px;
            background: transparent;
            border: none;
            outline: none;
            font-size: 1em;
            padding: 0 35px 0 5px;
            color: #fff;
        }

        .inputbox ion-icon {
            position: absolute;
            right: 8px;
            color: #fff;
            font-size: 1.2em;
            top: 20px;
        }

        button {
            width: 100%;
            height: 40px;
            border-radius: 40px;
            background-color: #fff;
            border: none;
            outline: none;
            cursor: pointer;
            font-size: 1em;
            font-weight: 600;
        }

        .register {
            font-size: 0.9em;
            color: #fff;
            text-align: center;
            margin: 25px 0 10px;
        }

        .register p a {
            text-decoration: none;
            color: #fff;
            font-weight: 600;
        }

        .register p a:hover {
            text-decoration: underline;
        }

        @media screen and (max-width: 480px) {
            .form-box {
                width: 100%;
                border-radius: 0px;
            }
        }
    </style>
</head>
<body>
<section>
    <div class="form-box">
        <div class="form-value">
            <h2>Register</h2>
            <form action="register.php" method="POST">
                <div class="inputbox">
                    <input type="text" name="fullname" required>
                    <label>Masukan Nama Lengkap</label>
                    <ion-icon name="person-outline"></ion-icon>
                </div>
                
                <div class="inputbox">
                    <input type="text" name="username" required>
                    <label>Masukan Username</label>
                    <ion-icon name="at-outline"></ion-icon>
                </div>
                
                <div class="inputbox">
                    <input type="email" name="email" required>
                    <label>Masukan Email</label>
                    <ion-icon name="mail-outline"></ion-icon>
                </div>

                <div class="inputbox">
                    <input type="password" name="password" required>
                    <label>Masukan Password</label>
                    <ion-icon name="lock-closed-outline"></ion-icon>
                </div>

                <button type="submit">Register</button>

                <div class="register">
                    <p>Sudah punya akun? <a href="login.php">Login di sini</a></p>
                </div>
            </form>
        </div>
    </div>
</section>
</body>
</html>
