<?php
session_start();
include 'koneksi.php'; // Menggunakan koneksi dari koneksi.php (variabel: $con)

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Cek apakah email ada di database
    $query = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        // Cek apakah password cocok (tanpa hashing)
        if ($password === $user['password']) { 
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            echo "<script>alert('Login berhasil!'); window.location.href='index.php';</script>";
            exit();
        } else {
            echo "<script>alert('Password salah!'); window.location.href='login.php';</script>";
            exit();
        }
    } else {
        echo "<script>alert('Email tidak ditemukan!'); window.location.href='login.php';</script>";
        exit();
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
    <title>Login</title>
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
            height: 450px;
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
            margin: 30px 0;
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

        .error {
            color: red;
            text-align: center;
            margin-bottom: 10px;
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
            <h2>Login</h2>

            <form action="login.php" method="POST">
                <div class="inputbox">
                    <input type="email" name="email" required>
                    <label>Masukkan Email</label>
                    <ion-icon name="mail-outline"></ion-icon>
                </div>

                <div class="inputbox">
                    <input type="password" name="password" required>
                    <label>Masukkan Password</label>
                    <ion-icon name="lock-closed-outline"></ion-icon>
                </div>

                <button type="submit">Login</button>

                <div class="register">
                    <p>Belum punya akun? <a href="register.php">Daftar di sini</a></p>
                </div>
            </form>
        </div>
    </div>
</section>
</body>
</html>
