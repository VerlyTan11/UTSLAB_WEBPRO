<?php
require_once('koneksi.php');

if (isset($_GET['id'])) {
    $start_task_id = mysqli_real_escape_string($koneksi, $_GET['id']);

    // Periksa apakah sudah ada tugas dalam status "In Progress"
    $inProgressCheck = "SELECT COUNT(*) as inProgressCount FROM tbl_task WHERE Status = 'In Progress'";
    $inProgressResult = mysqli_query($koneksi, $inProgressCheck);
    $inProgressCount = mysqli_fetch_assoc($inProgressResult)['inProgressCount'];

    if ($inProgressCount > 0) {
        // Jika sudah ada tugas dalam status "In Progress," ubah status tugas tersebut menjadi "Waiting on"
        $updateWaitingOn = "UPDATE tbl_task SET Status = 'Waiting on' WHERE Status = 'In Progress'";
        $updateWaitingOnResult = mysqli_query($koneksi, $updateWaitingOn);

        if (!$updateWaitingOnResult) {
            echo "Error: " . mysqli_error($koneksi);
            exit;
        }
    }

    // Ubah status tugas yang dipilih menjadi "In Progress"
    $updateInProgress = "UPDATE tbl_task SET Status = 'In Progress' WHERE id = $start_task_id";
    $updateInProgressResult = mysqli_query($koneksi, $updateInProgress);

    if ($updateInProgressResult) {
        // Perubahan status berhasil
        // Redirect kembali ke halaman utama atau halaman lain yang sesuai
        header("Location: home_page.php");
        exit;
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
} else {
    echo "ID tugas tidak valid.";
}
?>