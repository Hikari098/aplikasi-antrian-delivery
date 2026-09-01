<?php
require_once "../../config/database.php";

date_default_timezone_set("Asia/Jakarta");
?>
<!doctype html>
<html lang="id" class="h-100">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>History Antrian Delivery</title>

    <link href="../../assets/img/LOGO%20PMTI.jpg" type="image/jpeg" rel="icon">
    <link href="../../assets/vendor/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="../../assets/css/style.css" rel="stylesheet">
</head>

<body class="d-flex flex-column h-100 bg-light">

    <nav class="navbar navbar-expand-md navbar-dark bg-success shadow-sm py-3">
        <div class="container-fluid px-4">
            <span class="navbar-brand fw-bold fs-4">
                <i class="bi-clock-history me-2"></i> HISTORY ANTRIAN DELIVERY
            </span>
            <a href="../../index.php" class="btn btn-warning fw-bold border-white text-dark rounded-pill px-3 shadow-sm">
                <i class="bi-house-door-fill me-1"></i> Kembali ke Menu Utama
            </a>
        </div>
    </nav>

    <main class="flex-shrink-0">
        <div class="container-fluid pt-4 px-4">
            <div class="card border-0 shadow-sm rounded-3 bg-white mb-5">
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle border-top" id="tabelHistory">
                            <thead class="table-light fw-bold text-secondary">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Nomor</th>
                                    <th>Nama Customer</th>
                                    <th>Nama Driver</th>
                                    <th>Plat Nomor</th>
                                    <th>Loket Tujuan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Query gabungan fleksibel untuk menampilkan data riil dari admisi jika di histori kosong
                                $query_str = "SELECT 
                                                h.id,
                                                h.tanggal,
                                                h.no_antrian,
                                                h.id_loket,
                                                COALESCE(NULLIF(h.nama_customer, ''), NULLIF(h.nama_customer, '-'), a.nama_customer, '-') AS nama_customer,
                                                COALESCE(NULLIF(h.nama_driver, ''), NULLIF(h.nama_driver, '-'), a.nama_driver, '-') AS nama_driver,
                                                COALESCE(NULLIF(h.plat_nomor, ''), NULLIF(h.plat_nomor, '-'), a.plat_nomor, '-') AS plat_nomor
                                              FROM queue_antrian_history h
                                              LEFT JOIN queue_antrian_admisi a 
                                                ON CAST(h.no_antrian AS UNSIGNED) = CAST(a.no_antrian AS UNSIGNED) 
                                               AND (h.tanggal = a.tanggal OR REPLACE(h.tanggal, '-', '') = REPLACE(a.tanggal, '-', ''))
                                              ORDER BY h.id DESC";

                                $query = mysqli_query($mysqli, $query_str);

                                if ($query && mysqli_num_rows($query) > 0) {
                                    while ($row = mysqli_fetch_assoc($query)) {
                                        $tgl_formatted = date('d-m-Y', strtotime($row['tanggal']));
                                        $no_formatted  = str_pad($row['no_antrian'], 3, "0", STR_PAD_LEFT);
                                        
                                        $loket_text = "LOKET " . $row['id_loket'];
                                        if ($row['id_loket'] == '11') $loket_text = "HT & ISONITE (1)";
                                        elseif ($row['id_loket'] == '12') $loket_text = "PHOSPHATE COATING (2)";
                                        elseif ($row['id_loket'] == '13') $loket_text = "RAW MATERIAL (3)";
                                        elseif ($row['id_loket'] == '14') $loket_text = "AUDIT / MEETING (4)";
                                        elseif ($row['id_loket'] == '15') $loket_text = "SUPPLIER (5)";

                                        echo "<tr>";
                                        echo "<td class='text-muted small'>{$tgl_formatted}</td>";
                                        echo "<td><span class='badge bg-success fs-6 px-3 py-2'>{$no_formatted}</span></td>";
                                        echo "<td class='fw-bold'>{$row['nama_customer']}</td>";
                                        echo "<td class='text-uppercase'>{$row['nama_driver']}</td>";
                                        echo "<td class='fw-bold text-uppercase text-secondary'>{$row['plat_nomor']}</td>";
                                        echo "<td><span class='text-success fw-bold'><i class='bi-check-circle-fill me-1'></i> {$loket_text}</span></td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='6' class='text-center text-muted py-4 font-monospace'>Belum ada histori antrian.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="footer mt-auto py-3 bg-white border-top shadow-sm">
        <div class="container-fluid text-center">
            <span class="text-muted small">&copy; <?php echo date('Y'); ?> - <span class="fw-bold text-success">hikaritecho</span>. All rights reserved.</span>
        </div>
    </footer>

    <script src="../../assets/vendor/js/jquery-3.6.0.min.js"></script>
    <script src="../../assets/vendor/js/bootstrap.min.js"></script>
</body>
</html>