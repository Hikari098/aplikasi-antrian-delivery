<?php
date_default_timezone_set("Asia/Jakarta");

if (file_exists("../../config/database.php")) {
    include "../../config/database.php";
}

$master_customers = [];

if (isset($mysqli) && !$mysqli->connect_error) {
    $query_master_cust = mysqli_query($mysqli, "SELECT nama_customer FROM master_customer ORDER BY nama_customer ASC");
    if ($query_master_cust) {
        while ($cust = mysqli_fetch_assoc($query_master_cust)) {
            $master_customers[] = $cust['nama_customer'];
        }
    }
}
?>
<!doctype html>
<html lang="en" class="h-100">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Aplikasi Antrian Delivery">
    <meta name="author" content="hikaritecho">

    <title>Aplikasi Antrian Delivery</title>

    <link href="../../assets/img/LOGO%20PMTI.jpg" type="image/jpeg" rel="icon">
    <link href="../../assets/vendor/css/bootstrap.min.css" rel="stylesheet">
    <link href="../../assets/vendor/css/bootstrap-icons.css" rel="stylesheet">
    <link href="../../assets/vendor/css/swap.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/style.css">

    <style>
        input:-webkit-autofill,
        input:-webkit-autofill:hover,
        input:-webkit-autofill:focus,
        input:-webkit-autofill:active {
            -webkit-box-shadow: 0 0 0 30px white inset !important;
            -webkit-text-fill-color: #212529 !important;
            transition: background-color 5000s ease-in-out 0s;
        }
    </style>
</head>

<body class="d-flex flex-column h-100">
    <main class="flex-shrink-0">
        <div class="container pt-5">
            <div class="row justify-content-lg-center">
                <div class="col-lg-6 mb-4">
                    <div class="px-4 py-3 mb-4 bg-white rounded-2 shadow-sm">
                        <div class="d-flex align-items-center me-md-auto">
                            <i class="bi-people-fill text-success me-3 fs-3"></i>
                            <h1 class="h5 pt-2">Ambil Nomor Antrian</h1>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-5">
                            <div class="border border-success rounded-2 py-2 mb-4 text-center">
                                <h3 class="pt-2">ANTRIAN SEKARANG</h3>
                                <h1 id="antrian" class="display-4 fw-bold text-success text-center lh-1 pb-2">000</h1>
                            </div>

                            <form id="formAntrian" autocomplete="off">
                                <div class="mb-3">
                                    <label for="nama_customer" class="form-label fw-bold">Nama Customer</label>
                                    <input type="text" class="form-control form-control-lg" name="nama_customer" id="nama_customer" placeholder="Ketik abjad nama perusahaan..." list="rekomendasi_customer" autocomplete="off" required>
                                    
                                    <datalist id="rekomendasi_customer">
                                        <?php foreach ($master_customers as $nama_c): ?>
                                            <option value="<?= htmlspecialchars($nama_c) ?>">
                                        <?php endforeach; ?>
                                    </datalist>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="nama_driver" class="form-label fw-bold">Nama Driver</label>
                                    <input type="text" class="form-control form-control-lg" name="nama_driver" id="nama_driver" placeholder="Masukkan nama driver" autocomplete="off" required>
                                </div>
                                <div class="mb-4">
                                    <label for="plat_nomor" class="form-label fw-bold">Plat Nomor Kendaraan</label>
                                    <input type="text" class="form-control form-control-lg" name="plat_nomor" id="plat_nomor" placeholder="Contoh: B 1234 ABC" style="text-transform: uppercase;" autocomplete="off" required>
                                </div>

                                <div class="mb-4">
                                    <label for="id_loket" class="form-label fw-bold">Pilih Tujuan</label>
                                    <select class="form-select form-select-lg" name="id_loket" id="id_loket" autocomplete="off" required>
                                        <option value="">-- Pilih Tujuan --</option>
                                        <option value="11">HT & ISONITE</option>
                                        <option value="12">PHOSPHATE COATING</option>
                                        <option value="13">RAW MATERIAL</option>
                                        <option value="14">AUDIT / MEETING</option>
                                        <option value="15">SUPPLIER</option>
                                    </select>
                                </div>

                                <button type="submit" class="btn btn-success btn-lg w-100 rounded-pill py-3 fs-5">
                                    <i class="bi-person-plus fs-4 me-2"></i> Ambil Nomor Antrian
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="footer mt-auto py-4 bg-light border-top">
        <div class="container-fluid">
            <div class="copyright text-center mb-2 mb-md-0">
                &copy; <?php echo date('Y') ?> - <span class="fw-bold text-success">hikaritecho</span>. All rights reserved.
            </div>
        </div>
    </footer>

    <script src="../../assets/vendor/js/jquery-3.6.0.min.js" type="text/javascript"></script>
    <script src="../../assets/vendor/js/popper.min.js" type="text/javascript"></script>
    <script src="../../assets/vendor/js/bootstrap.min.js" type="text/javascript"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            $.ajax({
                url: 'get_antrian.php?v=' + Math.random(),
                type: 'GET',
                success: function(data) {
                    var cleanData = data.trim();
                    if (cleanData !== '') {
                        $('#antrian').text(cleanData);
                    }
                },
                error: function() {
                    $('#antrian').text('000');
                }
            });

            $('#formAntrian').on('submit', function(e) {
                e.preventDefault();
                var formData = $(this).serialize();

                $.ajax({
                    type: 'POST',
                    url: 'insert.php',
                    data: formData,
                    success: function(result) {
                        var cleanResult = result.trim();

                        if (cleanResult === 'Sukses') {
                            Swal.fire({
                                title: 'Sukses!',
                                text: 'Nomor antrian Anda berhasil didapatkan.',
                                icon: 'success',
                                confirmButtonColor: '#198754',
                                confirmButtonText: 'OK'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    $('#formAntrian')[0].reset();
                                    $('#formAntrian input').blur();
                                    window.location.href = '../../index.php';
                                }
                            });
                        } else {
                            Swal.fire({
                                title: 'Gagal!',
                                text: result,
                                icon: 'error',
                                confirmButtonColor: '#dc3545'
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Gagal terhubung ke server!',
                            icon: 'error',
                            confirmButtonColor: '#dc3545'
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>