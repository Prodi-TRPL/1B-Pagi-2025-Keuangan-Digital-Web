<?php
include '../../../Database/koneksi.php';

$tahun = $_GET['tahun'] ?? date("Y");

$data = mysqli_query($koneksi, "SELECT * FROM catatan WHERE YEAR(tanggal) = '$tahun' ORDER BY tanggal DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>History Tahun <?= $tahun ?></title>
    <link rel="stylesheet" href="history_ok.css">
    <link rel="shortcut icon" href="../../../foto/ICONB.png" type="image/x-icon">
</head>
<body>
<div class="history-container">
    <h2>History Transaksi Tahun <?= $tahun ?></h2>
    <a href="laporan tahunan.php" class="btn-back">Kembali</a>

    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Kategori</th>
                <th>Jenis</th>
                <th>Nominal</th>
                <th>Nama</th>
                <th>Alamat</th>
                <th>Bukti</th>
            </tr>
        </thead>
        <tbody>
            <?php if (mysqli_num_rows($data) == 0): ?>
                <tr><td colspan="7" class="empty">Tidak ada transaksi tahun ini</td></tr>
            <?php endif; ?>
            <?php while($row = mysqli_fetch_assoc($data)): ?>
                <tr>
                    <td><?= date("d/m/Y H:i", strtotime($row['tanggal'])) ?></td>
                    <td><?= ucfirst($row['kategori']) ?></td>
                    <td><?= ucfirst($row['nama_iuran']) ?></td>
                    <td>Rp <?= number_format($row['jumlah'],0,',','.') ?></td>
                    <td><?= $row['nama'] ?></td>
                    <td><?= $row['alamat'] ?></td>
                    <td><?= $row['bukti'] ? "<a href='../../../uploads/".htmlspecialchars($row['bukti'])."' target='_blank'>lihat</a>" : "-" ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>