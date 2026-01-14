<?php
    include '../../../Database/koneksi.php';

    $editData = [
    'nik' => '',
    'nama' => '',
    'gender' => '',
    'nomor' => '',
    'alamat' => '',
    'role' => ''
    ];

    $errors = [];

    if (isset($_POST['register'])) {

    $nik      = mysqli_real_escape_string($koneksi, $_POST['nik']);
    $nama     = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $gender   = mysqli_real_escape_string($koneksi, $_POST['gender']);
    $alamat   = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $nomor    = mysqli_real_escape_string($koneksi, $_POST['nomor']);
    $role     = mysqli_real_escape_string($koneksi, $_POST['role']);
    $password = $_POST['password'];
    $confirm  = $_POST['passwordConfirm'];

    // CEK AKUN SUDAH ADA
    $cek = mysqli_query($koneksi, "SELECT nik FROM user_role WHERE nik='$nik'");
    if (mysqli_num_rows($cek) > 0) {
        echo "<script>
            alert('Akun sudah terdaftar');
            window.history.back();
        </script>";
        exit();
    }

    // VALIDASI PASSWORD
    if ($password !== $confirm) {
        echo "<script>
            alert('Password tidak sama');
            window.history.back();
        </script>";
        exit();
    }

    $password = password_hash($password, PASSWORD_BCRYPT);

    $query = "INSERT INTO user_role 
        (nik, nama, password, gender, alamat, nomor, role)
        VALUES ('$nik', '$nama', '$password', '$gender', '$alamat', '$nomor', '$role')";

    if (mysqli_query($koneksi, $query)) {
        echo "<script>
            alert('Akun berhasil ditambahkan');
            window.location='daftar warga.php';
        </script>";
    } else {
        echo "<script>
            alert('Terjadi kesalahan sistem');
        </script>";
    }
}


    // ==== EDIT DATA ====
    if (isset($_POST['edit']) && isset($_POST['edit_nik'])) {
        $niklama = mysqli_real_escape_string($koneksi, $_POST['nik_lama']);
        $nikbaru     = mysqli_real_escape_string($koneksi, $_POST['edit_nik']);
        $nama    = mysqli_real_escape_string($koneksi, $_POST['edit_nama']);
        $gender  = mysqli_real_escape_string($koneksi, $_POST['edit_gender']);
        $nomor   = mysqli_real_escape_string($koneksi, $_POST['edit_nomor']);
        $alamat  = mysqli_real_escape_string($koneksi, $_POST['edit_alamat']);
        $role    = mysqli_real_escape_string($koneksi, $_POST['edit_role']);

        $query = "UPDATE user_role 
                SET nik='$nikbaru', nama='$nama', gender='$gender', nomor='$nomor', alamat='$alamat', role='$role'
                WHERE nik='$niklama'";

        if (mysqli_query($koneksi, $query)) {
            // sukses update
            echo "<script>
                    alert('Data berhasil diperbarui');
                    window.location.href='daftar warga.php';
                </script>";
        } else {
            echo "Error: " . mysqli_error($koneksi);
        }
    }

    // ==== HAPUS DATA ====
    if (isset($_POST['delete'])) {
        $nik_delete = $_POST['delete_nik'];
        $query = "DELETE FROM user_role WHERE nik='$nik_delete'";
        if (mysqli_query($koneksi, $query)) {
            $_SESSION['message'] = "Data warga berhasil dihapus.";
        } else {
            $_SESSION['message'] = "Gagal menghapus data: " . mysqli_error($koneksi);
        }
        header("Location: daftar warga.php");
        exit();
    }
?>