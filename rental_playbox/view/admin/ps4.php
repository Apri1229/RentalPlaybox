<?php
session_start();
require '../../koneksi.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    header("Location: ../../login.php");
    exit();
}

$action = $_GET['action'] ?? '';

if ($action == 'add' || $action == 'edit') {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $nama = $_POST['nama_paket'];
        $jenis = $_POST['jenis'];
        $stok = $_POST['stok'];
        $harga = $_POST['harga_sewa'];
        $deskripsi = $_POST['deskripsi'];
        $games = $_POST['include_game'];
        $id = $_POST['id'] ?? null;
        
        // Handle file upload
        $foto = null;
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
            $target_dir = "../../uploads/ps4/";
            $file_ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
            $filename = 'ps4_' . uniqid() . '.' . $file_ext;
            $target_file = $target_dir . $filename;
            
            if (move_uploaded_file($_FILES['foto']['tmp_name'], $target_file)) {
                $foto = $filename;
                
                // Delete old photo if exists
                if ($action == 'edit' && isset($_POST['old_foto']) && $_POST['old_foto']) {
                    $old_file = $target_dir . $_POST['old_foto'];
                    if (file_exists($old_file)) {
                        unlink($old_file);
                    }
                }
            }
        } elseif ($action == 'edit' && isset($_POST['old_foto'])) {
            $foto = $_POST['old_foto'];
        }
        
        if ($action == 'add') {
            $sql = "INSERT INTO playbox_ps4 
                    (nama_paket, jenis, stok, harga_sewa, deskripsi, include_game, foto, status) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, 'tersedia')";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssiisss", $nama, $jenis, $stok, $harga, $deskripsi, $games, $foto);
        } else {
            $sql = "UPDATE playbox_ps4 SET 
                    nama_paket = ?, jenis = ?, stok = ?, harga_sewa = ?, 
                    deskripsi = ?, include_game = ?, foto = ? 
                    WHERE id_ps4 = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssiisssi", $nama, $jenis, $stok, $harga, $deskripsi, $games, $foto, $id);
        }
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Data PS4 berhasil " . ($action == 'add' ? 'ditambahkan' : 'diupdate');
            header("Location: ps4.php");
            exit();
        } else {
            $_SESSION['error'] = "Gagal menyimpan data PS4";
            header("Location: ps4.php?action=$action" . ($id ? "&id=$id" : ""));
            exit();
        }
    }
    
    // Display form
    $ps4 = null;
    if ($action == 'edit' && isset($_GET['id'])) {
        $id = $_GET['id'];
        $sql = "SELECT * FROM playbox_ps4 WHERE id_ps4 = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $ps4 = $result->fetch_assoc();
    }
    
    include 'ps4_form.php';
    exit();
}

if ($action == 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "DELETE FROM playbox_ps4 WHERE id_ps4 = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Paket PS4 berhasil dihapus";
    } else {
        $_SESSION['error'] = "Gagal menghapus paket PS4";
    }
    
    header("Location: ps4.php");
    exit();
}

// Get all PS4 packages
$sql = "SELECT * FROM playbox_ps4 ORDER BY jenis, nama_paket";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola PS4 - Rental Playbox</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../../assets/css/style.css" rel="stylesheet"> <!-- Tambahkan baris ini -->
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Kelola PlayStation 4</h2>
            <a href="ps4.php?action=add" class="btn btn-primary">
                <i class="bi bi-plus"></i> Tambah Paket
            </a>
        </div>
        
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
                                <th>ID</th>
                                <th>Nama Paket</th>
                                <th>Jenis</th>
                                <th>Stok</th>
                                <th>Harga Sewa</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $row['id_ps4'] ?></td>
                                    <td><?= $row['nama_paket'] ?></td>
                                    <td><?= $row['jenis'] ?></td>
                                    <td><?= $row['stok'] ?></td>
                                    <td>Rp <?= number_format($row['harga_sewa'], 0, ',', '.') ?></td>
                                    <td>
                                        <span class="badge bg-<?= $row['status'] == 'tersedia' ? 'success' : 'warning' ?>">
                                            <?= ucfirst($row['status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="ps4.php?action=edit&id=<?= $row['id_ps4'] ?>" class="btn btn-sm btn-warning">
                                            Edit
                                        </a>
                                        <a href="ps4.php?action=delete&id=<?= $row['id_ps4'] ?>" 
                                           class="btn btn-sm btn-danger" 
                                           onclick="return confirm('Hapus paket ini?')">
                                            Hapus
                                        </a>
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