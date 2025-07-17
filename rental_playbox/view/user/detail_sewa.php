<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Sewa - Rental Playbox</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container py-4">
        <h2 class="mb-4">Detail Penyewaan #<?= $sewa['id_sewa'] ?></h2>
        
        <div class="card mb-4 shadow">
            <div class="card-header bg-primary text-white">
                <h5>Informasi Penyewaan</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Tanggal Sewa:</strong> <?= date('d M Y', strtotime($sewa['tanggal_sewa'])) ?></p>
                        <p><strong>Tanggal Kembali:</strong> <?= date('d M Y', strtotime($sewa['tanggal_kembali'])) ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Total Harga:</strong> Rp <?= number_format($sewa['total_harga'], 0, ',', '.') ?></p>
                        <p><strong>Status:</strong> 
                            <span class="badge bg-<?= 
                                $sewa['status'] == 'selesai' ? 'success' : 
                                ($sewa['status'] == 'batal' ? 'danger' : 'warning') 
                            ?>">
                                <?= ucfirst($sewa['status']) ?>
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card shadow">
            <div class="card-header bg-success text-white">
                <h5>Detail PlayStation</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Nama Paket</th>
                                <th>Jenis</th>
                                <th>Durasi</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($item = $detail_items->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $item['nama_paket'] ?></td>
                                    <td><?= $item['jenis'] ?></td>
                                    <td><?= $item['durasi'] ?> hari</td>
                                    <td>Rp <?= number_format($item['subtotal'], 0, ',', '.') ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="mt-4">
            <a href="dashboard.php" class="btn btn-primary">Kembali</a>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>