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
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Iuran</title>
    <link rel="stylesheet" href="payment_ok.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <link rel="shortcut icon" href="../../../foto/ICONB.png" type="image/x-icon">
</head>

<body>

<div class="layout">
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
                <a href="../dashboard/dashboard.php" class="nav-link">
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
                        <a href="../status pembayaran/status_pembayaran.php" class="nav-link dropdown-link">Catatan Iuran</a>
                    </li>
                    <li class="nav-item">
                        <a href="../catatan pemasukan dan pengeluaran/catatan pemasukan dan pengeluaran.php" class="nav-link dropdown-link">Catatan Keuangan</a>
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

        <header class="top-header">
            <h1>Pembayaran Iuran</h1>
            <a href="modal_pembayaran.php" class="setting-btn">Pengaturan ⚙</a>
        </header>

    <!-- TAB PEMILIHAN IURAN -->
    <section class="tabs-section">
        <div class="tabs">
            <button class="tab active" data-tab="rutin">Iuran Rutin</button>
            <button class="tab" data-tab="nonrutin">Iuran Tidak Rutin</button>
        </div>

        <div id="rutin" class="tab-content active">
            <select id="select-rutin" class="text-content">
                <option selected disabled>Pilih Iuran Rutin</option>
                <?php
                $rutin = mysqli_query($koneksi, "SELECT 
                                                ji.id,
                                                ji.nama_iuran AS nama_iuran,
                                                ji.nominal AS nominal,
                                                ji.periode AS periode,
                                                ji.tanggal_mulai,
                                                ji.tanggal_tenggat
                                            FROM jenis_iuran ji
                                            JOIN iuran_warga iw 
                                                ON iw.iuran_id = ji.id
                                            WHERE ji.jenis = 'rutin'
                                            AND iw.status NOT IN ('pending','lunas')
                                            AND iw.nik = '$nik'
                                            AND (
                                                    -- Tahunan: berulang setiap tahun pada bulan/tanggal yang sama
                                                    (ji.periode = 'tahunan'
                                                    AND DATE_FORMAT(CURDATE(), '%m-%d') 
                                                        BETWEEN DATE_FORMAT(ji.tanggal_mulai, '%m-%d') 
                                                            AND DATE_FORMAT(ji.tanggal_tenggat, '%m-%d'))

                                                    -- Bulanan: hanya tanggal, dengan LAST_DAY() agar tenggat otomatis menyesuaikan akhir bulan
                                                    OR (ji.periode = 'bulanan'
                                                        AND DAY(CURDATE()) BETWEEN DAY(ji.tanggal_mulai) AND DAY(LAST_DAY(CURDATE())))

                                                    -- Sekali bayar: hanya tampil jika hari ini di antara tanggal mulai dan tenggat
                                                    OR (ji.periode = 'sekali'
                                                        AND CURDATE() BETWEEN ji.tanggal_mulai AND ji.tanggal_tenggat)
                                                )
                                            ORDER BY ji.id ASC;
                ");

                        while ($row = mysqli_fetch_assoc($rutin)):
                    ?>
                    <option value="<?= $row['id']; ?>"
                            data-nama="<?= $row['nama_iuran']; ?>"
                            data-nominal="<?= $row['nominal']; ?>"
                            data-periode="<?= $row['periode']; ?>"
                            data-tanggal-mulai="<?= $row['tanggal_mulai']; ?>"
                            data-tanggal-tenggat="<?= $row['tanggal_tenggat']; ?>">
                        <?= $row['nama_iuran']; ?> (Rp<?= number_format($row['nominal']); ?>)
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div id="nonrutin" class="tab-content">
            <select id="select-nonrutin" class="text-content">
                <option selected disabled>Pilih Iuran Tidak Rutin</option>
                <?php
                $non = mysqli_query($koneksi, "SELECT 
                                                ji.id,
                                                ji.nama_iuran AS nama_iuran,
                                                ji.nominal AS nominal,
                                                ji.periode AS periode,
                                                ji.tanggal_mulai,
                                                ji.tanggal_tenggat
                                            FROM jenis_iuran ji
                                            JOIN iuran_warga iw 
                                                ON iw.iuran_id = ji.id
                                            WHERE ji.jenis = 'tidak rutin'
                                            AND iw.status NOT IN ('pending','lunas')
                                            AND iw.nik = '$nik'
                                            AND (
                                                    -- Tahunan: berulang setiap tahun pada bulan/tanggal yang sama
                                                    (ji.periode = 'tahunan'
                                                    AND DATE_FORMAT(CURDATE(), '%m-%d') 
                                                        BETWEEN DATE_FORMAT(ji.tanggal_mulai, '%m-%d') 
                                                            AND DATE_FORMAT(ji.tanggal_tenggat, '%m-%d'))

                                                    -- Bulanan: hanya tanggal, dengan LAST_DAY() agar tenggat otomatis menyesuaikan akhir bulan
                                                    OR (ji.periode = 'bulanan'
                                                        AND DAY(CURDATE()) BETWEEN DAY(ji.tanggal_mulai) AND DAY(LAST_DAY(CURDATE())))

                                                    -- Sekali bayar: hanya tampil jika hari ini di antara tanggal mulai dan tenggat
                                                    OR (ji.periode = 'sekali bayar'
                                                        AND CURDATE() BETWEEN ji.tanggal_mulai AND ji.tanggal_tenggat)
                                                )
                                            ORDER BY ji.id ASC;
                ");

                while ($row = mysqli_fetch_assoc($non)):
                ?>
                    <option value="<?= $row['id']; ?>"
                            data-nama="<?= $row['nama_iuran']; ?>"
                            data-nominal="<?= $row['nominal']; ?>"
                            data-periode="<?= $row['periode']; ?>"
                            data-tanggal-mulai="<?= $row['tanggal_mulai']; ?>"
                            data-tanggal-tenggat="<?= $row['tanggal_tenggat']; ?>">
                        <?= $row['nama_iuran']; ?> (Rp<?= number_format($row['nominal']); ?>)
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
    </section>

    <hr>

    <!-- DETAIL PEMBAYARAN -->
    <section class="details-section">
        <h2>Detail Pembayaran</h2>
        <form id="formPembayaran" method="POST" action="kirim_pembayaran.php" enctype="multipart/form-data">
        <div class="details-grid">
            <div class="left-form">
                <input type="text" id="nik"  name="nik"  value="<?= htmlspecialchars($nik, ENT_QUOTES, 'UTF-8'); ?>" readonly>
                <input type="text" id="nama" name="nama" value="<?= htmlspecialchars($nama, ENT_QUOTES, 'UTF-8'); ?>" placeholder="Nama Kepala Keluarga" readonly>


                <input type="text"   id="jenisIuran" name="jenis_iuran_display" placeholder="Jenis Iuran" readonly>
                <input type="number" id="nominal"    name="nominal" placeholder="Nominal Pembayaran" readonly>
                <input type="text"   id="periode"    name="periode" placeholder="periode iuran"readonly>
                <input type="hidden" id="iuran_id"   name="iuran_id" value="">
                <input type="hidden" id="iuran_type" name="iuran_type" value="">
                <input type="hidden" id="iuran_nama" name="iuran_nama" value="">
            </div>

            <div class="right-form">
                <h3>Pilih Metode Pembayaran</h3>
                <div class="radio-group">
                    <label><input type="radio" name="metode" value="transfer"> Transfer Bank</label>
                    <label><input type="radio" name="metode" value="qris"> QRIS / E-Wallet</label>
                </div>

                <?php
                $bank = mysqli_query($koneksi, "SELECT * FROM metode_pembayaran LIMIT 1");
                $rek = mysqli_fetch_assoc($bank);
                ?>

                <div id="bankInfo" class="info hidden">
                    <p>Silakan transfer ke nomor rekening berikut:</p>
                    <p><?= $rek['bank_nama'] ?? '—'; ?></p>
                    <p><?= $rek['bank_pemilik'] ?? '—'; ?></p>
                    <h3><?= $rek['bank_nomor'] ?? '—'; ?></h3>
                </div>

                <div class="qr-preview hidden" id="qrBox">
                    <p>Scan QR untuk melakukan pembayaran:</p>
                    <?php if (!empty($rek['qris_file'])): ?>
                        <img src="../../../uploads/<?= $rek['qris_file']; ?>" alt="QR Code" id="qrImage">
                    <?php else: ?>
                        <p style="color:red;">QRIS belum diupload.</p>
                    <?php endif; ?>
                </div>

                <div style="margin-top:12px;">
                    <label for="bukti">Upload Bukti Pembayaran</label><br>
                    <input type="file" id="bukti" name="bukti" accept="image/*,application/pdf">
                </div>

                <div style="margin-top:14px;">
                    <button type="submit" id="kirimBtn" class="btn-konfirmasi">Kirim</button>
                </div>
            </div>
        </div>
        </form>
    </section>

    <hr>

    <!-- TABEL PENDING -->
    <section class="details-section" id="pendingSection">
    <h2>Pembayaran Pending</h2>
    <div class="scroll">
        <table class="table-bayar">
            <tr>
                <thead>
                    <th>Nama</th>
                    <th>Metode</th>
                    <th>Nama Iuran</th>
                    <th>Nominal</th>
                    <th>Jenis Pembayaran</th>
                    <th>Status</th>
                    <th>Bukti</th>
                </thead>
            </tr>
            <?php
            $query_pending = "SELECT * FROM iuran_warga WHERE nik = '$nik' AND status = 'pending'";
            $pending = mysqli_query($koneksi, $query_pending);

            if (mysqli_num_rows($pending) > 0) {
                while ($p = mysqli_fetch_assoc($pending)) {
                    echo "<tr>";
                    echo "<td>".htmlspecialchars($p['nama'])."</td>";
                    echo "<td>".htmlspecialchars($p['jenis_iuran'])."</td>";
                    echo "<td>".htmlspecialchars($p['nama_iuran'])."</td>";
                    echo "<td>Rp ".number_format($p['nominal'])."</td>";
                    echo "<td>".htmlspecialchars($p['metode'] ?? '-')."</td>";
                    echo "<td style='font-weight:700; color:orange'>".htmlspecialchars($p['status'])."</td>";
                    echo "<td>";
                    if (!empty($p['bukti'])) {
                        echo "<a href='../../../uploads/".htmlspecialchars($p['bukti'])."' target='_blank' class='btn-view'>lihat</a>";
                    } else {
                        echo "-";
                    }
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='7'>Tidak ada data pending</td></tr>";
            }
            ?>
        </table>
    </div>
    </section>
    <hr>

        <!-- TABEL RIWAYAT (DITERIMA / DITOLAK) -->
        <section class="details-section" id="riwayatSection">
            <h2>Riwayat Pembayaran</h2>
                <div class="scroll">
                    <table class="table-bayar">
                        <tr>
                            <thead>
                                <th>Nama</th>
                                <th>Jenis Iuran</th>
                                <th>Nama Iuran</th>
                                <th>Nominal</th>
                                <th>Jenis Pembayaran</th>
                                <th>Status</th>
                                <th>Bukti</th>
                            </thead>
                        </tr>
                <?php
                $query_pending = "SELECT * FROM iuran_warga WHERE nik = '$nik' AND status = 'lunas'";
                $pending = mysqli_query($koneksi, $query_pending);
            
                if (mysqli_num_rows($pending) > 0) {
                    while ($p = mysqli_fetch_assoc($pending)) {
                        echo "<tr>";
                        echo "<td>".htmlspecialchars($p['nama'])."</td>";
                        echo "<td>".htmlspecialchars($p['jenis_iuran'])."</td>";
                        echo "<td>".htmlspecialchars($p['nama_iuran'])."</td>";
                        echo "<td>Rp ".number_format($p['nominal'])."</td>";
                        echo "<td>".htmlspecialchars($p['metode'] ?? '-')."</td>";
                        echo "<td style='font-weight:700; color:green'>".htmlspecialchars($p['status'])."</td>";
                        echo "<td>";
                        if (!empty($p['bukti'])) {
                            echo "<a href='../../../uploads/".htmlspecialchars($p['bukti'])."' target='_blank' class='btn-view'>lihat</a>";
                        } else {
                            echo "-";
                        }
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>Tidak ada data riwayat</td></tr>";
                }
                ?>
                </table>
            </div>
        </section>
            
        <hr>

    </div>
</div>
<script src="payment_ok.js"></script>
<script>
    (function(){
        const url = new URL(window.location.href);
        if(url.searchParams.get('success')==='1'){
            alert('Pengiriman berhasil');
            const el = document.getElementById('pendingSection');
            if(el) el.scrollIntoView({behavior:'smooth'});
        }
    })();
</script>

</body>
</html>