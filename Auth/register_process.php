<?php
require_once('db_connect.php'); // Sambungkan ke database

if (isset($_POST['username']) && isset($_POST['email']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Lindungi dari SQL Injection dengan menggunakan prepared statement
    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $email, $password);

    if ($stmt->execute()) {
        echo "Registrasi berhasil. Silakan login <a href='login.php'>di sini</a>.";
    } else {
        echo "Registrasi gagal. Silakan coba lagi.";
    }

    $stmt->close();
}
?>