<?php
require_once('koneksi.php');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['task_done'])) {
    // Daftar ID tugas yang dicentang sebagai "Done"
    $done_tasks = $_POST['task_done'];

    // Ubah tugas yang diselesaikan ke paling bawah
    $sql = "UPDATE tbl_task SET Status = 'Selesai' WHERE id IN (".implode(',', $done_tasks).")";
    $result = mysqli_query($koneksi, $sql);

    if (!$result) {
        echo "Error: " . mysqli_error($koneksi);
    }

    // Redirect kembali ke halaman utama
    header("Location: home_page.php");
} else {
    echo "Tidak ada tugas yang dicentang.";
}

mysqli_close($koneksi);
?>
