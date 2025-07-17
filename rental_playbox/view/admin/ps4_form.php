
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($ps4) ? 'Edit' : 'Tambah' ?> Paket PS4</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../../assets/css/style.css" rel="stylesheet">
</head>
</html>
<body>
    <?php include 'navbar.php'; ?>
    <!-- ...existing form code... -->
    <?php include 'footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
<div class="container py-4">
    <h2 class="mb-4"><?= ucfirst($action) ?> Paket PlayStation 4</h2>
    
    <div class="card shadow">
        <div class="card-body">
            <form method="post" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?= $ps4['id_ps4'] ?? '' ?>">
                
                <?php if (isset($ps4['foto'])): ?>
                    <input type="hidden" name="old_foto" value="<?= $ps4['foto'] ?>">
                <?php endif; ?>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Nama Paket</label>
                            <input type="text" class="form-control" name="nama_paket" required
                                   value="<?= $ps4['nama_paket'] ?? '' ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Jenis PlayStation</label>
                            <select class="form-select" name="jenis" required>
                                <option value="">Pilih Jenis</option>
                                <option value="PS4 Slim" <?= (isset($ps4['jenis'])) && $ps4['jenis'] == 'PS4 Slim' ? 'selected' : '' ?>>PS4 Slim</option>
                                <option value="PS4 Pro" <?= (isset($ps4['jenis'])) && $ps4['jenis'] == 'PS4 Pro' ? 'selected' : '' ?>>PS4 Pro</option>
                                <option value="PS4 VR Bundle" <?= (isset($ps4['jenis'])) && $ps4['jenis'] == 'PS4 VR Bundle' ? 'selected' : '' ?>>PS4 VR Bundle</option>
                            </select>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Stok</label>
                                <input type="number" class="form-control" name="stok" min="1" required
                                       value="<?= $ps4['stok'] ?? 1 ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Harga Sewa per Hari</label>
                                <input type="number" class="form-control" name="harga_sewa" min="10000" required
                                       value="<?= $ps4['harga_sewa'] ?? '' ?>">
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Include Game</label>
                            <input type="text" class="form-control" name="include_game" 
                                   placeholder="Contoh: FIFA 23, GTA V, God of War"
                                   value="<?= $ps4['include_game'] ?? '' ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea class="form-control" name="deskripsi" rows="3"><?= $ps4['deskripsi'] ?? '' ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Foto PlayStation</label>
                            <input type="file" class="form-control" name="foto" accept="image/*">
                            
                            <?php if (isset($ps4['foto']) && $ps4['foto']): ?>
                                <div class="mt-2">
                                    <img src="../../uploads/ps4/<?= $ps4['foto'] ?>" alt="Foto PS4" style="max-height: 150px;" class="img-thumbnail">
                                    <p class="text-muted mt-1"><?= $ps4['foto'] ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between">
                    <a href="ps4.php" class="btn btn-secondary">Kembali</a>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>