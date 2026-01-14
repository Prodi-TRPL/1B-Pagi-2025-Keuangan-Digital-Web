<!DOCTYPE html>
<html lang="id">

<?php
include '../../../Database/koneksi.php';

// rekap pertahun
$qRekap = mysqli_query($koneksi,
    "SELECT 
        YEAR(tanggal) AS tahun,
        SUM(CASE WHEN kategori='Pemasukan' THEN jumlah ELSE 0 END) AS pemasukan,
        SUM(CASE WHEN kategori='Pengeluaran' THEN jumlah ELSE 0 END) AS pengeluaran
     FROM catatan
     GROUP BY YEAR(tanggal)
     ORDER BY YEAR(tanggal)"
);

// total semua
$total_semua_pemasukan = mysqli_fetch_assoc(
    mysqli_query($koneksi, "SELECT SUM(jumlah) AS total FROM catatan WHERE kategori='Pemasukan'")
)['total'] ?? 0;

$total_semua_pengeluaran = mysqli_fetch_assoc(
    mysqli_query($koneksi, "SELECT SUM(jumlah) AS total FROM catatan WHERE kategori='Pengeluaran'")
)['total'] ?? 0;

$saldo_akhir = $total_semua_pemasukan - $total_semua_pengeluaran;
?>


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Tahunan</title>
    <link rel="stylesheet" href="laporan_tahunan_oke.css">
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
                <a href="../dashboard/dashboard_ketua.php" class="nav-link">
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
                        <a href="../pendaftaran akun/daftar warga.php" class="nav-link dropdown-link">Pendaftaran Akun</a>
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
                        <a href="../Grafik/grafik_bulanan.php" class="nav-link dropdown-link">Bulanan</a>
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
        <h2>Laporan Tahunan</h2>
        <div class="cards">
            <div class="card">
                <h4>Total Pemasukan</h4>
                <p>Rp <?= number_format($total_semua_pemasukan,0,',','.'); ?></p>
            </div>
            <div class="card">
                <h4>Total Pengeluaran</h4>
                <p>Rp <?= number_format($total_semua_pengeluaran,0,',','.'); ?></p>
            </div>
            <div class="card">
                <h4>Saldo Akhir</h4>
                <p>Rp <?= number_format($saldo_akhir,0,',','.'); ?></p>
            </div>
        </div>
        <div class="table-wrapper">
            <table>
                <caption>Rekap Tahunan</caption>
                <thead>
                    <tr>
                        <th>Tahun</th>
                        <th>Pemasukan</th>
                        <th>Pengeluaran</th>
                        <th>Saldo Akhir</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $saldo_sebelumnya = 0;
                    while ($row = mysqli_fetch_assoc($qRekap)) :
                        $saldo_tahun_ini = $saldo_sebelumnya + $row['pemasukan'] - $row['pengeluaran'];
                    ?>
                    <tr>
                        <td><?= $row['tahun'] ?></td>
                        <td>Rp <?= number_format($row['pemasukan'],0,',','.') ?></td>
                        <td>Rp <?= number_format($row['pengeluaran'],0,',','.') ?></td>
                        <td>Rp <?= number_format($saldo_tahun_ini,0,',','.') ?></td>
                        <td class="aksi-col">
                            <!-- Tombol History per tahun -->
                            <form action="history_tahunan.php" method="GET" style="display:inline;">
                                <input type="hidden" name="tahun" value="<?= $row['tahun'] ?>">
                                <button type="submit" class="btn-history-btn">History</button>
                            </form>

                            <!-- Tombol Export Bulanan -->
                            <a href="laporan.php?mode=bulanan_per_tahun&tahun=<?= $row['tahun'] ?>" 
                            target="_blank" class="btn-export-btn">
                            Export
                            </a>
                        </td>
                    </tr>
                    <?php
                        $saldo_sebelumnya = $saldo_tahun_ini;
                    endwhile;
                    ?>
                </tbody>
            </table>
        </div>
        <a href="laporan.php?mode=tahunan" target="_blank" class="btn-export-final">Export Laporan Tahunan</a>
    </div>

<script src="laporan_tahunan.js"></script>

</body>

</html>