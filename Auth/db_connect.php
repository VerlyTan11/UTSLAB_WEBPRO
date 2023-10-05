<?php
$servername = "localhost";
$username = "username_db";
$password = "password_db";
$dbname = "tasktracker";

// Buat koneksi ke database
$conn = new mysqli($servername, $username, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi ke database gagal: " . $conn->connect_error);
}
?>