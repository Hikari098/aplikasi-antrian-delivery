<?php
// Panggil koneksi database
require_once "../../config/database.php";

date_default_timezone_set("Asia/Jakarta");

// Tangkap parameter filter pencarian dari form (jika ada)
$search_nama  = isset($_GET['search_nama']) ? mysqli_real_escape_string($mysqli, $_GET['search_nama']) : '';
$filter_loket = isset($_GET['filter_loket']) ? mysqli_real_escape_string($mysqli, $_GET['filter_loket']) : '';

// Query dasar: mengambil dari history dan join ke admisi untuk melengkapi nama customer/driver
$query_str = "SELECT h.*, a.nama_customer, a.nama_driver, a.plat_nomor 
              FROM queue_antrian_history h
              LEFT JOIN queue_antrian_admisi a ON h.tanggal = a.tanggal AND h.no_antrian = a.no_antrian
              WHERE 1=1";

// Jika ada filter pencarian nama
if (!empty($search_nama)) {
    $query_str .= " AND (a.nama_driver LIKE '%$search_nama%' OR a.nama_customer LIKE '%$search_nama%')";
}

// Jika ada filter loket (11 sampai 15)
if (!empty($filter_loket)) {
    $query_str .= " AND h.id_loket = '$filter_loket'";
}

// Urutkan berdasarkan tanggal terbaru dan ID history terbesar (paling update di atas)
$query_str .= " ORDER BY h.tanggal DESC, h.id DESC";

$query = mysqli_query($mysqli, $query_str) or die('Ada kesalahan query: ' . mysqli_error($mysqli));
?>
<!doctype html>
<html lang="en" class="h-100">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>History Antrian Delivery</title>
    
    <link href="../../assets/img/LOGO%20PMTI.jpg" type="image/jpeg" rel="icon">
    <link href="../../assets/vendor/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">

    <style>
        /* Gaya khusus saat cetak PDF agar tombol filter tidak ikut ter-print */
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background-color: #fff !important;
                padding: 0;
            }
            .card {
                border: none !important;
                box-shadow: none !important;
            }
            .table th {
                background-color: #198754 !important;
                color: #fff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>

<body class="d-flex flex-column h-100 bg-light">
    
    <nav class="navbar navbar-expand-md navbar-dark bg-success shadow-sm py-3 no-print">
        <div class="container-fluid px-4">
            <span class="navbar-brand fw-bold fs-4">
                <i class="bi-hourglass-split me-2"></i> HISTORY ANTRIAN DELIVERY
            </span>
            <a href="../../index.php" class="btn btn-warning fw-bold border-white text-dark rounded-pill px-3 shadow-sm">
                <i class="bi-house-door-fill me-1"></i> Home
            </a>
        </div>
    </nav>

    <main class="flex-shrink-0">
        <div class="container-fluid pt-4 px-4">
            
            <nav aria-label="breadcrumb" class="mb-3 no-print">
                <ol class="breadcrumb bg-white px-3 py-2 rounded-3 shadow-sm d-inline-flex mb-0 border-start border-warning border-3">
                    <li class="breadcrumb-item"><a href="../setting/index.php" class="text-success fw-bold text-decoration-none">Setting</a></li>
                    <li class="breadcrumb-item active fw-bold text-secondary" aria-current="page">History</li>
                </ol>
            </nav>

            <div class="card border-0 shadow-sm rounded-3 bg-white mb-4 no-print">
                <div class="card-body p-4">
                    <h5 class="fw-bold text-dark mb-3"><i class="bi-funnel-fill text-success me-2"></i>Filter & Cari Data</h5>
                    <form method="GET" action="" class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted">Cari Nama Driver / Customer</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi-search text-muted"></i></span>
                                <input type="text" name="search_nama" class="form-control bg-light border-start-0" placeholder="Masukkan nama..." value="<?= htmlspecialchars($search_nama) ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-muted">Pilih Ruang Loket</label>
                            <select name="filter_loket" class="form-select bg-light">
                                <option value="">-- Semua Loket --</option>
                                <option value="11" <?= $filter_loket == '11' ? 'selected' : '' ?>>HT & ISONITE (Loket 1)</option>
                                <option value="12" <?= $filter_loket == '12' ? 'selected' : '' ?>>PHOSPHATE COATING (Loket 2)</option>
                                <option value="13" <?= $filter_loket == '13' ? 'selected' : '' ?>>RAW MATERIAL (Loket 3)</option>
                                <option value="14" <?= $filter_loket == '14' ? 'selected' : '' ?>>AUDIT / MEETING (Loket 4)</option>
                                <option value="15" <?= $filter_loket == '15' ? 'selected' : '' ?>>SUPPLIER (Loket 5)</option>
                            </select>
                        </div>
                        <div class="col-md-5 d-flex align-items-end gap-2">
                            <button type="submit" class="btn btn-success fw-bold px-4 rounded-3 shadow-sm w-50 py-2">
                                <i class="bi-filter me-1"></i> Terapkan Filter
                            </button>
                            <a href="index.php" class="btn btn-outline-secondary fw-bold px-3 rounded-3 w-25 py-2">
                                Reset
                            </a>
                            <button type="button" onclick="window.print();" class="btn btn-danger fw-bold px-3 rounded-3 shadow-sm w-25 py-2">
                                <i class="bi-file-earmark-pdf-fill me-1"></i> PDF
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="text-center d-none d-print-block mb-4">
                <img src="../../assets/img/LOGO%20PMTI.jpg" alt="Logo" class="rounded-circle mb-2" width="60px">
                <h4 class="fw-bold text-uppercase mb-0">Laporan Riwayat Panggilan Antrian Delivery</h4>
                <p class="text-muted small">PT Parker Metal Treatment Indonesia</p>
                <hr>
            </div>

            <div class="card border-0 shadow-sm rounded-3 bg-white mb-5">
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle border-top">
                            <thead class="table-light fw-bold text-secondary">
                                <tr>
                                    <th width="12%">Tanggal</th>
                                    <th width="10%">No. Antrian</th>
                                    <th>Nama Customer</th>
                                    <th>Nama Driver</th>
                                    <th width="15%">Plat Nomor</th>
                                    <th width="20%">Loket Tujuan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (mysqli_num_rows($query) > 0): ?>
                                    <?php while ($row = mysqli_fetch_assoc($query)): 
                                        // Format nomor antrian jadi 3 digit (misal: 002)
                                        $no_formatted = str_pad($row['no_antrian'], 3, "0", STR_PAD_LEFT);
                                        
                                        // Konversi ID Loket ke Nama Teks Tampilan
                                        $loket_name = "Loket Tidak Diketahui";
                                        if ($row['id_loket'] == '11') $loket_name = "HT & ISONITE (1)";
                                        elseif ($row['id_loket'] == '12') $loket_name = "PHOSPHATE COATING (2)";
                                        elseif ($row['id_loket'] == '13') $loket_name = "RAW MATERIAL (3)";
                                        elseif ($row['id_loket'] == '14') $loket_name = "AUDIT / MEETING (4)";
                                        elseif ($row['id_loket'] == '15') $loket_name = "SUPPLIER (5)";
                                    ?>
                                        <tr>
                                            <td class="font-monospace text-muted"><?= date('d-m-Y', strtotime($row['tanggal'])) ?></td>
                                            <td><span class="badge bg-success bg-opacity-10 text-dark fw-bold fs-5 px-3 py-1 rounded-3"><?= $no_formatted ?></span></td>
                                            <td class="fw-bold text-dark"><?= htmlspecialchars($row['nama_customer'] ?? '-') ?></td>
                                            <td><?= htmlspecialchars($row['nama_driver'] ?? '-') ?></td>
                                            <td class="text-uppercase fw-bold text-secondary"><?= htmlspecialchars($row['plat_nomor'] ?? '-') ?></td>
                                            <td><span class="text-success fw-bold"><i class="bi-cpu me-1"></i> <?= $loket_name ?></span></td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-5 font-monospace">Tidak ditemukan riwayat data antrian yang sesuai dengan kriteria filter.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <footer class="footer mt-auto py-3 bg-white border-top shadow-sm no-print">
        <div class="container-fluid text-center">
            <span class="text-muted small">&copy; 2026 - <span class="fw-bold text-success">hikaritecho</span>. All rights reserved.</span>
        </div>
    </footer>

    <script src="../../assets/vendor/js/jquery-3.6.0.min.js" type="text/javascript"></script>
    <script src="../../assets/vendor/js/bootstrap.min.js" type="text/javascript"></script>
</body>

</html>