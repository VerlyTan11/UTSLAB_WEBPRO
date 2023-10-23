<?php
require_once('koneksi.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['task_name']) && isset($_POST['task_date'])) {
    $newTask = $_POST['task_name'];
    $taskDate = $_POST['task_date'];

    // Periksa apakah tugas dan tanggal sudah diisi
    if (empty($newTask) || empty($taskDate)) {
        echo "Error: Tugas dan tanggal harus diisi.";
        exit();
    }

    $sql = "INSERT INTO tbl_task (Tugas, Status, Tanggal) VALUES (?, 'Pending', ?)";
    $stmt = mysqli_prepare($koneksi, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ss", $newTask, $taskDate);
        $result = mysqli_stmt_execute($stmt);

        if ($result) {
            header("Location: home_page.php");
            exit();
        } else {
            echo "Error: " . mysqli_error($koneksi);
        }

        mysqli_stmt_close($stmt);
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
}
?>
