<?php
require_once "../../config/database.php";

date_default_timezone_set("Asia/Jakarta");

// Logika Filter Tanggal (Default: Hari Ini)
$tgl_mulai   = isset($_GET['tgl_mulai']) && !empty($_GET['tgl_mulai']) ? $_GET['tgl_mulai'] : date('Y-m-d');
$tgl_selesai = isset($_GET['tgl_selesai']) && !empty($_GET['tgl_selesai']) ? $_GET['tgl_selesai'] : date('Y-m-d');

// Array Nama Loket
$loket_arr = array(
    '11' => 'HT & ISONITE (1)',
    '12' => 'PHOSPHATE COATING (2)',
    '13' => 'RAW MATERIAL (3)',
    '14' => 'AUDIT / MEETING (4)',
    '15' => 'SUPPLIER (5)'
);
?>
<!doctype html>
<html lang="id" class="h-100">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>History & Rekap Antrian Delivery</title>

    <link href="../../assets/img/LOGO%20PMTI.jpg" type="image/jpeg" rel="icon">
    <link href="../../assets/vendor/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="../../assets/css/style.css" rel="stylesheet">

    <style>
        @media print {
            .no-print { display: none !important; }
            body { background-color: #ffffff !important; color: #000000 !important; }
            .card { border: none !important; box-shadow: none !important; }
            .table-success { background-color: #198754 !important; color: #ffffff !important; -webkit-print-color-adjust: exact; }
            .badge { border: 1px solid #000 !important; color: #000 !important; }
        }
    </style>
</head>

<body class="d-flex flex-column h-100 bg-light">

    <!-- HEADER SYSTEM -->
    <header class="bg-success text-white py-3 shadow-sm no-print">
        <div class="container-fluid px-4 d-flex justify-content-between align-items-center">
            <h4 class="fw-bold mb-0"><i class="bi-clock-history me-2"></i> HISTORY & REKAP ANTRIAN DELIVERY</h4>
            <a href="../../index.php" class="btn btn-warning fw-bold text-dark rounded-pill px-3 shadow-sm">
                <i class="bi-house-door-fill me-1"></i> Kembali ke Menu Utama
            </a>
        </div>
    </header>

    <!-- CONTENT -->
    <main class="flex-grow-1 p-4">
        <div class="container-fluid">
            
            <!-- FORM FILTER TANGGAL -->
            <div class="card border-0 shadow-sm rounded-3 mb-4 no-print">
                <div class="card-body p-3">
                    <form method="GET" action="index.php" class="row g-3 align-items-center">
                        <div class="col-auto">
                            <label class="fw-bold text-secondary mb-0"><i class="bi-funnel-fill me-1"></i> Filter Tanggal:</label>
                        </div>
                        <div class="col-auto">
                            <div class="input-group">
                                <span class="input-group-text bg-white fw-bold text-muted">Dari</span>
                                <input type="date" name="tgl_mulai" class="form-control fw-bold" value="<?php echo $tgl_mulai; ?>" required>
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="input-group">
                                <span class="input-group-text bg-white fw-bold text-muted">Sampai</span>
                                <input type="date" name="tgl_selesai" class="form-control fw-bold" value="<?php echo $tgl_selesai; ?>" required>
                            </div>
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-success fw-bold px-3"><i class="bi-search me-1"></i> Cari Data</button>
                            <a href="index.php" class="btn btn-outline-secondary fw-bold px-3">Hari Ini</a>
                        </div>
                        <div class="col-auto ms-auto">
                            <button type="button" onclick="window.print()" class="btn btn-primary fw-bold px-3 shadow-sm">
                                <i class="bi-printer-fill me-1"></i> Cetak / Save PDF
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- HEADER DOKUMEN CETAK KOP RESMI -->
            <div class="d-none d-print-block mb-4 text-center">
                <img src="../../assets/img/LOGO%20PMTI.jpg" style="max-height: 50px;" class="mb-2" alt="Logo PMTI"><br>
                <h4 class="fw-bold text-uppercase mb-1" style="font-size: 16pt;">LAPORAN RIWAYAT PANGGILAN ANTRIAN DELIVERY</h4>
                <p class="fw-bold text-dark mb-1" style="font-size: 11pt; font-style: italic;">PT Parker Metal Treatment Indonesia</p>
                <p class="fw-bold text-muted small mb-0">Periode: <?php echo date('d-m-Y', strtotime($tgl_mulai)); ?> s/d <?php echo date('d-m-Y', strtotime($tgl_selesai)); ?></p>
                <hr style="border-top: 2px solid #000; opacity: 1; margin-top: 10px;">
            </div>

            <!-- TABEL DATA HISTORY -->
            <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-success fw-bold text-uppercase">
                            <tr>
                                <th width="10%">Tanggal</th>
                                <th width="10%">Jam Input</th>
                                <th width="10%">Jam Selesai</th>
                                <th width="8%">Nomor</th>
                                <th>Nama Customer</th>
                                <th>Nama Driver</th>
                                <th>Plat Nomor</th>
                                <th>Loket Tujuan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Query Penggabungan Pintar: Menyatukan Jam Input & Jam Selesai berdasarkan transaksi riil
                            $query_str = "SELECT 
                                            MAX(id) AS id,
                                            tanggal,
                                            no_antrian,
                                            nama_customer,
                                            nama_driver,
                                            plat_nomor,
                                            id_loket,
                                            MIN(NULLIF(jam_input, '00:00:00')) AS jam_input,
                                            MAX(NULLIF(jam_selesai, '00:00:00')) AS jam_selesai
                                          FROM queue_antrian_history 
                                          WHERE tanggal BETWEEN '$tgl_mulai' AND '$tgl_selesai' 
                                          GROUP BY tanggal, no_antrian, id_loket, nama_customer, nama_driver, plat_nomor
                                          ORDER BY MAX(id) DESC";

                            $query_history = mysqli_query($mysqli, $query_str);

                            if ($query_history && mysqli_num_rows($query_history) > 0) {
                                while ($row = mysqli_fetch_assoc($query_history)) {
                                    $no_antrian  = str_pad($row['no_antrian'], 3, "0", STR_PAD_LEFT);
                                    $jam_in      = (!empty($row['jam_input']) && $row['jam_input'] != '-') ? date('H:i:s', strtotime($row['jam_input'])) : '-';
                                    $jam_out     = (!empty($row['jam_selesai']) && $row['jam_selesai'] != '-') ? date('H:i:s', strtotime($row['jam_selesai'])) : '<span class="badge bg-warning text-dark">Proses</span>';
                                    $id_loket    = $row['id_loket'];
                                    $nama_loket  = isset($loket_arr[$id_loket]) ? $loket_arr[$id_loket] : '-';
                                    $tgl_indo    = date('d-m-Y', strtotime($row['tanggal']));
                                    ?>
                                    <tr>
                                        <td class="text-secondary fw-bold"><?php echo $tgl_indo; ?></td>
                                        <td class="fw-bold text-primary"><i class="bi-box-arrow-in-right me-1"></i><?php echo $jam_in; ?></td>
                                        <td class="fw-bold text-success"><i class="bi-check-circle me-1"></i><?php echo $jam_out; ?></td>
                                        <td><span class="badge bg-success fs-6 px-3 py-2"><?php echo $no_antrian; ?></span></td>
                                        <td class="fw-bold"><?php echo !empty($row['nama_customer']) ? $row['nama_customer'] : '-'; ?></td>
                                        <td><?php echo !empty($row['nama_driver']) ? $row['nama_driver'] : '-'; ?></td>
                                        <td class="fw-bold text-uppercase"><?php echo !empty($row['plat_nomor']) ? $row['plat_nomor'] : '-'; ?></td>
                                        <td><span class="text-success fw-bold"><i class="bi-check-circle-fill me-1"></i><?php echo $nama_loket; ?></span></td>
                                    </tr>
                                    <?php
                                }
                            } else {
                                echo '<tr><td colspan="8" class="text-center text-muted py-4 font-monospace">Belum ada histori antrian untuk periode ini.</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>

    <footer class="footer mt-auto py-3 bg-white border-top shadow-sm no-print">
        <div class="container-fluid text-center">
            <span class="text-muted small">&copy; <?php echo date('Y'); ?> - <span class="fw-bold text-success">hikaritecho</span>. All rights reserved.</span>
        </div>
    </footer>

    <script src="../../assets/vendor/js/jquery-3.6.0.min.js"></script>
    <script src="../../assets/vendor/js/bootstrap.min.js"></script>
</body>
</html>