<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tasktracker";

// Buat koneksi ke database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// You can use the $conn object to perform database operations

// To close the connection when you're done
// $conn->close();
?>