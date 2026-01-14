<?php include ('authentication.php')?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun</title>
    <link rel="stylesheet" href="register_ok.css">
    <link rel="shortcut icon" href="ICONB.png" type="image/x-icon">
</head>
<body>
    <main class="register-page">
        <section class="register-card">
            <h1>Daftar Akun Perumahan</h1>
            <form method="POST" action="" id="registerForm" class="register-form">
                <input type="text"     id="fullName"        name="nik"             placeholder="Masukkan NIK" required>
                <input type="text"     id="regEmail"        name="nama"            placeholder="Masukkan Nama Lengkap (Ketua RT)" required>
                <input type="password" id="password"        name="password"        placeholder="Masukkan Password" required>
                <input type="password" id="passwordConfirm" name="passwordConfirm" placeholder="Konfirmasi Password" required>
                <input type="text"     id="address"         name="alamat"          placeholder="Masukkan Alamat Perumahan" required>
                <input type="tel"      id="phone"           name="nomor"           placeholder="Nomor Handphone/WhatsApp 08xxxxxxxxxx" required>
                <input type="hidden"   id="role"            name="role"            value="ketua">

                <div class="gender-group">
                    <label style="font-weight: 600;">Jenis Kelamin:</label>

                    <label class="gender-option">
                        <input type="radio" name="gender" value="Laki-laki" required>
                        <div class="custom-radio"></div>
                        Laki-laki
                    </label>

                    <label class="gender-option">
                        <input type="radio" name="gender" value="Perempuan" required>
                        <div class="custom-radio"></div>
                        Perempuan
                    </label>

                    <label class="consent">
                        <input type="checkbox" id="consentCheckbox" required>
                        <span class="custom-checkbox" aria-hidden="true"></span>
                        saya setuju untuk menerima pesan yang berisikan informasi
                    </label>
                </div>
                

                <input  type="hidden" name="register" value="1">
                <button type="submit" id="registerBtn" name="register">Daftar</button>

                <input  type="hidden">
                <button type="button" id="backBtn" onclick="window.location.href='../Daftar login/Login.php'">Kembali</button>
            </form>
        </section>
    </main>
    
    <script src="register.js"></script>
</body>
</html>
