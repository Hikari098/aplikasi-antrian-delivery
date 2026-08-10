<?php
date_default_timezone_set("Asia/Jakarta");

if (file_exists("../../config/database.php")) {
    include "../../config/database.php";
}

$conn = null;
if (isset($mysqli) && !$mysqli->connect_error) { $conn = $mysqli; }
elseif (isset($db) && !$db->connect_error) { $conn = $db; }
elseif (isset($koneksi) && !$koneksi->connect_error) { $conn = $koneksi; }

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$nama_customer = '';
$mode = 'Tambah';

if ($id > 0 && $conn) {
    $query = mysqli_query($conn, "SELECT nama_customer FROM master_customer WHERE id = $id");
    if (mysqli_num_rows($query) > 0) {
        $data = mysqli_fetch_assoc($query);
        $nama_customer = $data['nama_customer'];
        $mode = 'Ubah';
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $mode ?> Customer - PT PMTI</title>
    <link href="../../assets/vendor/css/bootstrap.min.css" rel="stylesheet">
    <link href="../../assets/vendor/css/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container pt-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-5">
                    <h4 class="fw-bold mb-4 text-dark text-center">
                        <i class="bi-pencil-square text-success me-2"></i> <?= $mode ?> Data Customer
                    </h4>
                    <hr>

                    <form id="formCustomer" autocomplete="off">
                        <input type="hidden" name="action" value="<?= strtolower($mode) ?>">
                        <input type="hidden" name="id" value="<?= $id ?>">

                        <div class="mb-4">
                            <label for="nama_customer" class="form-label fw-bold text-secondary">Nama Perusahaan / Customer</label>
                            <input type="text" class="form-control form-control-lg bg-light" name="nama_customer" id="nama_customer" value="<?= htmlspecialchars($nama_customer) ?>" placeholder="Contoh: PT. Nama Perusahaan" required>
                        </div>

                        <div class="row">
                            <div class="col-6">
                                <a href="index.php" class="btn btn-light btn-lg w-100 fw-bold border">Batal</a>
                            </div>
                            <div class="col-6">
                                <button type="submit" class="btn btn-success btn-lg w-100 fw-bold">Simpan</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="../../assets/vendor/js/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script type="text/javascript">
$(document).ready(function() {
    $('#formCustomer').on('submit', function(e) {
        e.preventDefault();
        var formData = $(this).serialize();

        $.ajax({
            type: 'POST',
            url: 'proses.php',
            data: formData,
            success: function(result) {
                var cleanResult = result.trim();
                if (cleanResult === 'Sukses') {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: 'Data master customer sukses disimpan.',
                        icon: 'success',
                        confirmButtonColor: '#198754'
                    }).then(() => {
                        window.location.href = 'index.php';
                    });
                } else {
                    Swal.fire({
                        title: 'Gagal!',
                        text: cleanResult,
                        icon: 'error',
                        confirmButtonColor: '#dc3545'
                    });
                }
            },
            error: function() {
                Swal.fire('Error!', 'Gagal tersambung ke server Laragon!', 'error');
            }
        });
    });
});
</script>
</body>
</html>