<?php
// Sisipkan file koneksi.php
require_once('koneksi.php');

if (isset($_GET['keyword'])) {
    $keyword = mysqli_real_escape_string($koneksi, $_GET['keyword']);

    // Query pencarian berdasarkan kata kunci
    $sql = "SELECT * FROM tbl_task WHERE Tugas LIKE ?";
    $stmt = mysqli_prepare($koneksi, $sql);

    if ($stmt) {
        $likeKeyword = "%" . $keyword . "%";
        mysqli_stmt_bind_param($stmt, "s", $likeKeyword);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            $nomor = 1;
            while ($baris = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . $nomor . "</td>";
                echo "<td>" . $baris['Tugas'] . "</td>";
                echo "<td>" . $baris['Status'] . "</td>";
                echo "<td>" . $baris['Tanggal'] . "</td>";
                echo "<td>";
                echo "<a href='edit_task.php?id=" . $baris['id'] . "'>Edit</a> |";
                echo "<a href='home_page.php?status=2&id=" . $baris['id'] . "'>Hapus</a> |";
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
