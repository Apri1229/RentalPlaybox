<?php
session_start();
require '../../koneksi.php';

if (!isset($_SESSION['user'])) {
    header("Location: ../../login.php");
    exit();
}

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'add':
        $id_ps4 = $_POST['id_ps4'];
        $durasi = $_POST['durasi'];
        
        $sql = "SELECT * FROM playbox_ps4 WHERE id_ps4 = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_ps4);
        $stmt->execute();
        $result = $stmt->get_result();
        $ps4 = $result->fetch_assoc();
        
        if ($ps4) {
            $_SESSION['cart_ps4'][] = [
                'id' => $ps4['id_ps4'],
                'nama' => $ps4['nama_paket'],
                'jenis' => $ps4['jenis'],
                'harga' => $ps4['harga_sewa'],
                'durasi' => $durasi
            ];
            $_SESSION['success'] = "Paket PS4 berhasil ditambahkan ke keranjang";
        }
        header("Location: sewa_ps4.php");
        exit();
        
    case 'remove':
        $id = $_GET['id'];
        foreach ($_SESSION['cart_ps4'] as $key => $item) {
            if ($item['id'] == $id) {
                unset($_SESSION['cart_ps4'][$key]);
                break;
            }
        }
        $_SESSION['success'] = "Item berhasil dihapus dari keranjang";
        header("Location: sewa_ps4.php");
        exit();
        
    case 'clear':
        $_SESSION['cart_ps4'] = [];
        $_SESSION['success'] = "Keranjang berhasil dikosongkan";
        header("Location: sewa_ps4.php");
        exit();
        
    case 'checkout':
        if (empty($_SESSION['cart_ps4'])) {
            $_SESSION['error'] = "Keranjang sewa kosong";
            header("Location: sewa_ps4.php");
            exit();
        }
        
        $tanggal_sewa = $_POST['tanggal_sewa'];
        $id_user = $_SESSION['user']['id_user'];
        $total = 0;
        
        // Hitung total harga
        foreach ($_SESSION['cart_ps4'] as $item) {
            $total += $item['harga'] * $item['durasi'];
        }
        
        // Hitung tanggal kembali
        $max_durasi = max(array_column($_SESSION['cart_ps4'], 'durasi'));
        $tanggal_kembali = date('Y-m-d', strtotime($tanggal_sewa . " + $max_durasi days"));
        
        // Simpan data penyewaan
        $sql = "INSERT INTO penyewaan 
                (id_user, tanggal_sewa, tanggal_kembali, total_harga, status) 
                VALUES (?, ?, ?, ?, 'diproses')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issd", $id_user, $tanggal_sewa, $tanggal_kembali, $total);
        $stmt->execute();
        $id_sewa = $stmt->insert_id;
        
        // Simpan detail penyewaan
        foreach ($_SESSION['cart_ps4'] as $item) {
            $subtotal = $item['harga'] * $item['durasi'];
            
            $sql_detail = "INSERT INTO detail_sewa 
                          (id_sewa, id_ps4, durasi, subtotal) 
                          VALUES (?, ?, ?, ?)";
            $stmt_detail = $conn->prepare($sql_detail);
            $stmt_detail->bind_param("iiid", $id_sewa, $item['id'], $item['durasi'], $subtotal);
            $stmt_detail->execute();
            
            // Update status PS4
            $sql_update = "UPDATE playbox_ps4 SET status = 'disewa' WHERE id_ps4 = ?";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bind_param("i", $item['id']);
            $stmt_update->execute();
        }
        
        // Kosongkan keranjang
        $_SESSION['cart_ps4'] = [];
        $_SESSION['success'] = "Penyewaan PlayStation berhasil diproses!";
        header("Location: dashboard.php");
        exit();
        
    case 'detail':
        $id_sewa = $_GET['id'];
        $id_user = $_SESSION['user']['id_user'];
        
        // Cek apakah sewa milik user
        $sql = "SELECT * FROM penyewaan WHERE id_sewa = ? AND id_user = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $id_sewa, $id_user);
        $stmt->execute();
        $result = $stmt->get_result();
        $sewa = $result->fetch_assoc();
        
        if (!$sewa) {
            $_SESSION['error'] = "Data penyewaan tidak ditemukan";
            header("Location: dashboard.php");
            exit();
        }
        
        // Ambil detail sewa
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
        
    default:
        header("Location: sewa_ps4.php");
        exit();
}