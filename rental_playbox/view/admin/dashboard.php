<?php
session_start();
require '../../koneksi.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    header("Location: ../../login.php");
    exit();
}

// Hitung statistik
$stats = [
    'total_ps4' => $conn->query("SELECT COUNT(*) FROM playbox_ps4")->fetch_row()[0],
    'ps4_tersedia' => $conn->query("SELECT COUNT(*) FROM playbox_ps4 WHERE status = 'tersedia'")->fetch_row()[0],
    'total_sewa' => $conn->query("SELECT COUNT(*) FROM penyewaan")->fetch_row()[0],
    'total_pendapatan' => $conn->query("SELECT SUM(total_harga) FROM penyewaan WHERE status = 'selesai'")->fetch_row()[0] ?? 0
];

// Ambil 5 sewa terbaru
$sewa_terbaru = $conn->query("
    SELECT p.*, u.nama 
    FROM penyewaan p
    JOIN users u ON p.id_user = u.id_user
    ORDER BY p.tanggal_sewa DESC
    LIMIT 5
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Rental Playbox</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container py-4">
        <h2 class="mb-4">Admin Dashboard</h2>
        
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total PS4</h5>
                        <p class="card-text display-4"><?= $stats['total_ps4'] ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h5 class="card-title">PS4 Tersedia</h5>
                        <p class="card-text display-4"><?= $stats['ps4_tersedia'] ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Sewa</h5>
                        <p class="card-text display-4"><?= $stats['total_sewa'] ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card bg-warning text-dark">
                    <div class="card-body">
                        <h5 class="card-title">Total Pendapatan</h5>
                        <p class="card-text display-4">Rp <?= number_format($stats['total_pendapatan'], 0, ',', '.') ?></p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card shadow">
            <div class="card-header bg-white">
                <h5 class="mb-0">Penyewaan Terbaru</h5>
            </div>
            <div class="card-body">
                <?php if ($sewa_terbaru->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead class="table-light">
                                <tr>
                                    <th>ID Sewa</th>
                                    <th>Nama User</th>
                                    <th>Tanggal Sewa</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($sewa = $sewa_terbaru->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= $sewa['id_sewa'] ?></td>
                                        <td><?= $sewa['nama'] ?></td>
                                        <td><?= date('d M Y', strtotime($sewa['tanggal_sewa'])) ?></td>
                                        <td>Rp <?= number_format($sewa['total_harga'], 0, ',', '.') ?></td>
                                        <td>
                                            <span class="badge bg-<?= 
                                                $sewa['status'] == 'selesai' ? 'success' : 
                                                ($sewa['status'] == 'batal' ? 'danger' : 'warning') 
                                            ?>">
                                                <?= ucfirst($sewa['status']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="sewa.php?action=detail&id=<?= $sewa['id_sewa'] ?>" class="btn btn-sm btn-info">Detail</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">Belum ada data penyewaan</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>