<?php session_start(); ?>
<!doctype html>
<html lang="en" class="h-100">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Aplikasi Antrian General Static">
    <meta name="author" content="Ade Rahman">

    <title>Aplikasi Antrian Delivery</title>

    <link href="../../assets/img/LOGO%20PMTI.jpg" type="image/jpeg" rel="icon">
    <link href="../../assets/vendor/css/bootstrap.min.css" rel="stylesheet">
    <link href="../../assets/vendor/css/bootstrap-icons.css" rel="stylesheet">
    <link href="../../assets/vendor/css/swap.css" rel="stylesheet">
    <link href="../../assets/vendor/css/datatables.min.css" type="text/css" rel="stylesheet">
    <link href="../../assets/css/style.css" rel="stylesheet">

    <style>
        .preview-logo-box {
            max-width: 100%;
            height: 120px;
            object-fit: contain;
            margin-bottom: 1rem;
        }
    </style>
</head>

<body class="d-flex flex-column h-100 bg-light">
    <main class="flex-shrink-0">
        <div class="container pt-4 pb-5">
            <div class="d-flex flex-column flex-md-row px-4 py-3 mb-4 bg-white rounded-2 shadow-sm border-start border-success border-4">
                <div class="d-flex align-items-center me-md-auto">
                    <i class="bi-gear-fill text-success me-3 fs-3"></i>
                    <h1 class="h5 pt-2 mb-0 fw-bold">Setting Aplikasi Antrian</h1>
                </div>
                <div class="ms-5 ms-md-0 pt-md-2 pb-md-0">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 align-items-center gap-2">
                            <li class="breadcrumb-item mb-0">
                                <a href="../../index.php" class="text-decoration-none">
                                    <i class="bi-house-fill text-success fs-4 me-2"></i>
                                </a>
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>

            <?php if (!isset($_SESSION['username'])) : ?>
                <!-- HALAMAN FORM LOGIN ADMIN -->
                <div class="row justify-content-lg-center pt-5">
                    <div class="col-lg-5 mb-4">
                        <div class="px-4 py-3 mb-4 bg-white rounded-2 shadow-sm">
                            <div class="d-flex justify-content-center align-items-center me-md-auto">
                                <i class="bi-lock-fill text-success me-3 fs-5"></i>
                                <h1 class="h5 pt-2 mb-0">Login Admin</h1>
                            </div>
                        </div>

                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-5">
                                <?php if (isset($_GET['error'])) : ?>
                                    <div class="alert alert-danger py-2 small text-center fw-bold mb-3" role="alert">
                                        <i class="bi-exclamation-triangle-fill me-2"></i> <?= htmlspecialchars($_GET['error']); ?>
                                    </div>
                                <?php endif; ?>

                                <form action="login.php" method="post">
                                    <div class="mb-3">
                                        <label for="username" class="form-label fw-bold text-secondary">Username</label>
                                        <input type="text" class="form-control" id="username" name="username" placeholder="Username" required>
                                    </div>
                                    <div class="mb-4">
                                        <label for="password" class="form-label fw-bold text-secondary">Password</label>
                                        <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                                    </div>
                                    <button type="submit" class="btn btn-success w-100 fw-bold py-2 shadow-sm">
                                        <i class="bi-unlock-fill me-2"></i> Login
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else : ?>
                <!-- HALAMAN UTAMA DASHBOARD SETTING -->
                <?php
                require_once "../../config/database.php";
                $query = mysqli_query($mysqli, "SELECT * FROM queue_setting ORDER BY id DESC LIMIT 1") or die('Ada kesalahan pada query tampil data : ' . mysqli_error($mysqli));
                $rows = mysqli_num_rows($query);
                $data = ($rows <> 0) ? mysqli_fetch_assoc($query) : [];
                ?>
                <form action="" method="post" id="saveSetting" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?= isset($data['id']) ? $data['id'] : ''; ?>">
                    
                    <div class="row">
                        <!-- KOLOM KIRI: Informasi Instansi & Menu Navigasi -->
                        <div class="col-lg-7">
                            <!-- Card Informasi Instansi -->
                            <div class="card border-0 shadow-sm rounded-3 mb-4">
                                <div class="card-header bg-white fw-bold text-dark border-bottom py-3">Informasi Instansi</div>
                                <div class="card-body p-4">
                                    <div class="mb-3">
                                        <label for="nama_instansi" class="form-label fw-bold text-secondary small">Nama Instansi</label>
                                        <input type="text" class="form-control form-control-sm" id="nama_instansi" name="nama_instansi" value="<?= isset($data['nama_instansi']) ? $data['nama_instansi'] : ''; ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="alamat" class="form-label fw-bold text-secondary small">Alamat Lengkap</label>
                                        <textarea class="form-control form-control-sm" id="alamat" name="alamat" rows="2" required><?= isset($data['alamat']) ? $data['alamat'] : ''; ?></textarea>
                                    </div>
                                    <div class="row">
                                        <div class="col-6 mb-3">
                                            <label for="telpon" class="form-label fw-bold text-secondary small">Telpon</label>
                                            <input type="text" class="form-control form-control-sm" id="telpon" name="telpon" value="<?= isset($data['telpon']) ? $data['telpon'] : ''; ?>" required>
                                        </div>
                                        <div class="col-6 mb-3">
                                            <label for="email" class="form-label fw-bold text-secondary small">Email</label>
                                            <input type="email" class="form-control form-control-sm" id="email" name="email" value="<?= isset($data['email']) ? $data['email'] : ''; ?>" required>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="running_text" class="form-label fw-bold text-secondary small">Running Text</label>
                                        <textarea class="form-control form-control-sm" id="running_text" name="running_text" rows="2" required><?= isset($data['running_text']) ? $data['running_text'] : ''; ?></textarea>
                                    </div>
                                    <div class="mb-0">
                                        <label for="youtube_id" class="form-label fw-bold text-secondary small">YouTube ID</label>
                                        <input type="text" class="form-control form-control-sm" id="youtube_id" name="youtube_id" value="<?= isset($data['youtube_id']) ? $data['youtube_id'] : ''; ?>" required>
                                    </div>
                                </div>
                            </div>

                            <!-- Menu Navigasi -->
                            <div class="card border-0 shadow-sm rounded-3 mb-4">
                                <div class="card-header bg-white fw-bold text-dark border-bottom py-3">Menu Navigasi</div>
                                <div class="card-body p-4">
                                    <div class="d-flex flex-wrap gap-2">
                                        <a href="../history/index.php" class="btn btn-outline-success btn-sm fw-semibold py-2 px-3 flex-fill text-center">
                                            <i class="bi-clock-history me-1"></i> Log History
                                        </a>
                                        <a href="../customer/index.php" class="btn btn-outline-success btn-sm fw-semibold py-2 px-3 flex-fill text-center">
                                            <i class="bi-building me-1"></i> Customer
                                        </a>
                                        <a href="../driver/index.php" class="btn btn-outline-success btn-sm fw-semibold py-2 px-3 flex-fill text-center">
                                            <i class="bi-person-badge me-1"></i> Driver
                                        </a>
                                        <a href="../plat/index.php" class="btn btn-outline-success btn-sm fw-semibold py-2 px-3 flex-fill text-center">
                                            <i class="bi-card-text me-1"></i> Plat Nomor
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- KOLOM KANAN: Styling Monitor & Tombol Aksi -->
                        <div class="col-lg-5">
                            <div class="card border-0 shadow-sm rounded-3 mb-4">
                                <div class="card-header bg-white fw-bold text-dark border-bottom py-3">Styling Monitor</div>
                                <div class="card-body p-4 text-center">
                                    <img src="<?= (isset($data['logo']) && $data['logo'] && file_exists('../../assets/img/' . $data['logo'])) ? '../../assets/img/' . $data['logo'] : '../../assets/img/default.png'; ?>" class="rounded mx-auto d-block preview-logo-box" alt="Logo">

                                    <div class="mb-3 text-start">
                                        <label for="logo" class="form-label fw-bold text-secondary small">Pilih Logo</label>
                                        <input class="form-control form-control-sm" type="file" id="logo" name="logo">
                                        <input type="hidden" name="nama_logo" value="<?= isset($data['logo']) ? $data['logo'] : ''; ?>">
                                    </div>

                                    <!-- Pengaturan Warna: 3 Kolom per Baris agar Sejajar -->
                                    <div class="row text-start g-2">
                                        <!-- Baris Pertama -->
                                        <div class="col-4">
                                            <label for="warna_primary" class="form-label fw-bold text-secondary small">Primary</label>
                                            <input type="color" class="form-control form-control-color form-control-sm w-100" id="warna_primary" name="warna_primary" value="<?= isset($data['warna_primary']) ? $data['warna_primary'] : '#563d7c'; ?>" required>
                                        </div>
                                        <div class="col-4">
                                            <label for="warna_secondary" class="form-label fw-bold text-secondary small">Secondary</label>
                                            <input type="color" class="form-control form-control-color form-control-sm w-100" id="warna_secondary" name="warna_secondary" value="<?= isset($data['warna_secondary']) ? $data['warna_secondary'] : '#563d7c'; ?>" required>
                                        </div>
                                        <div class="col-4">
                                            <label for="warna_accent" class="form-label fw-bold text-secondary small">Accent</label>
                                            <input type="color" class="form-control form-control-color form-control-sm w-100" id="warna_accent" name="warna_accent" value="<?= isset($data['warna_accent']) ? $data['warna_accent'] : '#563d7c'; ?>" required>
                                        </div>

                                        <!-- Baris Kedua (Background di bawah Primary, Text tepat di bawah Secondary) -->
                                        <div class="col-4 mt-2">
                                            <label for="warna_background" class="form-label fw-bold text-secondary small">Background</label>
                                            <input type="color" class="form-control form-control-color form-control-sm w-100" id="warna_background" name="warna_background" value="<?= isset($data['warna_background']) ? $data['warna_background'] : '#563d7c'; ?>" required>
                                        </div>
                                        <div class="col-4 mt-2">
                                            <label for="warna_text" class="form-label fw-bold text-secondary small">Text</label>
                                            <input type="color" class="form-control form-control-color form-control-sm w-100" id="warna_text" name="warna_text" value="<?= isset($data['warna_text']) ? $data['warna_text'] : '#563d7c'; ?>" required>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tombol Aksi di Kanan -->
                            <div class="d-flex gap-2 mb-4">
                                <button type="submit" class="btn btn-success flex-fill fw-bold py-2 shadow-sm">
                                    <i class="bi-save-fill me-2"></i> Simpan
                                </button>
                                <button type="button" id="logout" class="btn btn-danger flex-fill fw-bold py-2 shadow-sm">
                                    <i class="bi-box-arrow-right me-2"></i> Logout
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </main>

    <footer class="footer mt-auto py-3 bg-white border-top">
        <div class="container-fluid">
            <div class="copyright text-center text-muted small">
                &copy; <?php echo date('Y') ?> - <span class="fw-bold text-success">hikaritecho</span>. All rights reserved.
            </div>
        </div>
    </footer>

    <script src="../../assets/vendor/js/jquery-3.6.0.min.js" type="text/javascript"></script>
    <script src="../../assets/vendor/js/popper.min.js" type="text/javascript"></script>
    <script src="../../assets/vendor/js/bootstrap.min.js" type="text/javascript"></script>
    <script src="../../assets/vendor/js/datatables.min.js" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).on("submit", "#saveSetting", function(e) {
            e.preventDefault();
            var formData = new FormData(this);

            $.ajax({
                type: 'POST',
                url: 'save.php',
                data: formData,
                contentType: false,
                cache: false,
                processData: false,
                success: function(result) {
                    if (result.trim() === 'Success') {
                        alert("Setting berhasil disimpan");
                        window.location.reload();
                    } else {
                        alert(result);
                    }
                },
            });
        });

        $(document).on("click", "#logout", function(e) {
            $.ajax({
                type: 'POST',
                url: 'logout.php',
                success: function(result) {
                    if (result.trim() === 'Success') {
                        window.location.reload();
                    } else {
                        alert("Eits ada masalah nih, hubungi IT Support yaa!");
                    }
                },
            });
        });
    </script>
</body>

</html>