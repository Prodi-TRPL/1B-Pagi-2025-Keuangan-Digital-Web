document.getElementById("transaksiForm").addEventListener("submit", function(e) {
    e.preventDefault(); // cegah reload

    // Ambil value sesuai ID HTML kamu
    let namaTransaksi = document.getElementById("nama_transaksi").value.trim();
    let namaTerkait   = document.getElementById("nama_terkait").value.trim();
    let kategori      = document.getElementById("kategori").value;
    let jumlah        = document.getElementById("jumlah").value.trim();
    let metode        = document.getElementById("metode").value;

    // VALIDASI WAJIB ISI
    if (
        namaTransaksi === "" ||
        namaTerkait === "" ||
        kategori === "" ||
        jumlah === "" ||
        metode === ""
    ) {
        alert("Harap lengkapi data terlebih dahulu!");
        return; // STOP kirim data
    }

    // VALIDASI JUMLAH HARUS ANGKA & > 0
    if (isNaN(jumlah) || jumlah <= 0) {
        alert("Jumlah harus berupa angka lebih dari 0!");
        return;
    }

    // Kirim data jika lolos validasi
    let formData = new FormData(this);

    fetch("proses_transaksi.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.text())
    .then(result => {
        if (result.trim() === "sukses") {
            alert(" Transaksi berhasil ditambahkan!");
            this.reset();
        } else {
            alert(" Terjadi kesalahan: " + result);
        }
    })
    .catch(error => {
        alert(" Gagal mengirim data.");
        console.error(error);
    });
});

