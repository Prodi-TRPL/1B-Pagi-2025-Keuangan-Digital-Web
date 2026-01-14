<?php
include "../../../Database/koneksi.php";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Pengaturan Iuran</title>
    <link rel="stylesheet" href="modal_pembayaran.css" />
    <script defer src="modal_pembayaran_ok.js"></script>
</head>

<body>
    <!-- ========================= MODAL BESAR (HALAMAN UTAMA) ========================= -->
    <div class="modal-main">
        <div class="modal-box">
            <h2>Pengaturan Iuran 🔧</h2>

            <div class="iuran-container">
                <!-- Iuran Rutin -->
                <div class="iuran-box">
                    <h3>Iuran Rutin</h3>
                    <table class="table-iuran" id="table-rutin">
                        <?php
                        $rutin = mysqli_query($koneksi, "SELECT * FROM jenis_iuran WHERE jenis = 'rutin'");
                        while ($r = mysqli_fetch_assoc($rutin)):
                        ?>
                        <tr>
                            <td><?= $r['nama_iuran'] ?></td>
                            <td style="text-align:right;">
                                <div class="action-btns">
                                    <button class="btn btn-edit edit-btn"
                                        data-id="<?= $r['id'] ?>"
                                        data-nama="<?= $r['nama_iuran'] ?>"
                                        data-nominal="<?= $r['nominal'] ?>"
                                        data-periode="<?= $r['periode'] ?>"
                                        data-tanggal-mulai="<?= $r['tanggal_mulai'] ?>"
                                        data-tanggal-tenggat="<?= $r['tanggal_tenggat'] ?>"
                                        data-status="<?= $r['status'] ?>">
                                        Edit
                                    </button>
                                    <button class="btn btn-delete delete-btn"
                                        data-id="<?= $r['id'] ?>">
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </table>
                    <button class="btn-add add-btn" data-type="rutin">Tambah</button>
                </div>

                <!-- Iuran Tidak Rutin -->
                <div class="iuran-box">
                    <h3>Iuran Tidak Rutin</h3>
                    <table class="table-iuran" id="table-nonrutin">
                        <?php
                        $non = mysqli_query($koneksi, "SELECT * FROM jenis_iuran WHERE jenis = 'tidak rutin'");
                        while ($n = mysqli_fetch_assoc($non)):
                        ?>
                        <tr>
                            <td><?= $n['nama_iuran'] ?></td>
                            <td style="text-align:right;">
                                <div class="action-btns">
                                    <button class="btn btn-edit edit-btn"
                                        data-id="<?= $n['id'] ?>"
                                        data-nama="<?= $n['nama_iuran'] ?>"
                                        data-nominal="<?= $n['nominal'] ?>"
                                        data-periode="<?= $n['periode'] ?>"
                                        data-tanggal-mulai="<?= $n['tanggal_mulai'] ?>"
                                        data-tanggal-tenggat="<?= $n['tanggal_tenggat'] ?>"
                                        data-status="<?= $n['status'] ?>">
                                        Edit
                                    </button>
                                    <button class="btn btn-delete delete-btn"
                                        data-id="<?= $n['id'] ?>">
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </table>
                    <!-- penting: konsisten dengan yang dicek JS / PHP -->
                    <button class="btn-add add-btn" data-type="tidak-rutin">Tambah</button>
                </div>
            </div>

            <!-- Metode Pembayaran -->
            <h3 style="margin-top:30px">Informasi Rekening</h3>
            <?php 
            $metode = mysqli_query($koneksi, "SELECT * FROM metode_pembayaran LIMIT 1");
            $m = mysqli_fetch_assoc($metode); // tidak pakai while ;
            ?>

            <form id="bankForm" method="POST" enctype="multipart/form-data" action="save_rekening.php">
                <input type="hidden" id="bank_id" name="id" value="<?= $m['id'] ?? '' ?>">
                <div class="bank-section"
                    data-bank-id="<?= $m['id'] ?? '' ?>"
                    data-bank-nama="<?= $m['bank_nama'] ?? '' ?>"
                    data-bank-pemilik="<?= $m['bank_pemilik'] ?? '' ?>"
                    data-bank-nomor="<?= $m['bank_nomor'] ?? '' ?>"
                    data-qris-file="<?= $m['qris_file'] ?? '' ?>">

                    <label>Nama Bank</label>
                    <input type="text" id="bank-nama" name="bank_nama" />

                    <label>Nama Pemilik Rekening</label>
                    <input type="text" id="bank-pemilik" name="bank_pemilik" />

                    <label>Nomor Rekening</label>
                    <input type="text" id="bank-nomor" name="bank_nomor" />
                </div>

                <h3>Kode QRIS</h3>
                <div class="qris-section">
                    <label>Upload QRIS (biarkan kosong jika tidak ingin ganti)</label>
                    <input type="file" id="qris-upload" name="qris" accept="image/*" />
                </div>

                <button type="submit" class="btn-save-edit" style="margin-top:25px;">Simpan</button>
            </form>
            <button class="btn-save-edit" style="margin-top:25px; background:#6c757d;" onclick="window.location.href='pembayaran.php'">Kembali</button>
        </div>
    </div>

    <!-- MODAL EDIT IURAN -->
    <div id="editModal" class="modal-edit">
        <div class="edit-content">
            <span class="close-edit">&times;</span>
            <h3>Pengaturan Data Iuran</h3>
            <form id="editForm" method="POST">
                <input type="hidden" id="editId" name="id"/>
                <input type="hidden" name="jenis" id="jenisIuranType">

                <div class="input-group">
                    <label>Nama Iuran *</label>
                    <input type="text" id="editNama" name="nama" required/>
                </div>

                <div class="input-group">
                    <label>Nominal *</label>
                    <input type="number" id="editNominal" name="nominal" min="0" required/>
                </div>

                <div class="input-group">
                    <label>Periode *</label>
                    <select id="editPeriode" name="periode" required>
                        <option value="bulanan">Bulanan</option>
                        <option value="tahunan">Tahunan</option>
                        <option value="sekali bayar">Sekali Bayar</option>
                    </select>
                </div>

                <div class="input-group">
                    <label>Tanggal Mulai *</label>
                    <input type="date" id="tanggal_mulai" name="tanggal_mulai" required/>
                </div>

                <div class="input-group">
                    <label>Tanggal Tenggat *</label>
                    <input type="date" id="tanggal_tenggat" name="tanggal_tenggat" required/>
                </div>

                <div class="input-group">
                    <label>Status</label>
                    <select id="data-status" name="status">
                        <option value="aktif">Aktif</option>
                        <option value="nonaktif">Nonaktif</option>
                    </select>
                </div>

                <input type="hidden" id="data-tanggal-dibuat" name="tanggal_dibuat"/>
                <button type="submit" class="btn-save-edit">Simpan</button>
            </form>
        </div>
    </div>
</body>
</html>
