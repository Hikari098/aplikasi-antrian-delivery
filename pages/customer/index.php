<?php
date_default_timezone_set("Asia/Jakarta");

// Panggil koneksi database
if (file_exists("../../config/database.php")) {
    include "../../config/database.php";
}

// Ambil koneksi yang valid
$conn = null;
if (isset($mysqli) && !$mysqli->connect_error) { $conn = $mysqli; }
elseif (isset($db) && !$db->connect_error) { $conn = $db; }
elseif (isset($koneksi) && !$koneksi->connect_error) { $conn = $koneksi; }
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Master Customer - PT PMTI</title>
    <link href="../../assets/vendor/css/bootstrap.min.css" rel="stylesheet">
    <link href="../../assets/vendor/css/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link href="../../assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            
            <!-- Header Halaman -->
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center p-3 mb-4 bg-white rounded-2 shadow-sm border-start border-success border-4">
                <div class="d-flex align-items-center mb-2 mb-md-0">
                    <i class="bi-building text-success me-3 fs-3"></i>
                    <h4 class="mb-0 fw-bold text-dark">Kelola Data Master Customer</h4>
                </div>
                <div class="d-flex gap-2">
                    <a href="../setting/index.php" class="btn btn-sm btn-outline-success px-3 rounded-pill fw-bold" style="font-size: 0.85rem;">
                        <i class="bi-gear-fill me-1"></i>Setting
                    </a>
                    <a href="../nomor/index.php" class="btn btn-sm btn-secondary px-3 rounded-pill fw-bold" style="font-size: 0.85rem;">
                        <i class="bi-arrow-left me-1"></i>Antrian
                    </a>
                </div>
            </div>

            <!-- Card Menu Tambah & Upload File Explorer -->
            <div class="card border-0 shadow-sm mb-4 rounded-3">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <!-- Tombol Tambah Manual -->
                        <div class="col-md-4 mb-3 mb-md-0">
                            <a href="tambah_ubah.php" class="btn btn-success w-100 py-2 fw-bold shadow-sm">
                                <i class="bi-plus-circle me-2"></i> Tambah Customer Manual
                            </a>
                        </div>
                        <!-- Form Upload File Explorer (CSV / TXT) -->
                        <div class="col-md-8">
                            <form id="formUploadCustomer" enctype="multipart/form-data" class="input-group">
                                <input type="hidden" name="action" value="import_file">
                                <input type="file" name="file_customer" id="file_customer" class="form-control bg-light" accept=".csv, .txt" required>
                                <button class="btn btn-primary fw-bold px-3 shadow-sm" type="submit">
                                    <i class="bi-upload me-1"></i> Import File Explorer
                                </button>
                            </form>
                            <small class="text-muted d-block mt-1">* Menerima format file `.csv` or `.txt` (Pisahkan nama perusahaan per baris baru).</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card Tabel & Fitur Filter Pencarian -->
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-4">
                    
                    <!-- Input Pencarian / Filter Realtime -->
                    <div class="mb-4">
                        <div class="input-group shadow-sm rounded">
                            <span class="input-group-text bg-light border-end-0"><i class="bi-search text-muted"></i></span>
                            <input type="text" id="filterCari" class="form-control bg-light border-start-0 py-2" placeholder="Ketik nama perusahaan untuk memfilter data secara realtime...">
                        </div>
                    </div>

                    <!-- Tabel Data Master -->
                    <div class="table-responsive rounded-2">
                        <table class="table table-hover align-middle border mb-0" id="tabelCustomer">
                            <thead class="bg-success text-white">
                                <tr>
                                    <th width="8%" class="text-center py-3 text-white fw-bold">No</th>
                                    <th class="py-3 text-white fw-bold">Nama Customer / Perusahaan</th>
                                    <th width="25%" class="text-center py-3 text-white fw-bold">Aksi Operasional</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($conn) {
                                    $query = mysqli_query($conn, "SELECT * FROM master_customer ORDER BY nama_customer ASC");
                                    $no = 1;
                                    if (mysqli_num_rows($query) > 0) {
                                        while ($row = mysqli_fetch_assoc($query)) {
                                            echo "<tr class='baris-data'>";
                                            echo "<td class='text-center fw-bold text-secondary'>" . $no++ . "</td>";
                                            echo "<td class='fw-bold text-dark nama-perusahaan'>" . htmlspecialchars($row['nama_customer']) . "</td>";
                                            echo "<td class='text-center'>
                                                    <a href='tambah_ubah.php?id=" . $row['id'] . "' class='btn btn-warning btn-sm fw-bold px-3 shadow-sm me-1 text-dark'><i class='bi-pencil-square me-1'></i> Ubah</a>
                                                    <button class='btn btn-danger btn-sm fw-bold px-3 shadow-sm tombol-hapus' data-id='" . $row['id'] . "' data-nama='" . htmlspecialchars($row['nama_customer']) . "'><i class='bi-trash me-1'></i> Hapus</button>
                                                  </td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='3' class='text-center text-muted py-4 fw-bold bg-white'>Belum ada data customer terdaftar.</td></tr>";
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

<script src="../../assets/vendor/js/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function(){
    
    // FITUR 1: Filter Realtime
    $("#filterCari").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("#tabelCustomer tbody tr.baris-data").filter(function() {
            $(this).toggle($(this).find(".nama-perusahaan").text().toLowerCase().indexOf(value) > -1);
        });
    });

    // FITUR 2: Import File AJAX dengan Deteksi & Notifikasi Data Dobel
    $("#formUploadCustomer").on("submit", function(e){
        e.preventDefault();
        var formData = new FormData(this);

        Swal.fire({
            title: 'Mohon Tunggu',
            text: 'Sedang memproses data dari file explorer...',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        $.ajax({
            url: 'proses.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response){
                var res = response.trim();
                
                // Cek apakah respons mengandung split pipa "|"
                if(res.indexOf('Sukses|') !== -1) {
                    var parts = res.split('|');
                    var baru = parts[1];
                    var dobel = parts[2];

                    Swal.fire({
                        title: 'Proses Selesai!',
                        html: `Berhasil menambahkan <b>${baru}</b> data customer baru.<br><small class="text-danger">(${dobel} data dobel dilewati agar tidak ganda)</small>`,
                        icon: 'success',
                        confirmButtonColor: '#198754'
                    }).then(() => { 
                        location.reload(); 
                    });
                } else {
                    Swal.fire('Gagal!', res, 'error');
                }
            }
        });
    });

    // FITUR 3: Hapus Data
    $(document).on("click", ".tombol-hapus", function(){
        var id = $(this).data("id");
        var nama = $(this).data("nama");

        Swal.fire({
            title: 'Apakah Anda Yakin?',
            text: "Menghapus customer " + nama + " ?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'proses.php',
                    type: 'POST',
                    data: { action: 'hapus', id: id },
                    success: function(response){
                        if(response.trim() === 'Sukses') {
                            Swal.fire('Terhapus!', 'Data customer berhasil dihapus.', 'success')
                            .then(() => { location.reload(); });
                        } else {
                            Swal.fire('Gagal!', response, 'error');
                        }
                    }
                });
            }
        });
    });

});
</script>
</body>
</html>