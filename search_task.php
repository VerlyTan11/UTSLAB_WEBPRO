<?php
// Sisipkan file koneksi.php
require_once('koneksi.php');

if (isset($_GET['keyword'])) {
    $keyword = mysqli_real_escape_string($koneksi, $_GET['keyword']);

    // Query pencarian berdasarkan kata kunci pada nama tugas atau tanggal tugas
    $sql = "SELECT * FROM tbl_task WHERE Tugas LIKE ? OR Tanggal LIKE ?";
    $stmt = mysqli_prepare($koneksi, $sql);

    if ($stmt) {
        $likeKeyword = "%" . $keyword . "%";
        mysqli_stmt_bind_param($stmt, "ss", $likeKeyword, $likeKeyword);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            $nomor = 1;
            while ($baris = mysqli_fetch_assoc($result)) {
                echo "<tr class='border border-solid text-center'>";
                echo "<td>" . $nomor . "</td>";
                echo "<td>" . $baris['Tugas'] . "</td>";
                echo "<td>" . $baris['Tanggal'] . "</td>";
                echo "<td>" . $baris['Status'] . "</td>";
                echo "<td class='d-flex justify-content-center grid gap-0 column-gap-3'>";
                echo "<a href='javascript:void(0)' class='done-link btn btn-primary' data-id='" . $baris['id'] . "' onclick='statusStart(\"{$baris['id']}\")'>Start</a>";
                echo "<a href='javascript:void(0)' class='done-link btn btn-primary' data-id='" . $baris['id'] . "' onclick='statusDone(\"{$baris['id']}\")'>Done</a>";
                echo "<a href='edit_task.php?id=" . $baris['id'] . "' class='btn btn-primary'>Edit</a>";
                echo "<a href='home_page.php?status=2&id=" . $baris['id'] . "' class='btn btn-primary'>Hapus</a>";
                echo "</td>";
                echo "</tr>";
                $nomor++;
            }
        } else {
            echo "<tr><td colspan='5'>Tidak ada hasil yang ditemukan.</td></tr>";
        }

        mysqli_stmt_close($stmt);
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
}

mysqli_close($koneksi);
?>
