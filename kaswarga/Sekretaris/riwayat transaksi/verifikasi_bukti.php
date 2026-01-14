<?php 
include '../../../Database/koneksi.php';
session_start();

if (!isset($_SESSION['nama'])) {
    // User belum login
    header("Location: ../../Daftar login/Login.php");
    exit;
}   
    $nama = $_SESSION['nama'] ?? '';

if (!isset($_GET['id']) || !isset($_GET['aksi'])) {
    header("Location: kelola_riwayat.php?error=invalid");
    exit;
}

$id   = $_GET['id'];
$aksi = $_GET['aksi'];

// ambil data bukti (untuk hapus file kalau diperlukan)
$q = mysqli_query($koneksi, "SELECT bukti FROM iuran_warga WHERE id='$id'");
$data = mysqli_fetch_assoc($q);
$fileBukti = $data['bukti'] ?? null;

// aksi verifikasi
if ($aksi == "terima") {
    mysqli_query($koneksi, "UPDATE iuran_warga SET `status`='lunas' WHERE id='$id'");
} elseif ($aksi == "tolak") {
    mysqli_query($koneksi, "UPDATE iuran_warga SET `status`='belum lunas' WHERE id='$id'");
} 

// kembali ke kelola riwayat
header("Location: kelola_riwayat.php?success=1");
exit;
