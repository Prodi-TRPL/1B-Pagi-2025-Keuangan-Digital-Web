<?php
  include 'sunting daftar warga.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pendaftaran Akun Warga</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
  <link rel="shortcut icon" href="../../../foto/ICONB.png" type="image/x-icon">
  <link rel="stylesheet" href="daftar_warga_oke.css">
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
    <h2><i class="fas fa-users me-3"></i> Pendaftaran Akun Warga</h2>

    <div class="message" id="message"></div>

    <div class="transaction-box">
      <div class="header-controls">
        <h4><i class="fas fa-list me-2"></i> Data Warga Terdaftar</h4>
        <button type="button" class="btn btn-primary" id="tambahDataBtn">
          <i class="fas fa-plus-circle me-2"></i> Tambah Data Warga
        </button>
      </div>

      <div class="table-container">
        <table id="dataTable">
          <thead>
            <tr>
              <th>No</th>
              <th>NIK</th>
              <th>Nama</th>
              <th>Kelamin</th>
              <th>Telepon</th>
              <th>Alamat</th>
              <th>Jabatan</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php
              $query = "SELECT * FROM user_role
                        ORDER BY 
                            CASE 
                                WHEN role = 'ketua' THEN 1
                                WHEN role = 'sekretaris' THEN 2
                                WHEN role = 'bendahara' THEN 3
                                ELSE 4 END,
                        nik ASC";
              
              $result = mysqli_query($koneksi, $query);
              $no = 1;

              if (mysqli_num_rows($result) > 0) {
                  while ($row = mysqli_fetch_assoc($result)) {
                      echo "<tr>";
                        echo "<td>".$no++."</td>";
                        echo "<td>".$row['nik']."</td>";
                        echo "<td>".$row['nama']."</td>";
                        echo "<td>".$row['gender']."</td>";
                        echo "<td>".$row['nomor']."</td>";
                        echo "<td>".$row['alamat']."</td>";
                        echo "<td>".$row['role']."</td>";
                        echo "<td>
                                <button type='button' class='edit-btn'
                                        data-nik='{$row['nik']}'
                                        data-nama='{$row['nama']}'
                                        data-gender='{$row['gender']}'
                                        data-nomor='{$row['nomor']}'
                                        data-alamat='{$row['alamat']}'
                                        data-role='{$row['role']}'>
                                  Edit
                                </button>

                                <form method='post' style='display:inline-block;' onsubmit=\"return confirm('Yakin mau hapus data ini?');\">
                                  <input type='hidden' name='delete_nik' value='{$row['nik']}'>
                                  <button type='submit' name='delete' class='delete-btn'>Delete</button>
                                </form>
                              </td>";
                      echo "</tr>";
                  }
              } else {
                  echo "<tr><td colspan='3'>Belum ada data</td></tr>";
              }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Modal Edit Data -->
  <div class="modal" id="editDataModal" style="display: <?php echo $editing ? 'flex' : 'none'; ?>;">
      <div class="modal-content">
          <div class="modal-header">
              <h5 class="modal-title">
                  <i class="fas fa-edit me-2"></i> Edit Data Warga
              </h5>
              <button type="button" class="btn-cancel close-btn" id="closeEditModalBtn">&times;</button>
          </div>
          <div class="modal-body">
              <form method="POST" action="">

                  <input type="hidden" name="nik_lama" id="nik_lama" value="<?php echo isset($editData['nik']) ? $editData['nik'] : ''; ?>">
                  
                  <div class="mb-4">
                      <label for="edit_nik" class="form-label">NIK:</label>
                      <input type="text" class="form-control" id="edit_nik" name="edit_nik" 
                            value="<?php echo isset($editData['nik']) ? $editData['nik'] : ''; ?>" required>
                  </div>

                  <div class="mb-4">
                      <label for="edit_nama" class="form-label">Nama Lengkap:</label>
                      <input type="text" class="form-control" id="edit_nama" name="edit_nama" 
                            value="<?php echo isset($editData['nama']) ? $editData['nama'] : ''; ?>" required>
                  </div>

                  <div class="mb-4">
                      <label for="edit_gender" class="form-label">Kelamin:</label>
                      <select class="form-control" id="edit_gender" name="edit_gender" required>
                          <option value="Laki-laki" <?php echo (isset($editData['gender']) && $editData['gender'] == 'Laki-laki') ? 'selected' : ''; ?>>Laki-laki</option>
                          <option value="Perempuan" <?php echo (isset($editData['gender']) && $editData['gender'] == 'Perempuan') ? 'selected' : ''; ?>>Perempuan</option>
                      </select>
                  </div>
                  
                  <div class="mb-4">
                      <label for="edit_nomor" class="form-label">Nomor Telepon:</label>
                      <input type="text" class="form-control" id="edit_nomor" name="edit_nomor" 
                            value="<?php echo isset($editData['nomor']) ? $editData['nomor'] : ''; ?>" required> 
                  </div>

                  <div class="mb-4">
                      <label for="edit_alamat" class="form-label">Alamat:</label>
                      <input type="text" class="form-control" id="edit_alamat" name="edit_alamat" 
                            value="<?php echo isset($editData['alamat']) ? $editData['alamat'] : ''; ?>" required> 
                  </div>
                  
                  <div class="mb-4" id="roleContainer">
                    <label for="edit_role" class="form-label">Jabatan:</label>
                    <select class="form-control" id="edit_role" name="edit_role" required>
                        <option value="warga">Warga</option>
                        <option value="sekretaris">Sekretaris</option>
                        <option value="bendahara">Bendahara</option>
                        <option value="ketua">Ketua</option>
                    </select>
                  </div>

                  <input type="hidden" name="edit" value="1">
                  <button type="submit" class="btn btn-primary w-50 me-2">
                      <i class="fas fa-save me-2"></i> Simpan Perubahan
                  </button>
                  </div>
              </form>
          </div>
      </div>
  </div>


  <!-- MODAL TAMBAH DATA -->
  <div class="modal" id="tambahDataModal">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">
          <i class="fas fa-user-plus me-2"></i> Tambah Data Warga
        </h5>
        <button type="button" class="close-btn" id="closeModalBtn">&times;</button>
      </div>
      <div class="modal-body">

        <form method="POST" action="" id="registrationForm">
          <div class="mb-4">
              <label for="nik" class="form-label">NIK:</label>
              <input type="text" class="form-control" id="nik" name="nik"
                    placeholder="Masukkan 16 digit NIK" required maxlength="16">
              <div class="form-text">NIK harus terdiri dari 16 digit angka</div>
          </div>

          <div class="mb-4">
              <label for="nama" class="form-label">Nama Lengkap:</label>
              <input type="text" class="form-control" id="nama" name="nama"
                    placeholder="Masukkan nama lengkap warga" required>
          </div>

          <div class="mb-4">
                <label class="form-label">Kelamin</label>

                <div class="gender-group">
                    <label class="gender-item">
                        <input type="radio" name="gender" value="Laki-laki" required>
                        <span class="custom-radio"></span>
                        <span>Laki-laki</span>
                    </label>

                    <label class="gender-item">
                        <input type="radio" name="gender" value="Perempuan" required>
                        <span class="custom-radio"></span>
                        <span>Perempuan</span>
                    </label>
                </div>

                <div class="form-text">Pilih jenis kelamin</div>
            </div>

          <div class="mb-4">
              <label for="telepon" class="form-label">Nomor Telepon:</label>
              <input type="text" class="form-control" id="telepon" name="nomor"
                    placeholder="Masukkan nomor telepon aktif" required>
              <div class="form-text">Contoh: 081234567890</div>
          </div>

          <div class="mb-4">
              <label for="alamat" class="form-label">Alamat</label>
              <input type="text" class="form-control" id="alamat" name="alamat"
                    placeholder="Masukkan alamat rumah di perumahan" required>
          </div>

          <div class="mb-4">
    <label for="role" class="form-label">Jabatan:</label>
    <select class="form-control" id="role" name="role" required>
        <option value="ketua" <?php echo (isset($editData['role']) && $editData['role'] == 'ketua') ? 'selected' : ''; ?>>Ketua</option>
        <option value="warga" <?php echo (isset($editData['role']) && $editData['role'] == 'warga') ? 'selected' : ''; ?>>Warga</option>
        <option value="sekretaris" <?php echo (isset($editData['role']) && $editData['role'] == 'sekretaris') ? 'selected' : ''; ?>>Sekretaris</option>
        <option value="bendahara" <?php echo (isset($editData['role']) && $editData['role'] == 'bendahara') ? 'selected' : ''; ?>>Bendahara</option>
    </select>
</div>

          <div class="mb-4">
              <label for="password" class="form-label">Password:</label>
              <input type="password" class="form-control" id="password" name="password"
                    placeholder="Masukkan password untuk akun" required>
              <div class="form-text">Password minimal 6 karakter</div>
          </div>

          <div class="mb-4">
              <label for="passwordConfrim" class="form-label">Konfirmasi Password:</label>
              <input type="password" class="form-control" id="passwordConfirm" name="passwordConfirm"
                    placeholder="Ulangi password untuk konfirmasi" required>
              <div class="form-text">Kedua password harus sama</div>
          </div>

          <input type="hidden" name="register" value="1">

          <button type="submit" id="registerBtn" name="register"
                  class="btn btn-primary">
                  <i class="fas fa-save me-2"></i> Simpan Data
          </button>
        </form>
      </div>
    </div>
  </div>

  <script src="daftar_warga.js"></script>
</body>
</html>