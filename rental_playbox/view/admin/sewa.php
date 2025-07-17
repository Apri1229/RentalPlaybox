<?php
session_start();
require '../../koneksi.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    header("Location: ../../login.php");
    exit();
}

$action = $_GET['action'] ?? '';

if ($action == 'detail' && isset($_GET['id'])) {
    $id_sewa = $_GET['id'];
    
    // Get penyewaan info
    $sql_sewa = "SELECT p.*, u.nama 
                FROM penyewaan p
                JOIN users u ON p.id_user = u.id_user
                WHERE p.id_sewa = ?";
    $stmt_sewa = $conn->prepare($sql_sewa);
    $stmt_sewa->bind_param("i", $id_sewa);
    $stmt_sewa->execute();
    $result_sewa = $stmt_sewa->get_result();
    $sewa = $result_sewa->fetch_assoc();
    
    if (!$sewa) {
        $_SESSION['error'] = "Data penyewaan tidak ditemukan";
        header("Location: sewa.php");
        exit();
    }
    
    // Get detail items
    $sql_detail = "SELECT d.*, p.nama_paket, p.jenis 
                  FROM detail_sewa d
                  JOIN playbox_ps4 p ON d.id_ps4 = p.id_ps4
                  WHERE d.id_sewa = ?";
    $stmt_detail = $conn->prepare($sql_detail);
    $stmt_detail->bind_param("i", $id_sewa);
    $stmt_detail->execute();
    $detail_items = $stmt_detail->get_result();
    
    include 'detail_sewa.php';
    exit();
}

if ($action == 'update_status' && isset($_GET['id'])) {
    $id_sewa = $_GET['id'];
    $status = $_GET['status'];
    
    $sql = "UPDATE penyewaan SET status = ? WHERE id_sewa = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $status, $id_sewa);
    
    if ($stmt->execute()) {
        // Jika status selesai, kembalikan PS4 ke stok
        if ($status == 'selesai') {
            $sql_detail = "SELECT id_ps4 FROM detail_sewa WHERE id_sewa = ?";
            $stmt_detail = $conn->prepare($sql_detail);
            $stmt_detail->bind_param("i", $id_sewa);
            $stmt_detail->execute();
            $result = $stmt_detail->get_result();
            
            while ($row = $result->fetch_assoc()) {
                $sql_update = "UPDATE playbox_ps4 SET status = 'tersedia' WHERE id_ps4 = ?";
                $stmt_update = $conn->prepare($sql_update);
                $stmt_update->bind_param("i", $row['id_ps4']);
                $stmt_update->execute();
            }
        }
        
        $_SESSION['success'] = "Status penyewaan berhasil diupdate";
    } else {
        $_SESSION['error'] = "Gagal mengupdate status penyewaan";
    }
    
    header("Location: sewa.php");
    exit();
}

// Get all penyewaan
$sql = "SELECT p.*, u.nama 
        FROM penyewaan p
        JOIN users u ON p.id_user = u.id_user
        ORDER BY p.tanggal_sewa DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link href="../../assets/css/style.css" rel="stylesheet">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Penyewaan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container py-4">
        <h2 class="mb-4">Kelola Penyewaan</h2>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
        
        <div class="card shadow">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead class="table-primary">
                            <tr>
                                <th>ID Sewa</th>
                                <th>Nama User</th>
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
                                    <td><?= $sewa['nama'] ?></td>
                                    <td><?= date('d M Y', strtotime($sewa['tanggal_sewa'])) ?></td>
                                    <td><?= date('d M Y', strtotime($sewa['tanggal_kembali'])) ?></td>
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
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                Ubah Status
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="sewa.php?action=update_status&id=<?= $sewa['id_sewa'] ?>&status=diproses">Diproses</a></li>
                                                <li><a class="dropdown-item" href="sewa.php?action=update_status&id=<?= $sewa['id_sewa'] ?>&status=selesai">Selesai</a></li>
                                                <li><a class="dropdown-item" href="sewa.php?action=update_status&id=<?= $sewa['id_sewa'] ?>&status=batal">Batal</a></li>
                                            </ul>
                                        </div>
                                        <a href="sewa.php?action=detail&id=<?= $sewa['id_sewa'] ?>" class="btn btn-sm btn-info mt-1">Detail</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>