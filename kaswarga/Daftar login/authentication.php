<?php include ('../../Database/koneksi.php')?>

<?php
    session_start();

    $errors = array();
    $nik = "";
    $nama = "";
    $password = "";
    $passwordConfirm = "";
    $gender = "";
    $alamat = "";
    $nomor = "";
    $role = "";
    

    // REGISTER USER
    if (isset($_POST['register'])) {
        $nik             = mysqli_real_escape_string($koneksi, $_POST['nik']);
        $nama            = mysqli_real_escape_string($koneksi, $_POST['nama']);
        $gender          = mysqli_real_escape_string($koneksi, $_POST['gender']);
        $alamat          = mysqli_real_escape_string($koneksi, $_POST['alamat']);
        $nomor           = mysqli_real_escape_string($koneksi, $_POST['nomor']);
        $role            = mysqli_real_escape_string($koneksi, $_POST['role']);
        $password        = mysqli_real_escape_string($koneksi, $_POST['password']);
        $passwordConfirm = mysqli_real_escape_string($koneksi, $_POST['passwordConfirm']);

        if (empty($nik))             {array_push($errors, "NIK harus di isi!"); }
        if (empty($nama))            {array_push($errors, "Nama Lengkap harus di isi!"); }
        if (empty($gender))          {array_push($errors, "Gender harus di isi!"); }
        if (empty($alamat))          {array_push($errors, "Alamat harus di isi!"); }
        if (empty($nomor))           {array_push($errors, "Nomor Telepon harus di isi!"); }
        if (empty($password))        {array_push($errors, "Password dibutuhkan"); }
        if (empty($passwordConfirm)) {array_push($errors, "Konfirmasi password dibutuhkan"); }

        if ($password != $passwordConfirm) 
            {array_push($errors, "Kedua Password tidak sama!"); }


        $nama_check_query = "SELECT * FROM `user_role` WHERE nik='$nik' OR nama='$nama' LIMIT 1";
        $result = mysqli_query($koneksi, $nama_check_query);
        $user = mysqli_fetch_assoc($result);

            if ($user) { // jika user ada.
                if ($user['nik'] == $nik) {
                    echo "<script>
                        alert('NIK sudah digunakan!');
                        window.history.back();
                    </script>";
                }

                if ($user && $user['nama'] == $nama) {
                    array_push($errors, "email already exists");
                }
            }

            if (count($errors) == 0) {
                $password = password_hash($password, PASSWORD_BCRYPT); //enkripsi password 

                $query = "INSERT INTO `user_role` VALUES ('$nik', '$nama', '$password', '$gender', '$alamat', '$nomor', '$role')";
                mysqli_query($koneksi, $query);
                $_SESSION['nama'] = $nama;
                $_SESSION['success'] = "You are now logged in";
                header('location: ../Daftar Login/Login.php');
                exit();
            }
    }
    
    // LOGIN USER
    if (isset($_POST['login'])) {
    $nama     = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $password = mysqli_real_escape_string($koneksi, $_POST['password']);

    if (empty($nama)) {
        $errors[] = "Username is required";
    }
    if (empty($password)) {
        $errors[] = "Password is required";
    }

    if (count($errors) == 0) {
        $query = "SELECT * FROM user_role WHERE nama='$nama' LIMIT 1";
        $results = mysqli_query($koneksi, $query);

        if (mysqli_num_rows($results) > 0) {
            $row = mysqli_fetch_assoc($results);

            if (password_verify($password, $row['password'])) {
                // login sukses
                $_SESSION['nik']  = $row['nik'];   // pastikan kolom nik ada di tabel
                $_SESSION['nama'] = $row['nama'];
                $_SESSION['role'] = $row['role'];

                // redirect sesuai role
                if ($row['role'] == 'ketua') {
                    header('location: ../Ketua/dashboard/dashboard_ketua.php');
                } else if ($row['role'] == 'bendahara') {
                    header('location: ../Bendahara/dashboard/dashboard.php');
                } else if ($row['role'] == 'sekretaris') {
                    header('location: ../Sekretaris/dashboard/dashboard_sekre.php');
                } else {
                    header('location: ../Warga/dashboard/dashboard_warga.php');
                }
                exit();
            } else {
                echo "<script>
                        alert('Password salah!');
                        window.history.back();
                    </script>";
                    }
        } else {
            echo "<script>
                    alert('Username tidak ditemukan!');
                    window.history.back();
                </script>";
        }
    }
}
?>