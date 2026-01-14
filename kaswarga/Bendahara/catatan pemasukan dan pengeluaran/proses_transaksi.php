<?php
include '../../../Database/koneksi.php';

if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

date_default_timezone_set("Asia/Jakarta");
$tanggal = date("Y-m-d H:i:s");
$kategori       = $_POST['kategori'];
$jumlah         = $_POST['jumlah'];
$uraian         = $_POST['uraian'];
$nama_transaksi = $_POST['nama_transaksi'];
$nama_terkait   = $_POST['nama_terkait'];
$metode         = $_POST['metode'];
$alamat         = $_POST['alamat'];
$status         = "lunas";
$periode        = "sekali bayar";

$bukti_nama = "";

// VALIDASI BUKTI PEMBAYARAN WAJIB
if (!isset($_FILES['bukti']) || $_FILES['bukti']['error'] != 0) {
    echo "Silakan unggah bukti pembayaran!";
    exit();
}

if (isset($_FILES['bukti']) && $_FILES['bukti']['error'] === 0) {
    $folder = "../../../uploads/";

    if (!is_dir($folder)) {
        mkdir($folder, 0777, true);
    }

    $bukti_nama = time() . "_" . basename($_FILES["bukti"]["name"]);
    $target_file = $folder . $bukti_nama;

    move_uploaded_file($_FILES["bukti"]["tmp_name"], $target_file);
}

$query = "INSERT INTO catatan (nama, alamat, metode, periode, jumlah, `status`, tanggal, bukti, nama_iuran, kategori,  uraian)
          VALUES ('$nama_terkait', '$alamat', '$metode', '$periode', '$jumlah', '$status', '$tanggal', '$bukti_nama', '$nama_transaksi', '$kategori', '$uraian')";

if (mysqli_query($koneksi, $query)) {
    echo "sukses";
} else {
    echo "error: " . mysqli_error($koneksi);
}

mysqli_close($koneksi);
?>
