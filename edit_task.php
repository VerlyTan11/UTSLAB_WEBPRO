<?php
require_once('koneksi.php');

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($koneksi, $_GET['id']);

    // Query untuk mendapatkan tugas berdasarkan ID
    $sql = "SELECT * FROM tbl_task WHERE id = ?";
    $stmt = mysqli_prepare($koneksi, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result->num_rows == 1) {
            $row = mysqli_fetch_assoc($result);
        } else {
            echo "Tugas tidak ditemukan.";
            exit;
        }
        mysqli_stmt_close($stmt);
    } else {
        echo "Error: " . mysqli_error($koneksi);
        exit;
    }
}

if (isset($_POST['update_task'])) {
    $newTask = $_POST['new_task'];
    $newDate = $_POST['new_date'];
    $id = $_POST['task_id'];

    $sql = "UPDATE tbl_task SET Tugas = ?, Tanggal = ? WHERE id = ?";
    $stmt = mysqli_prepare($koneksi, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ssi", $newTask, $newDate, $id);
        $result = mysqli_stmt_execute($stmt);

        if ($result) {
            header("Location: home_page.php"); // Redirect kembali ke halaman utama setelah berhasil mengedit
            exit;
        } else {
            echo "Error: " . mysqli_error($koneksi);
        }

        mysqli_stmt_close($stmt);
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Task</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="text-center mb-4">
                    <h2 class="display-4 display-md-6">Edit Task</h2>
                </div>
                <form action="edit_task.php" method="POST" class="mb-3">
                    <input type="hidden" name="task_id" value="<?php echo $row['id']; ?>">
                    <div class="input-group mb-3">
                        <div class="col-md-10">
                            <input type="text" class="form-control" name="new_task" value="<?php echo $row['Tugas']; ?>" required>
                        </div>
                        <div class="col-md-4">
                            <input type="date" class="form-control" name="new_date" value="<?php echo $row['Tanggal']; ?>">
                        </div>
                        <div class="input-group-append p-2">
                            <button class="btn btn-primary" type="submit" name="update_task">Simpan Perubahan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
