<?php
require_once('db_connect.php'); // Sambungkan ke database

if (isset($_POST['username']) && isset($_POST['email']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    try {
        // Lindungi dari SQL Injection dengan menggunakan prepared statement
        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $password);

        if ($stmt->execute()) {
            // Registrasi berhasil, arahkan ke login.html dengan pesan berhasil
            header('Location: login.html?status=berhasil');
            exit; // Pastikan untuk menghentikan eksekusi setelah mengarahkan pengguna
        } else {
            // Registrasi gagal, arahkan kembali ke register.html dengan pesan gagal
            header('Location: register.html?status=gagal');
            exit;
        }

        $stmt->close();
    } catch (mysqli_sql_exception $e) {
        // Tangani pengecualian ketika terjadi kesalahan di SQL
        // Di sini, Anda dapat menambahkan penanganan khusus jika ada kesalahan duplikasi (misalnya username sudah ada)
        if ($e->getCode() == 1062) { // 1062 adalah kode kesalahan untuk entri duplikat
            // Kesalahan duplikasi, arahkan kembali ke register.html dengan pesan khusus
            header('Location: register.html?status=duplikat');
            exit;
        } else {
            // Kesalahan lain, arahkan kembali ke register.html dengan pesan kesalahan umum
            header('Location: register.html?status=gagal');
            exit;
        }
    }
}
?>
