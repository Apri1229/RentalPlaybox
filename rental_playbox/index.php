<?php
session_start();
require 'koneksi.php';

// Ambil 4 PS4 terbaru
$sql = "SELECT * FROM playbox_ps4 WHERE status = 'tersedia' ORDER BY id_ps4 DESC LIMIT 4";
$ps4_list = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rental Playbox - Sewa PlayStation 4</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include 'view/user/navbar.php'; ?>

    <div class="container mt-4">
        <div class="hero-section text-center p-5 bg-dark text-white rounded">
            <h1 class="display-4">Rental Playbox</h1>
            <p class="lead">Sewa Playbox Console dengan harga terjangkau</p>
            <a href="view/user/sewa_ps4.php" class="btn btn-primary btn-lg mt-3">Sewa Sekarang</a>
        </div>

        <h2 class="text-center my-5">Paket Playbox Tersedia</h2>
        
        <div class="row">
            <?php while($ps4 = $ps4_list->fetch_assoc()): ?>
                <div class="col-md-3 mb-4">
                    <div class="card h-100 shadow">
                        <img src="uploads/ps4/<?= $ps4['foto'] ?>" class="card-img-top" alt="<?= $ps4['nama_paket'] ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?= $ps4['nama_paket'] ?></h5>
                            <p class="card-text"><?= $ps4['jenis'] ?></p>
                            <p class="text-success fw-bold">Rp <?= number_format($ps4['harga_sewa'], 0, ',', '.') ?>/hari</p>
                        </div>
                        <div class="card-footer bg-white">
                            <a href="view/user/sewa_ps4.php" class="btn btn-primary w-100">Sewa</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <?php include 'view/user/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>