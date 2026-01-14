<?php 
include '../../../Database/koneksi.php';
session_start();

if (!isset($_SESSION['nama'])) {
    // User belum login
    header("Location: ../../Daftar login/Login.php");
    exit;
}   
    $nik  = $_SESSION['nik'] ?? '';
    $nama = $_SESSION['nama'] ?? '';

$tahun = $_GET['tahun'] ?? date("Y"); // bisa diganti kalau mau filter tahun

// Membuat pemilihan tahun menjadi dynamic
$qTahun = mysqli_query($koneksi,
        "SELECT DISTINCT YEAR(tanggal) AS th
        FROM catatan
        ORDER BY th ASC"
);

$daftarTahun = [];
while ($r = mysqli_fetch_assoc($qTahun)) {
    $daftarTahun[] = $r['th'];
}

// Total pemasukan tahun ini
$total_pemasukan = mysqli_fetch_assoc(mysqli_query($koneksi,
    "SELECT SUM(jumlah) as total FROM catatan 
    WHERE kategori='Pemasukan' AND YEAR(tanggal)='$tahun'"
))['total'] ?? 0;

// Total pengeluaran tahun ini
$total_pengeluaran = mysqli_fetch_assoc(mysqli_query($koneksi,
    "SELECT SUM(jumlah) as total FROM catatan 
    WHERE kategori='Pengeluaran' AND YEAR(tanggal)='$tahun'"
))['total'] ?? 0;

// Untuk menghitung saldo yang tersisa
$uang_masuk = mysqli_fetch_assoc(mysqli_query($koneksi,
    "SELECT SUM(jumlah) as total FROM catatan 
    WHERE kategori='Pemasukan'"
))['total'] ?? 0;

$uang_keluar = mysqli_fetch_assoc(mysqli_query($koneksi,
    "SELECT SUM(jumlah) as total FROM catatan 
    WHERE kategori='Pengeluaran'"
))['total'] ?? 0;

$saldo_akhir = $uang_masuk - $uang_keluar;

// Rekap bulanan
$rekap = mysqli_query($koneksi,
    "SELECT 
        MONTH(tanggal) AS bulan,
        SUM(CASE WHEN kategori='Pemasukan' THEN jumlah ELSE 0 END) AS pemasukan,
        SUM(CASE WHEN kategori='Pengeluaran' THEN jumlah ELSE 0 END) AS pengeluaran
        FROM catatan
        WHERE YEAR(tanggal) = '$tahun'
        GROUP BY MONTH(tanggal)"
);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Bulanan</title>
    <link rel="stylesheet" href="../Laporan/laporan_bulanan_oke.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <link rel="shortcut icon" href="../../../foto/ICONB.png" type="image/x-icon">
</head>

<body>

<!--SIDEBAR-->
<aside class="sidebar">
    <!--Sidebar Header-->
    <header class="sidebar-header">
        <a href="#" class="header-logo">
            <img src="../../../foto/ICON.png" alt="KasWarga">
        </a>
        <button class="sidebar-toggler">
            <i class="fa-solid fa-chevron-left"></i>
        </button>
    </header>

    <nav class="sidebar-nav">
        <!--Primary Top Nav-->
        <ul class="nav-list primary-nav">
            <li class="nav-item">
                <a href="../dashboard/dashboard_sekre.php" class="nav-link">
                    <i class="fa-solid fa-house"></i>
                    <span class="nav-label">Dashboard</span>
                </a>
            </li>

            <!--Dropdown-->
            <li class="nav-item dropdown-container">
                <a href="dashboard.html" class="nav-link dropdown-toggle">
                    <i class="fa-solid fa-thumbtack"></i>
                    <span class="nav-label">Fitur</span>
                    <i class="fa-solid fa-chevron-down dropdown-icon"></i>
                </a>
                <!--dropdown Menu-->
                <ul class="dropdown-menu">
                    <li class="nav-item">
                        <a href="../riwayat transaksi/kelola_riwayat.php" class="nav-link dropdown-link">Kelola Riwayat</a>
                    </li>
                    <li class="nav-item">
                        <a href="../pembayaran/pembayaran.php" class="nav-link dropdown-link">Pembayaran</a>
                    </li>
                </ul>
            </li>
            <!--Dropdown-->
            <li class="nav-item dropdown-container">
                <a href="#" class="nav-link dropdown-toggle">
                    <i class="fa-solid fa-line-chart"></i>
                    <span class="nav-label">Grafik</span>
                    <i class="fa-solid fa-chevron-down dropdown-icon"></i>
                </a>
                <!--dropdown Menu-->
                <ul class="dropdown-menu">
                    <li class="nav-item">
                        <a href="../Grafik/Grafik_bulanan.php" class="nav-link dropdown-link">Bulanan</a>
                    </li>
                    <li class="nav-item">
                        <a href="../Grafik/Grafik_tahunan.php" class="nav-link dropdown-link">Tahunan</a>
                    </li>
                </ul>
            </li>
            <!--Dropdown-->
            <li class="nav-item dropdown-container">
                <a href="#" class="nav-link dropdown-toggle">
                    <i class="fa-solid fa-book"></i>
                    <span class="nav-label">Laporan</span>
                    <i class="fa-solid fa-chevron-down dropdown-icon"></i>
                </a>
                <!--dropdown Menu-->
                <ul class="dropdown-menu">
                    <li class="nav-item">
                        <a href="../Laporan/laporan bulanan.php" class="nav-link dropdown-link">Bulanan</a>
                    </li>
                    <li class="nav-item">
                        <a href="../Laporan/laporan tahunan.php" class="nav-link dropdown-link">Tahunan</a>
                    </li>
                </ul>
            </li>
            <!--Dropdown-->
            <li class="nav-item dropdown-container">
                <a href="#" class="nav-link dropdown-toggle">
                    <i class="fa-solid fa-cog"></i>
                    <span class="nav-label">Pengaturan</span>
                    <i class="fa-solid fa-chevron-down dropdown-icon"></i>
                </a>
                <!--dropdown Menu-->
                <ul class="dropdown-menu">
                    <li class="nav-item">
                        <a href="../../Daftar login/logout.php" class="nav-link dropdown-link">Keluar</a>
                    </li>
                </ul>
            </li>
        </ul>
    </nav>
</aside>

<div class="container">
        <div class="header-flex" style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px;">
            <h2 style="margin-bottom:0;">Laporan Bulanan</h2>
            <div class="custom-select-wrapper" style="margin: 0 0 0 20px;">
                <div class="select-selected">
                    <?= $tahun ?>
                </div>
                <div class="select-items select-hide">
                    <?php foreach ($daftarTahun as $th): ?>
                        <div data-value="<?= $th ?>" onclick="pilihTahun(<?= $th ?>)">
                            <?= $th ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

    <div class="cards">
        <div class="card">
            <h4>Total Pemasukan</h4>
            <p>Rp <?= number_format($total_pemasukan,0,',','.'); ?></p>
        </div>
        <div class="card">
            <h4>Total Pengeluaran</h4>
            <p>Rp <?= number_format($total_pengeluaran,0,',','.'); ?></p>
        </div>
        <div class="card">
            <h4>Sisa Saldo</h4>
            <p>Rp <?= number_format($saldo_akhir,0,',','.'); ?></p>
        </div>
    </div>

    <div class="table-wrapper">
        <table>
            <caption>Rekap Bulanan</caption>
            <thead>
                <tr>
                    <th>Bulan</th>
                    <th>Pemasukan</th>
                    <th>Pengeluaran</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $bulan_nama = [
                    1=>"Januari",2=>"Februari",3=>"Maret",4=>"April",5=>"Mei",6=>"Juni",
                    7=>"Juli",8=>"Agustus",9=>"September",10=>"Oktober",11=>"November",12=>"Desember"
                ];

                    $data_bulanan = [];
                        while ($row = mysqli_fetch_assoc($rekap)) {
                            $data_bulanan[$row['bulan']] = [
                                "pemasukan" => $row['pemasukan'],
                                "pengeluaran" => $row['pengeluaran'],
                                "saldo" => $row['pemasukan'] - $row['pengeluaran']
              ];
                }

                for ($i=1; $i<=12; $i++):
                ?>
                    <tr>
                        <td><?= $bulan_nama[$i] ?></td>
                    
                        <td>Rp <?= isset($data_bulanan[$i]) ? number_format($data_bulanan[$i]["pemasukan"],0,',','.') : 0 ?></td>
                        <td>Rp <?= isset($data_bulanan[$i]) ? number_format($data_bulanan[$i]["pengeluaran"],0,',','.') : 0 ?></td>
                    
                        <td class="aksi-col">
                            <!-- Tombol History -->
                            <form action="history.php" method="GET" style="display:inline;">
                                <input type="hidden" name="bulan" value="<?= $i ?>">
                                <input type="hidden" name="tahun" value="<?= $tahun ?>">
                                <button type="submit" class="btn-history-btn">History</button>
                            </form>

                            <!-- Tombol Export Bulanan -->
                            <a href="laporan.php?mode=bulanan&bulan=<?= $i ?>&tahun=<?= $tahun ?>" 
                               target="_blank" class="btn-export-btn">
                               Export
                            </a>
                        </td>
                    </tr>
                <?php endfor; ?>
            </tbody>
        </table>
    </div>

    <!-- Tombol laporan bulanan rekap setahun (tanpa parameter bulan) -->
    <a href="laporan.php?mode=bulanan&tahun=<?= $tahun ?>"  
        target="_blank" class="btn-export-final">
        Export Rekap Bulanan <?= $tahun ?>
    </a>

</div>

</body>
<script src="laporan_bulanan.js"></script>
</html>