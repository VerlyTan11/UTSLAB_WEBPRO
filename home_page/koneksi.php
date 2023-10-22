<?php
$host = "localhost";
$user = "root";
$pass = "";
$name = "to_do_list";

$koneksi = mysqli_connect($host, $user, $pass, $name);

if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>
