<?php
include '../../../Database/koneksi.php';

$bulan = $_GET['bulan'];
$tahun = $_GET['tahun'];

$data = mysqli_query($koneksi, "SELECT * FROM catatan WHERE MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' ORDER BY tanggal DESC;");
?>

<!DOCTYPE html>
<html>
<head>
    <title>History Transaksi Bulan <?= ($bulan == 1 ? "Januari" : ($bulan == 2 ? "Februari" : ($bulan == 3 ? "Maret" : ($bulan == 4 ? "April" : ($bulan == 5 ? "Mei" : ($bulan == 6 ? "Juni" : ($bulan == 7 ? "Juli" : ($bulan == 8 ? "Agustus" : ($bulan == 9 ? "September" : ($bulan == 10 ? "Oktober" : ($bulan == 11 ? "November" : "Desember"))))))))))) ?> Tahun <?= $tahun ?></title>
    <link rel="stylesheet" href="history_ok.css">
    <link rel="shortcut icon" href="../../../foto/ICONB.png" type="image/x-icon">
</head>
<body>

<div class="history-container">

    <h2>History Transaksi Bulan <?= ($bulan == 1 ? "Januari" : ($bulan == 2 ? "Februari" : ($bulan == 3 ? "Maret" : ($bulan == 4 ? "April" : ($bulan == 5 ? "Mei" : ($bulan == 6 ? "Juni" : ($bulan == 7 ? "Juli" : ($bulan == 8 ? "Agustus" : ($bulan == 9 ? "September" : ($bulan == 10 ? "Oktober" : ($bulan == 11 ? "November" : "Desember"))))))))))) ?> Tahun <?= $tahun ?></h2>

    <a href="laporan bulanan.php?tahun=<?= $tahun ?>" class="btn-back">Kembali</a>

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
                <tr><td colspan="7" class="empty">Tidak ada transaksi di bulan ini</td></tr>
            <?php endif; ?>

            <?php while($row = mysqli_fetch_assoc($data)): ?>
            <tr>
                <td><?= $row['tanggal'] ?></td>
                <td><?= ucfirst($row['kategori']) ?></td>
                <td><?= ucfirst($row['nama_iuran']) ?></td>
                <td>Rp <?= number_format($row['jumlah'], 0, ',', '.') ?></td>
                <td><?= $row['nama'] ?></td>
                <td><?= $row['alamat'] ?></td>
                <td>
                    <?php if (!empty($row['bukti'])) {
                        echo "<a href='../../../uploads/".htmlspecialchars($row['bukti'])."' target='_blank' class='btn-view'>lihat</a>";
                    } else {
                        echo "-";
                    }
                    ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

</div>


</body>
</html>
