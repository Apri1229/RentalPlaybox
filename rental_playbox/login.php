<?php
session_start();
require 'koneksi.php';

if (isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

$error = '';
$selected_role = 'user'; // Default role

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $selected_role = $_POST['role'];

    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        
        // Verifikasi password dan role
        if (password_verify($password, $user['password']) && $user['role'] == $selected_role) {
            $_SESSION['user'] = [
                'id_user' => $user['id_user'],
                'nama' => $user['nama'],
                'email' => $user['email'],
                'role' => $user['role']
            ];
            
            // Redirect berdasarkan role
            if ($user['role'] == 'admin') {
                header("Location: view/admin/dashboard.php");
            } else {
                header("Location: index.php");
            }
            exit();
        } else {
            $error = "Email, password, atau role tidak sesuai";
        }
    } else {
        $error = "Email tidak terdaftar";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Rental Playbox</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/login.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="text-center mb-4">
                    <h2 class="login-title">Rental Playbox</h2>
                    <p class="login-subtitle">Sistem Penyewaan PlayStation 4</p>
                </div>
                
                <div class="login-card shadow">
                    <div class="card-body p-4">
                        <h3 class="text-center mb-4">Login</h3>
                        
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?= $error ?></div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <!-- Role Selector -->
                            <div class="role-selector">
                                <label class="role-btn <?= $selected_role == 'user' ? 'active' : '' ?>">
                                    <input type="radio" name="role" value="user" <?= $selected_role == 'user' ? 'checked' : '' ?>> User
                                </label>
                                <label class="role-btn <?= $selected_role == 'admin' ? 'active' : '' ?>">
                                    <input type="radio" name="role" value="admin" <?= $selected_role == 'admin' ? 'checked' : '' ?>> Admin
                                </label>
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 login-btn">Login</button>
                        </form>
                        
                        <div class="login-links mt-3 text-center">
                            <p>Belum punya akun? <a href="register.php">Daftar disini</a></p>
                            <p><a href="index.php">Kembali ke beranda</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Aktifkan tombol role yang dipilih
        document.querySelectorAll('.role-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.role-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                this.querySelector('input').checked = true;
            });
        });
    </script>
</body>
</html>