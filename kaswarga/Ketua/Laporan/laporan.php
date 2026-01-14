<?php
require_once '../../../Database/koneksi.php';
require_once __DIR__ . '../../../../dompdf/autoload.inc.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$mode  = $_GET['mode'] ?? 'bulanan';  // bulanan | tahunan
$tahun = $_GET['tahun'] ?? date("Y");
$bulan = $_GET['bulan'] ?? null;

$html = "";

// --- KOP SURAT ---
$html .= "
<div style='text-align:center;'>
    <img src=\"http://localhost/kaswarga/foto/ICONB.png\" width=\"80\" height=\"80\" style=\"float:left; margin-right:10px;\">
    <h2 style='margin:0;'>KAS WARGA RT 05 RW 03</h2>
    <p style='margin:0;'>Jl. Melati No. 123, Batam Kota</p>
    <p style='margin:0;'>Telp: 0812-3456-7890 | Email: kaswarga@example.com</p>
</div>
<hr style='border:1px solid #000; margin:10px 0;'>
";

$bulan_nama = [
    1=>"Januari",2=>"Februari",3=>"Maret",4=>"April",5=>"Mei",6=>"Juni",
    7=>"Juli",8=>"Agustus",9=>"September",10=>"Oktober",11=>"November",12=>"Desember"
];

// ======================================================
// 1) LAPORAN BULANAN PER BULAN (detail Pemasukan & Pengeluaran)
// ======================================================

if ($mode === 'bulanan' && $bulan) {

    // Ringkasan bulan terpilih
    $total_pemasukan = mysqli_fetch_assoc(mysqli_query($koneksi,
        "SELECT COALESCE(SUM(jumlah),0) AS total FROM catatan 
         WHERE kategori='Pemasukan' AND YEAR(tanggal)='$tahun' AND MONTH(tanggal)='$bulan'"
    ))['total'] ?? 0;

    $total_pengeluaran = mysqli_fetch_assoc(mysqli_query($koneksi,
        "SELECT COALESCE(SUM(jumlah),0) AS total FROM catatan 
         WHERE kategori='Pengeluaran' AND YEAR(tanggal)='$tahun' AND MONTH(tanggal)='$bulan'"
    ))['total'] ?? 0;

    $saldo_akhir = ($total_pemasukan ?: 0) - ($total_pengeluaran ?: 0);

    // Judul laporan
    $html .= "<h2 style='text-align:center;'>Laporan Keuangan Bulan " . strtoupper($bulan_nama[(int)$bulan]) . " $tahun</h2>";

    // Ringkasan tabel
    $html .= "<table border='1' cellspacing='0' cellpadding='5' width='100%'>
                <tr><th>Total Pemasukan</th><th>Total Pengeluaran</th><th>Saldo Akhir</th></tr>
                <tr>
                    <td>Rp " . number_format($total_pemasukan,0,',','.') . "</td>
                    <td>Rp " . number_format($total_pengeluaran,0,',','.') . "</td>
                    <td>Rp " . number_format($saldo_akhir,0,',','.') . "</td>
                </tr>
              </table><br>";

    // Query detail (ambil uraian, bukan kategori/nama/alamat)
    $pemasukan = mysqli_query($koneksi,
        "SELECT tanggal, jumlah, uraian
         FROM catatan 
         WHERE kategori='Pemasukan' AND YEAR(tanggal)='$tahun' AND MONTH(tanggal)='$bulan'
         ORDER BY tanggal ASC"
    );

    $pengeluaran = mysqli_query($koneksi,
        "SELECT tanggal, jumlah, uraian
         FROM catatan 
         WHERE kategori='Pengeluaran' AND YEAR(tanggal)='$tahun' AND MONTH(tanggal)='$bulan'
         ORDER BY tanggal ASC"
    );

    // ============================
    // Tabel detail PEMASUKAN
    // ============================
    $html .= "<h3>PEMASUKAN</h3>
              <table border='1' cellspacing='0' cellpadding='5' width='100%'>
              <tr><th>Tanggal</th><th>Uraian</th><th>Nominal (Rp)</th></tr>";

    if ($pemasukan && mysqli_num_rows($pemasukan) > 0) {
        while ($row = mysqli_fetch_assoc($pemasukan)) {
            $html .= "<tr>
                        <td style='white-space:nowrap;'>" . date("d/m/Y H:i:s", strtotime($row['tanggal'])) . "</td>
                        <td>" . htmlspecialchars($row['uraian'] ?? '-') . "</td>
                        <td>Rp " . number_format((float)$row['jumlah'],0,',','.') . "</td>
                      </tr>";
        }
    } else {
        $html .= "<tr><td colspan='3' style='text-align:center;'>Tidak ada data pemasukan</td></tr>";
    }

    $html .= "</table>";

    // ============================
    // Tabel detail PENGELUARAN
    // ============================
    $html .= "<h3>PENGELUARAN</h3>
              <table border='1' cellspacing='0' cellpadding='5' width='100%'>
              <tr><th>Tanggal</th><th>Uraian</th><th>Nominal (Rp)</th></tr>";

    if ($pengeluaran && mysqli_num_rows($pengeluaran) > 0) {
        while ($row = mysqli_fetch_assoc($pengeluaran)) {
            $html .= "<tr>
                        <td style='white-space:nowrap;'>" . date("d/m/Y H:i:s", strtotime($row['tanggal'])) . "</td>
                        <td>" . htmlspecialchars($row['uraian'] ?? '-') . "</td>
                        <td>Rp " . number_format((float)$row['jumlah'],0,',','.') . "</td>
                      </tr>";
        }
    } else {
        $html .= "<tr><td colspan='3' style='text-align:center;'>Tidak ada data pengeluaran</td></tr>";
    }

    $html .= "</table>";
}


// ======================================================
// 2) LAPORAN BULANAN REKAP SETAHUN (ringkasan 12 bulan dalam tahun tertentu)
// ======================================================
elseif ($mode === 'bulanan' && !$bulan) {

    $rekap = mysqli_query($koneksi,
        "SELECT MONTH(tanggal) AS bulan,
                SUM(CASE WHEN kategori='Pemasukan' THEN jumlah ELSE 0 END) AS pemasukan,
                SUM(CASE WHEN kategori='Pengeluaran' THEN jumlah ELSE 0 END) AS pengeluaran
         FROM catatan
         WHERE YEAR(tanggal) = '$tahun'
         GROUP BY MONTH(tanggal)
         ORDER BY MONTH(tanggal)"
    );

    $total_pemasukan_tahun = mysqli_fetch_assoc(mysqli_query($koneksi,
        "SELECT COALESCE(SUM(jumlah),0) AS total FROM catatan WHERE kategori='Pemasukan' AND YEAR(tanggal)='$tahun'"
    ))['total'] ?? 0;

    $total_pengeluaran_tahun = mysqli_fetch_assoc(mysqli_query($koneksi,
        "SELECT COALESCE(SUM(jumlah),0) AS total FROM catatan WHERE kategori='Pengeluaran' AND YEAR(tanggal)='$tahun'"
    ))['total'] ?? 0;

    $html .= "<h2 style='text-align:center;'>Rekap Bulanan Tahun $tahun</h2>";
    $html .= "<table border='1' cellspacing='0' cellpadding='5' width='100%'>
                <tr><th>Total Pemasukan Tahun</th><th>Total Pengeluaran Tahun</th><th>Saldo Akhir Tahun</th></tr>
                <tr>
                    <td>Rp ".number_format($total_pemasukan_tahun,0,',','.')."</td>
                    <td>Rp ".number_format($total_pengeluaran_tahun,0,',','.')."</td>
                    <td>Rp ".number_format($total_pemasukan_tahun - $total_pengeluaran_tahun,0,',','.')."</td>
                </tr>
              </table><br>";

    $html .= "<h3>Rekap per Bulan</h3>
              <table border='1' cellspacing='0' cellpadding='5' width='100%'>
                <tr><th>Bulan</th><th>Pemasukan</th><th>Pengeluaran</th><th>Saldo</th></tr>";

    if ($rekap && mysqli_num_rows($rekap) > 0) {
        while ($row = mysqli_fetch_assoc($rekap)) {
            $pemasukan = (float)($row['pemasukan'] ?? 0);
            $pengeluaran = (float)($row['pengeluaran'] ?? 0);
            $saldo = $pemasukan - $pengeluaran;

            $html .= "<tr>
                        <td>".$bulan_nama[(int)$row['bulan']]."</td>
                        <td>Rp ".number_format($pemasukan,0,',','.')."</td>
                        <td>Rp ".number_format($pengeluaran,0,',','.')."</td>
                        <td>Rp ".number_format($saldo,0,',','.')."</td>
                      </tr>";
        }
    } else {
        $html .= "<tr><td colspan='4' style='text-align:center;'>Tidak ada data pada tahun ini</td></tr>";
    }
    $html .= "</table>";
}

// ======================================================
// 3) LAPORAN TAHUNAN (rekap lintas tahun)
// ======================================================
elseif ($mode === 'tahunan') {

    $qRekap = mysqli_query($koneksi,
        "SELECT YEAR(tanggal) AS tahun,
                SUM(CASE WHEN kategori='Pemasukan' THEN jumlah ELSE 0 END) AS pemasukan,
                SUM(CASE WHEN kategori='Pengeluaran' THEN jumlah ELSE 0 END) AS pengeluaran
         FROM catatan
         GROUP BY YEAR(tanggal)
         ORDER BY YEAR(tanggal)"
    );

    $total_semua_pemasukan = mysqli_fetch_assoc(
        mysqli_query($koneksi, "SELECT COALESCE(SUM(jumlah),0) AS total FROM catatan WHERE kategori='Pemasukan'")
    )['total'] ?? 0;

    $total_semua_pengeluaran = mysqli_fetch_assoc(
        mysqli_query($koneksi, "SELECT COALESCE(SUM(jumlah),0) AS total FROM catatan WHERE kategori='Pengeluaran'")
    )['total'] ?? 0;

    $html .= "<h2 style='text-align:center;'>Rekap Tahunan</h2>";
    $html .= "<table border='1' cellspacing='0' cellpadding='5' width='100%'>
                <tr><th>Total Pemasukan</th><th>Total Pengeluaran</th><th>Saldo Akhir</th></tr>
                <tr>
                    <td>Rp ".number_format($total_semua_pemasukan,0,',','.')."</td>
                    <td>Rp ".number_format($total_semua_pengeluaran,0,',','.')."</td>
                    <td>Rp ".number_format($total_semua_pemasukan - $total_semua_pengeluaran,0,',','.')."</td>
                </tr>
              </table><br>";

    $html .= "<h3>Rekap per Tahun</h3>
              <table border='1' cellspacing='0' cellpadding='5' width='100%'>
                <tr><th>Tahun</th><th>Pemasukan</th><th>Pengeluaran</th><th>Saldo Akhir Tahun</th></tr>";

    if ($qRekap && mysqli_num_rows($qRekap) > 0) {
        while ($row = mysqli_fetch_assoc($qRekap)) {
            $pemasukan_tahun = (float)($row['pemasukan'] ?? 0);
            $pengeluaran_tahun = (float)($row['pengeluaran'] ?? 0);
            $saldo_tahun_ini = $pemasukan_tahun - $pengeluaran_tahun;

            $html .= "<tr>
                        <td>".htmlspecialchars($row['tahun'])."</td>
                        <td>Rp ".number_format($pemasukan_tahun,0,',','.')."</td>
                        <td>Rp ".number_format($pengeluaran_tahun,0,',','.')."</td>
                        <td>Rp ".number_format($saldo_tahun_ini,0,',','.')."</td>
                      </tr>";
        }
    } else {
        $html .= "<tr><td colspan='4' style='text-align:center;'>Tidak ada data</td></tr>";
    }
    $html .= "</table>";
}

// ======================================================
// 4) LAPORAN BULANAN PER TAHUN (dipanggil dari tabel tahunan)
// ======================================================
elseif ($mode === 'bulanan_per_tahun' && $tahun) {

    $rekap = mysqli_query($koneksi,
        "SELECT MONTH(tanggal) AS bulan,
                SUM(CASE WHEN kategori='Pemasukan' THEN jumlah ELSE 0 END) AS pemasukan,
                SUM(CASE WHEN kategori='Pengeluaran' THEN jumlah ELSE 0 END) AS pengeluaran
         FROM catatan
         WHERE YEAR(tanggal) = '$tahun'
         GROUP BY MONTH(tanggal)
         ORDER BY MONTH(tanggal)"
    );

    $total_pemasukan_tahun = mysqli_fetch_assoc(mysqli_query($koneksi,
        "SELECT COALESCE(SUM(jumlah),0) AS total 
         FROM catatan 
         WHERE kategori='Pemasukan' AND YEAR(tanggal)='$tahun'"
    ))['total'] ?? 0;

    $total_pengeluaran_tahun = mysqli_fetch_assoc(mysqli_query($koneksi,
        "SELECT COALESCE(SUM(jumlah),0) AS total 
         FROM catatan 
         WHERE kategori='Pengeluaran' AND YEAR(tanggal)='$tahun'"
    ))['total'] ?? 0;

    $html .= "<h2 style='text-align:center;'>Rekap Bulanan Tahun $tahun</h2>";
    $html .= "<table border='1' cellspacing='0' cellpadding='5' width='100%'>
                <tr><th>Total Pemasukan Tahun</th><th>Total Pengeluaran Tahun</th><th>Saldo Akhir Tahun</th></tr>
                <tr>
                    <td>Rp ".number_format($total_pemasukan_tahun,0,',','.')."</td>
                    <td>Rp ".number_format($total_pengeluaran_tahun,0,',','.')."</td>
                    <td>Rp ".number_format($total_pemasukan_tahun - $total_pengeluaran_tahun,0,',','.')."</td>
                </tr>
              </table><br>";

    $html .= "<h3>Rekap per Bulan</h3>
              <table border='1' cellspacing='0' cellpadding='5' width='100%'>
                <tr><th>Bulan</th><th>Pemasukan</th><th>Pengeluaran</th><th>Saldo</th></tr>";

    if ($rekap && mysqli_num_rows($rekap) > 0) {
        while ($row = mysqli_fetch_assoc($rekap)) {
            $pemasukan = (float)($row['pemasukan'] ?? 0);
            $pengeluaran = (float)($row['pengeluaran'] ?? 0);
            $saldo = $pemasukan - $pengeluaran;

            $html .= "<tr>
                        <td>".$bulan_nama[(int)$row['bulan']]."</td>
                        <td>Rp ".number_format($pemasukan,0,',','.')."</td>
                        <td>Rp ".number_format($pengeluaran,0,',','.')."</td>
                        <td>Rp ".number_format($saldo,0,',','.')."</td>
                      </tr>";
        }
    } else {
        $html .= "<tr><td colspan='4' style='text-align:center;'>Tidak ada data pada tahun ini</td></tr>";
    }
    $html .= "</table>";
}

// --- Render PDF ---
$options = new Options();
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("laporan_$mode.pdf", ["Attachment" => false]);