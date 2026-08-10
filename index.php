<!doctype html>
<html lang="en" class="h-100">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Aplikasi Antrian Delivery">
    <meta name="author" content="hikaritecho">

    <title>Aplikasi Antrian Delivery</title>

    <link href="assets/img/LOGO%20PMTI.jpg" type="image/jpeg" rel="icon">

    <link href="assets/vendor/css/bootstrap.min.css" rel="stylesheet">

    <link href="assets/vendor/css/bootstrap-icons.css" rel="stylesheet">

    <link href="assets/vendor/css/swap.css" rel="stylesheet">

    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body class="d-flex flex-column h-100">
    <main class="flex-shrink-0">
        <div class="container">
            <?php
            require_once "config/database.php";
            $query = mysqli_query($mysqli, "SELECT * FROM queue_setting ORDER BY id DESC LIMIT 1") or die('Ada kesalahan pada query tampil data : ' . mysqli_error($mysqli));
            // ambil jumlah baris data hasil query
            $rows = mysqli_num_rows($query);

            if ($rows <> 0) {
                $data = mysqli_fetch_assoc($query);
            } else {
                $data = [];
            }
            ?>
            <div class="alert alert-light mb-3 mt-3" role="alert">
                <div class="text-center">
                    <img src="<?= $data['logo'] && file_exists('assets/img/' . $data['logo']) ? 'assets/img/' . $data['logo'] : 'assets/img/default.png' ?>" alt="Logo" width="60px" class="">
                    <h6 class="mt-2"><?= $data['nama_instansi'] ? $data['nama_instansi'] : 'PT PARKER METAL TREATMENT INDONESIA'; ?></h6>
                    <p>
                        <small>Silahkan pilih halaman yang ingin ditampilkan</small>
                    </p>
                </div>
            </div>

            <div class="row gx-3">
                <div class="col-sm-6 mb-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-3">
                            <div class="feature-icon-1 bg-success bg-gradient mb-4">
                                <i class="bi-people"></i>
                            </div>
                            <h3>Nomor Antrian</h3>
                            <p class="mb-5">Halaman Nomor Antrian digunakan pengunjung untuk mengambil nomor antrian.</p>
                            <a href="pages/nomor" class="btn btn-success rounded-pill px-4 py-2">
                                Tampilkan <i class="bi-arrow-right ms-2"></i>
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="col-sm-6 mb-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-3">
                            <div class="feature-icon-1 bg-success bg-gradient mb-4">
                                <i class="bi-mic"></i>
                            </div>
                            <h3>Panggilan Antrian</h3>
                            <p class="mb-5">Halaman Panggilan Antrian digunakan petugas loket untuk memanggil antrian.</p>
                            <a href="pages/panggilan" class="btn btn-success rounded-pill px-4 py-2">
                                Tampilkan <i class="bi-arrow-right ms-2"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row gx-3">
                <div class="col-sm-6 mb-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-3">
                            <div class="feature-icon-1 bg-success bg-gradient mb-4">
                                <i class="bi-display"></i>
                            </div>
                            <h3>Monitor Antrian</h3>
                            <p class="mb-5">Halaman Monitor Antrian digunakan menampilkan antrian pada monitor.</p>
                            <a href="pages/monitor" class="btn btn-success rounded-pill px-4 py-2">
                                Tampilkan <i class="bi-arrow-right ms-2"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 mb-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-3">
                            <div class="feature-icon-1 bg-success bg-gradient mb-4">
                                <i class="bi-gear"></i>
                            </div>
                            <h3>Setting Antrian</h3>
                            <p class="mb-5">Halaman Setting Antrian digunakan untuk setting antrian.</p>
                            <a href="pages/setting" class="btn btn-success rounded-pill px-4 py-2">
                                Tampilkan <i class="bi-arrow-right ms-2"></i>
                            </a>
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

    <script src="assets/vendor/js/jquery-3.6.0.min.js" type="text/javascript"></script>
    <script src="assets/vendor/js/popper.min.js" type="text/javascript"></script>
    <script src="assets/vendor/js/bootstrap.min.js" type="text/javascript"></script>
</body>

</html>