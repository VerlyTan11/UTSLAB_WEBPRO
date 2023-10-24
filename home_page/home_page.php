<?php
require_once('koneksi.php');

if (isset($_GET['status'])) {
    if ($_GET['status'] == '2' && isset($_GET['id'])) {
        $id = mysqli_real_escape_string($koneksi, $_GET['id']);

        // Hapus tugas berdasarkan ID
        $sql = "DELETE FROM tbl_task WHERE id = ?";
        $stmt = mysqli_prepare($koneksi, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $id);
            $result = mysqli_stmt_execute($stmt);

            if ($result) {
                // Redirect kembali ke halaman utama setelah menghapus tugas
                header("Location: home_page.php");
                exit;
            } else {
                echo "Error: " . mysqli_error($koneksi);
            }

            mysqli_stmt_close($stmt);
        } else {
            echo "Error: " . mysqli_error($koneksi);
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['task_start'])) {
        $startedTaskId = $_POST['task_start'];

        // Periksa apakah sudah ada tugas dalam status "In Progress"
        $inProgressCheck = "SELECT COUNT(*) as inProgressCount FROM tbl_task WHERE Status = 'In Progress'";
        $inProgressResult = mysqli_query($koneksi, $inProgressCheck);
        $inProgressCount = mysqli_fetch_assoc($inProgressResult)['inProgressCount'];

        if ($inProgressCount < 1) {
            // Update status tugas yang di-start menjadi "In Progress"
            $sql = "UPDATE tbl_task SET Status = 'In Progress' WHERE id = ?";
            $stmt = mysqli_prepare($koneksi, $sql);

            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "i", $startedTaskId);
                $result = mysqli_stmt_execute($stmt);

                if ($result) {
                    // Redirect kembali ke halaman utama
                    header("Location: home_page.php");
                    exit;
                } else {
                    echo "Error: " . mysqli_error($koneksi);
                }

                mysqli_stmt_close($stmt);
            } else {
                echo "Error: " . mysqli_error($koneksi);
            }
        } else {
            echo "Hanya satu tugas yang dapat berstatus 'In Progress' sekaligus.";
        }
    }
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['task_name']) && isset($_POST['task_date'])) {
        // Menambahkan tugas baru
        $taskName = mysqli_real_escape_string($koneksi, $_POST['task_name']);
        $taskStatus = "Not yet started"; // Status default: Pending
        $taskDate = mysqli_real_escape_string($koneksi, $_POST['task_date']); // Ambil tanggal dari input

        $sql = "INSERT INTO tbl_task (Tugas, Status, Tanggal) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($koneksi, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "sss", $taskName, $taskStatus, $taskDate);
            $result = mysqli_stmt_execute($stmt);

            if (!$result) {
                echo "Error: " . mysqli_error($koneksi);
            }

            mysqli_stmt_close($stmt);
        } else {
            echo "Error: " . mysqli_error($koneksi);
        }
    }

    if (isset($_POST['task_done'])) {
        $done_tasks = $_POST['task_done'];

        // Perbarui status tugas menjadi "Selesai" (ubah ke 'Selesai')
        $sql = "UPDATE tbl_task SET Status = 'Selesai' WHERE id IN (" . implode(',', $done_tasks) . ")";
        $result = mysqli_query($koneksi, $sql);

        if (!$result) {
            echo "Error: " . mysqli_error($koneksi);
        }

        // Tambahkan perintah SQL untuk memindahkan tugas selesai ke bawah
        $sql = "UPDATE tbl_task SET id = id + " . count($done_tasks) . " WHERE Status = 'Selesai' AND id NOT IN (" . implode(',', $done_tasks) . ")";
        $result = mysqli_query($koneksi, $sql);

        if (!$result) {
            echo "Error: " . mysqli_error($koneksi);
        }
    }

    if (isset($_POST['task_start'])) {
        // ID tugas yang di-start
        $startedTaskId = $_POST['task_start'];

        // Periksa jika sudah ada tugas yang sedang berlangsung
        $inProgressCount = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as count FROM tbl_task WHERE Status = 'In Progress'"))['count'];

        if ($inProgressCount === 0) {
            // Update status tugas yang di-start menjadi "In Progress"
            $sql = "UPDATE tbl_task SET Status = 'In Progress' WHERE id = ?";
            $stmt = mysqli_prepare($koneksi, $sql);

            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "i", $startedTaskId);
                $result = mysqli_stmt_execute($stmt);

                if (!$result) {
                    echo "Error: " . mysqli_error($koneksi);
                }

                mysqli_stmt_close($stmt);
            } else {
                echo "Error: " . mysqli_error($koneksi);
            }
        } else {
            echo "Hanya satu tugas yang dapat berstatus 'In Progress' sekaligus.";
        }

        // Redirect kembali ke halaman utama
        header("Location: home_page.php");
    }
    // Redirect kembali ke halaman utama setelah selesai
    header("Location: home_page.php");
}

// Mengambil daftar tugas dari database dan mengurutkannya berdasarkan tanggal terkecil
$sql = "SELECT * FROM tbl_task ORDER BY (Status = 'Selesai') ASC, Tanggal ASC, id DESC";
$hasil = mysqli_query($koneksi, $sql);
?>

<!DOCTYPE html>
<html>

<head>
    <title>To-Do List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>

<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <!-- Tombol "Log Out" -->
                <form method="POST" action="home_page.php">
                    <div class="d-flex justify-content-end mt-3">
                        <button class="btn btn-danger" type="submit" name="logout">Log Out</button>
                        <?php
                            // Mulai atau lanjutkan sesi
                            session_start();

                            if (isset($_POST['logout'])) {
                                // Hapus semua data sesi
                                session_destroy();

                                // Redirect ke halaman login
                                header("Location: Auth/login.html");
                                exit;
                            }
                        ?>
                    </div>
                </form>

                <div class="text-center mb-4">
                    <h2 class="display-4">To-Do List</h2>
                </div>
                <!-- form untuk menambahkan tugas baru -->
                <form action="home_page.php" method="POST" class="mb-3">
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" placeholder="Tambahkan tugas baru" name="task_name" required>
                        <input type="date" class="form-control" name="task_date" required>
                        <div class="input-group-append">
                            <button class="btn btn-primary btn-block" type="submit">Tambah</button>
                        </div>
                    </div>
                </form>
                <!-- form untuk melakukan pencarian -->
                <div class="input-group mb-3">
                    <input type="text" class="form-control" id="searchTask" placeholder="Cari tugas...">
                    <div class="input-group-append">
                        <button class="btn btn-secondary" id="searchButton">Cari</button>
                    </div>
                </div>

                <!-- Daftar tugas di sini -->
                <form action="home_page.php" method="POST" id="taskForm">
                    <table class="table table-responsive">
                        <thead>
                            <tr class="border border-solid">
                                <th class="text-center">No</th>
                                <th class="text-center">Tugas</th>
                                <th class="text-center">Tanggal</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $nomor = 1; // Inisialisasi nomor
                            while ($baris = mysqli_fetch_assoc($hasil)) {
                                echo "<tr class='border border-solid text-center'>";
                                echo "<td>" . $nomor . "</td>";
                                echo "<td>" . $baris['Tugas'] . "</td>";
                                echo "<td>" . $baris['Tanggal'] . "</td>";
                                echo "<td>" . $baris['Status'] . "</td>";
                                echo "<td class='d-block d-md-flex flex-md-row justify-content-center grid gap-0 column-gap-3'>";
                                if ($baris['Status'] !== 'Selesai') {
                                    // Hanya tugas yang belum selesai yang bisa di-start dan didone
                                    echo "<a href='javascript:void(0)' class='done-link btn btn-primary mr-2 mb-2' data-id='" . $baris['id'] . "' onclick='statusStart(\"{$baris['id']}\")'>Start</a>";
                                    echo "<a href='javascript:void(0)' class='done-link btn btn-primary mr-2 mb-2' data-id='" . $baris['id'] . "' onclick='statusDone(\"{$baris['id']}\")'>Done</a>";
                                }
                                if ($baris['Status'] !== 'Selesai') {
                                    // Hanya tugas yang belum selesai yang bisa diedit
                                    echo "<a href='edit_task.php?id=" . $baris['id'] . "' class='btn btn-primary mr-2 mb-2'>Edit</a>";
                                }
                                echo "<a href='home_page.php?status=2&id=" . $baris['id'] . "' class='btn btn-primary mb-2'>Hapus</a>";
                                echo "</td>";
                                echo "</tr>";
                                $nomor++;
                            }

                            mysqli_free_result($hasil);
                            ?>
                        </tbody>
                    </table>
                    <input type="hidden" name="task_start[]" id="taskStartInput">
                </form>


                <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const searchButton = document.getElementById('searchButton');
                    const searchTaskInput = document.getElementById('searchTask');
                    const taskTable = document.querySelector('.table-responsive');

                    searchButton.addEventListener('click', function () {
                        const keyword = searchTaskInput.value;

                        // Kirim permintaan Ajax untuk mencari tugas berdasarkan kata kunci
                        fetch(`search_task.php?keyword=${keyword}`, {
                            method: 'GET'
                        })
                        .then(response => response.text())
                        .then(data => {
                            // Ganti isi tabel dengan hasil pencarian
                            taskTable.querySelector('tbody').innerHTML = data;
                        })
                        .catch(error => {
                            console.error('Terjadi kesalahan:', error);
                        });
                    });
                });

                function statusDone(id) {
                    fetch('done_task.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `task_done=${id}`,
                    })
                    .then(response => {
                        if (response.status === 200) {
                            // Refresh halaman setelah tugas selesai diubah
                            location.reload();
                        } else {
                            console.error('Gagal mengubah status tugas.');
                        }
                    })
                    .catch(error => {
                        console.error('Terjadi kesalahan:', error);
                    });
                }

                function statusStart(id) {
                    fetch('start_task.php?id=' + id, {
                        method: 'GET',
                    })
                    .then(response => {
                        if (response.status === 200) {
                            location.reload(); // Refresh halaman setelah tugas di-start
                        } else {
                            console.error('Gagal memulai tugas.');
                        }
                    })
                    .catch(error => {
                        console.error('Terjadi kesalahan:', error);
                    });
                }
                </script>
            </div>
        </div>
    </div>

</body>

</html>