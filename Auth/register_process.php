<?php
require_once('db_connect.php'); // Sambungkan ke database

$registrationMessage = "";

if (isset($_POST['username']) && isset($_POST['email']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $email, $password);

    if ($stmt->execute()) {
        $registrationMessage = "Registrasi berhasil. Anda akan ke page login dalam 3 detik.";
    } else {
        $registrationMessage = "Registrasi gagal, silahkan coba lagi. Anda akan ke page register dalam 3 detik.";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <div class="main-container d-flex justify-content-center align-items-center" style="height: 100vh;">
        <div class="card login-page col-lg-4">
            <div class="card-body">
                <h2 class="card-title text-center" style="font-weight: 700;">REGISTER</h2>
            </div>

            <div class="card-text">
                <?php if (!empty($registrationMessage)) : ?>
                <div class="alert alert-success" role="alert">
                    <?php echo $registrationMessage; ?>
                </div>
                <script>
                setTimeout(function() {
                    window.location.href = 'login.html';
                }, 3000); // 3 detik
                </script>
                <?php else : ?>
                <div class="alert alert-warning" role="alert">
                    <?php echo $registrationMessage; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>

</html>