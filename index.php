<?php
require_once 'config/koneksi.php';

// Ambil data sepatu
$search = isset($_GET['search']) ? bersihkan_input($_GET['search']) : '';
$query = "SELECT * FROM sepatu";

if (!empty($search)) {
    $query .= " WHERE nama LIKE '%$search%' OR merk LIKE '%$search%' OR warna LIKE '%$search%'";
}

$query .= " ORDER BY dibuat_kapan DESC";
$result = $koneksi->query($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toko Sepatu Online</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <i class="fas fa-shoe-prints"></i>
                    <h1>SepatuKu</h1>
                </div>
                <nav>
                    <ul>
                        <li><a href="index.php" class="active"><i class="fas fa-home"></i> Beranda</a></li>
                        <li><a href="admin.php"><i class="fas fa-cog"></i> Admin</a></li>
                        <li><a href="#produk"><i class="fas fa-shopping-bag"></i> Produk</a></li>
                        <li><a href="#kontak"><i class="fas fa-phone"></i> Kontak</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <section class="hero">
        <div class="container">
            <h2>Sepatu Terbaik untuk Setiap Langkah Anda</h2>
            <p>Temukan koleksi sepatu terbaru dengan harga terbaik</p>
        </div>
    </section>

    <div class="container">
        <h2 class="section-title" id="produk">Koleksi Sepatu</h2>
        
        <div style="margin-bottom: 20px; text-align: center;">
            <form method="GET" action="" style="display: inline-block;">
                <input type="text" name="search" placeholder="Cari sepatu..." 
                       value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>"
                       style="padding: 10px; width: 300px; max-width: 100%; border-radius: 5px; border: 1px solid #ddd;">
                <button type="submit" style="padding: 10px 20px; background: #3498db; color: white; border: none; border-radius: 5px; cursor: pointer;">
                    <i class="fas fa-search"></i> Cari
                </button>
            </form>
        </div>
        
        <div class="produk-grid">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($sepatu = $result->fetch_assoc()): ?>
                <div class="produk-card">
                    <div class="produk-gambar">
                        <?php if ($sepatu['gambar']): ?>
                            <img src="<?php echo htmlspecialchars($sepatu['gambar']); ?>" alt="<?php echo htmlspecialchars($sepatu['nama']); ?>">
                        <?php else: ?>
                            <img src="https://via.placeholder.com/300x200?text=Sepatu" alt="Sepatu">
                        <?php endif; ?>
                    </div>
                    <div class="produk-info">
                        <h3><?php echo htmlspecialchars($sepatu['nama']); ?></h3>
                        <div class="produk-meta">
                            <i class="fas fa-tag"></i> <?php echo htmlspecialchars($sepatu['merk']); ?> | 
                            <i class="fas fa-palette"></i> <?php echo htmlspecialchars($sepatu['warna']); ?>
                        </div>
                        <div class="harga"><?php echo format_rupiah($sepatu['harga']); ?></div>
                        <div class="stok">Stok: <?php echo $sepatu['stok']; ?></div>
                        <div class="aksi">
                            <a href="beli.php?id=<?php echo $sepatu['id']; ?>" class="btn-beli">
                                <i class="fas fa-shopping-cart"></i> Beli
                            </a>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p style="text-align: center; grid-column: 1/-1; padding: 2rem; color: #7f8c8d;">
                    <?php echo empty($search) ? 'Belum ada sepatu tersedia' : 'Tidak ditemukan sepatu dengan kata kunci: ' . htmlspecialchars($search); ?>
                </p>
            <?php endif; ?>
        </div>
    </div>

    <footer id="kontak">
        <div class="container">
            <h3>Toko Sepatu Ghally</h3>
            <p>Jalan Andhika Graha Citra, Kota Depok</p>
            <p>Email: tokosepatughally@gmail.com | Telp: (021) 1234-5678</p>
            <p>&copy; <?php echo date('Y'); ?> Toko SepatuKu. Semua hak dilindungi.</p>
        </div>
    </footer>

    <script src="js/script.js"></script>
</body>
</html>