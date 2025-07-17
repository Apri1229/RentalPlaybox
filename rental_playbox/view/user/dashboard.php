<?php
session_start();
require '../../koneksi.php';

if (!isset($_SESSION['user'])) {
    header("Location: ../../login.php");
    exit();
}

$user_id = $_SESSION['user']['id_user'];

// Ambil riwayat sewa
$sql = "SELECT p.*, SUM(d.subtotal) as total 
        FROM penyewaan p
        LEFT JOIN detail_sewa d ON p.id_sewa = d.id_sewa
        WHERE p.id_user = ?
        GROUP BY p.id_sewa
        ORDER BY p.tanggal_sewa DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Rental Playbox</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container py-4">
        <h2 class="mb-4">Dashboard User</h2>
        
        <div class="card mb-4 shadow">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Riwayat Penyewaan</h5>
            </div>
            <div class="card-body">
                <?php if ($result->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID Sewa</th>
                                    <th>Tanggal Sewa</th>
                                    <th>Tanggal Kembali</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($sewa = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= $sewa['id_sewa'] ?></td>
                                        <td><?= date('d M Y', strtotime($sewa['tanggal_sewa'])) ?></td>
                                        <td><?= date('d M Y', strtotime($sewa['tanggal_kembali'])) ?></td>
                                        <td>Rp <?= number_format($sewa['total'], 0, ',', '.') ?></td>
                                        <td>
                                            <span class="badge bg-<?= 
                                                $sewa['status'] == 'selesai' ? 'success' : 
                                                ($sewa['status'] == 'batal' ? 'danger' : 'warning') 
                                            ?>">
                                                <?= ucfirst($sewa['status']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="proses_sewa.php?action=detail&id=<?= $sewa['id_sewa'] ?>" class="btn btn-sm btn-info">Detail</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">Anda belum memiliki riwayat penyewaan</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>