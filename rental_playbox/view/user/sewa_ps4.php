<?php
session_start();
require '../../koneksi.php';

if (!isset($_SESSION['user'])) {
    header("Location: ../../login.php");
    exit();
}

// Inisialisasi keranjang
if (!isset($_SESSION['cart_ps4'])) {
    $_SESSION['cart_ps4'] = [];
}

// Ambil PS4 tersedia
$sql = "SELECT * FROM playbox_ps4 WHERE status = 'tersedia'";
$ps4_list = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sewa PS4 - Rental Playbox</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container py-4">
        <h2 class="text-center mb-4">Sewa Playbox</h2>
        
        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4 shadow">
                    <div class="card-header bg-primary text-white">
                        <h4>Pilihan Paket Playbox</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php while($ps4 = $ps4_list->fetch_assoc()): ?>
                                <div class="col-md-6 mb-4">
                                    <div class="card h-100">
                                        <img src="../../uploads/ps4/<?= $ps4['foto'] ?>" class="card-img-top" alt="<?= $ps4['nama_paket'] ?>">
                                        <div class="card-body">
                                            <h5 class="card-title"><?= $ps4['nama_paket'] ?></h5>
                                            <p class="card-text"><?= $ps4['jenis'] ?></p>
                                            <p class="text-success fw-bold">Rp <?= number_format($ps4['harga_sewa'], 0, ',', '.') ?>/hari</p>
                                        </div>
                                        <div class="card-footer">
                                            <form method="post" action="proses_sewa.php?action=add">
                                                <input type="hidden" name="id_ps4" value="<?= $ps4['id_ps4'] ?>">
                                                <div class="input-group">
                                                    <input type="number" name="durasi" class="form-control" min="1" value="1" placeholder="Hari">
                                                    <button class="btn btn-primary" type="submit">+ Keranjang</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card shadow">
                    <div class="card-header bg-success text-white">
                        <h4>Keranjang Sewa</h4>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($_SESSION['cart_ps4'])): ?>
                            <ul class="list-group mb-3">
                                <?php $total = 0; ?>
                                <?php foreach($_SESSION['cart_ps4'] as $item): ?>
                                    <?php 
                                        $subtotal = $item['harga'] * $item['durasi'];
                                        $total += $subtotal;
                                    ?>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <?= $item['nama'] ?> (<?= $item['durasi'] ?> hari)
                                        <span class="badge bg-primary rounded-pill">
                                            Rp <?= number_format($subtotal, 0, ',', '.') ?>
                                        </span>
                                    </li>
                                <?php endforeach; ?>
                                <li class="list-group-item fw-bold d-flex justify-content-between">
                                    <span>Total</span>
                                    <span>Rp <?= number_format($total, 0, ',', '.') ?></span>
                                </li>
                            </ul>
                            
                            <form method="post" action="proses_sewa.php?action=checkout">
                                <div class="mb-3">
                                    <label class="form-label">Tanggal Sewa</label>
                                    <input type="date" name="tanggal_sewa" class="form-control" required 
                                           min="<?= date('Y-m-d') ?>">
                                </div>
                                <button type="submit" class="btn btn-success w-100">Proses Sewa</button>
                            </form>
                            <a href="proses_sewa.php?action=clear" class="btn btn-danger w-100 mt-2">Kosongkan Keranjang</a>
                        <?php else: ?>
                            <div class="alert alert-info">Keranjang sewa kosong</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>