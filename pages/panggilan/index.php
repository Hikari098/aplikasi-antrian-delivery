<?php
// Pastikan koneksi database tersedia di awal halaman panggilan
require_once "../../config/database.php";

date_default_timezone_set("Asia/Jakarta");
$tanggal = date("Y-m-d");

// Mengambil parameter loket yang aktif dibuka oleh petugas
$loket_aktif = isset($_GET['loket']) ? mysqli_real_escape_string($mysqli, $_GET['loket']) : '';
?>
<!doctype html>
<html lang="en" class="h-100">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Aplikasi Antrian Delivery">
    <meta name="author" content="hikaritecho">

    <title>Halaman Panggilan Antrian</title>

    <link href="../../assets/img/LOGO%20PMTI.jpg" type="image/jpeg" rel="icon">
    <link href="../../assets/vendor/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="../../assets/vendor/css/swap.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>

<body class="d-flex flex-column h-100 bg-light">
    
    <nav class="navbar navbar-expand-md navbar-dark bg-success shadow-sm py-3">
        <div class="container-fluid px-4">
            <span class="navbar-brand fw-bold fs-4">
                <i class="bi-megaphone-fill me-2"></i> KONTROL PANGGILAN ANTRIAN
            </span>
            <div class="d-flex align-items-center text-white fw-bold">
                <button type="button" class="btn btn-warning fw-bold border-white text-dark me-3 rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalPilihMonitor">
                    <i class="bi-tv-fill me-1"></i> Buka Layar Monitor TV
                </button>
                <span class="bg-dark bg-opacity-25 px-3 py-2 rounded-3">
                    <i class="bi-calendar3 me-2"></i> <?php echo date('d-m-Y'); ?>
                </span>
            </div>
        </div>
    </nav>

    <main class="flex-shrink-0">
        <div class="container-fluid pt-3 px-4">
            
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb bg-white px-3 py-2 rounded-3 shadow-sm d-inline-flex mb-0 border-start border-warning border-3">
                    <li class="breadcrumb-item">
                        <a href="../../index.php" class="text-success fw-bold text-decoration-none">
                            <i class="bi-house-door-fill me-1"></i> Home
                        </a>
                    </li>
                    <li class="breadcrumb-item active fw-bold text-secondary" aria-current="page">Panggilan</li>
                </ol>
            </nav>
            
            <?php if (empty($loket_aktif)): ?>
                <div class="row justify-content-center pt-4">
                    <div class="col-lg-6 text-center">
                        <div class="card border-0 shadow-sm p-5 bg-white rounded-3">
                            <p class="text-muted mb-4">Silakan pilih loket yang ingin Anda operasikan hari ini agar sistem dapat memfilter data antrian secara akurat.</p>
                            <div class="d-grid gap-2">
                                <a href="?loket=11" class="btn btn-success btn-lg fw-bold py-3 text-start px-4 mb-2 shadow-sm rounded-3"><i class="bi-cpu me-3 fs-5"></i> Buka Kontrol: HT & ISONITE</a>
                                <a href="?loket=12" class="btn btn-success btn-lg fw-bold py-3 text-start px-4 mb-2 shadow-sm rounded-3"><i class="bi-layers me-3 fs-5"></i> Buka Kontrol: PHOSPHATE COATING</a>
                                <a href="?loket=13" class="btn btn-success btn-lg fw-bold py-3 text-start px-4 mb-2 shadow-sm rounded-3"><i class="bi-box-seam me-3 fs-5"></i> Buka Kontrol: RAW MATERIAL</a>
                                <a href="?loket=14" class="btn btn-success btn-lg fw-bold py-3 text-start px-4 mb-2 shadow-sm rounded-3"><i class="bi-person-workspace me-3 fs-5"></i> Buka Kontrol: AUDIT / MEETING</a>
                                <a href="?loket=15" class="btn btn-success btn-lg fw-bold py-3 text-start px-4 mb-2 shadow-sm rounded-3"><i class="bi-truck me-3 fs-5"></i> Buka Kontrol: SUPPLIER</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>

                <div class="row mb-4">
                    <div class="col-12">
                        <div class="bg-white rounded-3 shadow-sm p-3 border-start border-success border-4 d-flex align-items-center justify-content-between">
                            <div>
                                <h5 class="text-muted mb-0 font-monospace text-uppercase fw-bold">Loket Operasional Saat Ini:</h5>
                                <h2 class="text-success fw-bold mb-0">
                                    <?php 
                                        if ($loket_aktif == '11') echo "HT & ISONITE";
                                        elseif ($loket_aktif == '12') echo "PHOSPHATE COATING";
                                        elseif ($loket_aktif == '13') echo "RAW MATERIAL";
                                        elseif ($loket_aktif == '14') echo "AUDIT / MEETING";
                                        elseif ($loket_aktif == '15') echo "SUPPLIER";
                                        else echo "LOKET TIDAK DIKENAL";
                                    ?>
                                </h2>
                            </div>
                            <a href="index.php" class="btn btn-outline-danger btn-sm fw-bold px-3"><i class="bi-arrow-left-right me-1"></i> Ganti Loket</a>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm bg-white text-dark rounded-3 p-3 text-center mb-3">
                            <h6 class="text-muted fw-bold">JUMLAH ANTRIAN</h6>
                            <h1 id="jumlah_antrian" class="display-3 fw-bold text-primary my-2">0</h1>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm bg-white text-dark rounded-3 p-3 text-center mb-3">
                            <h6 class="text-muted fw-bold">ANTRIAN SEKARANG</h6>
                            <h1 id="antrian_sekarang" class="display-3 fw-bold text-success my-2">-</h1>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm bg-white text-dark rounded-3 p-3 text-center mb-3">
                            <h6 class="text-muted fw-bold">ANTRIAN SELANJUTNYA</h6>
                            <h1 id="antrian_selanjutnya" class="display-3 fw-bold text-warning my-2">-</h1>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm bg-white text-dark rounded-3 p-3 text-center mb-3">
                            <h6 class="text-muted fw-bold">SISA ANTRIAN</h6>
                            <h1 id="sisa_antrian" class="display-3 fw-bold text-danger my-2">0</h1>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-3 bg-white mb-5">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold text-dark mb-0"><i class="bi-list-ul me-2"></i>Daftar Urutan Driver Mengantri</h5>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle border-top" id="tabelAntrian">
                                <thead class="table-light fw-bold text-secondary">
                                    <tr>
                                        <th width="10%">Nomor</th>
                                        <th>Nama Customer</th>
                                        <th>Nama Driver</th>
                                        <th>Plat Nomor</th>
                                        <th width="20%" class="text-center">Aksi Panggilan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <div class="modal fade" id="modalPilihMonitor" tabindex="-1" aria-labelledby="modalPilihMonitorLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-3">
                <div class="modal-header bg-success text-white py-3">
                    <h5 class="modal-title fw-bold" id="modalPilihMonitorLabel"><i class="bi-display me-2"></i> PILIH LAYAR MONITOR TV</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 bg-light">
                    <p class="text-muted small mb-3">Silakan pilih layar monitor sesuai alur loket tujuan untuk ditampilkan di layar TV:</p>
                    <div class="d-grid gap-2">
                        <a href="../monitor/index.php?loket=11" target="_blank" class="btn btn-outline-success text-start fw-bold py-3 px-4 shadow-sm rounded-3"><i class="bi-cpu me-3 fs-5"></i> Monitor: HT & ISONITE</a>
                        <a href="../monitor/index.php?loket=12" target="_blank" class="btn btn-outline-success text-start fw-bold py-3 px-4 shadow-sm rounded-3"><i class="bi-layers me-3 fs-5"></i> Monitor: PHOSPHATE COATING</a>
                        <a href="../monitor/index.php?loket=13" target="_blank" class="btn btn-outline-success text-start fw-bold py-3 px-4 shadow-sm rounded-3"><i class="bi-box-seam me-3 fs-5"></i> Monitor: RAW MATERIAL</a>
                        <a href="../monitor/index.php?loket=14" target="_blank" class="btn btn-outline-success text-start fw-bold py-3 px-4 shadow-sm rounded-3"><i class="bi-person-workspace me-3 fs-5"></i> Monitor: AUDIT / MEETING</a>
                        <a href="../monitor/index.php?loket=15" target="_blank" class="btn btn-outline-success text-start fw-bold py-3 px-4 shadow-sm rounded-3"><i class="bi-truck me-3 fs-5"></i> Monitor: SUPPLIER</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer mt-auto py-3 bg-white border-top shadow-sm">
        <div class="container-fluid text-center">
            <span class="text-muted small">&copy; <?php echo date('Y'); ?> - <span class="fw-bold text-success">hikaritecho</span>. All rights reserved.</span>
        </div>
    </footer>

    <script src="../../assets/vendor/js/jquery-3.6.0.min.js" type="text/javascript"></script>
    <script src="../../assets/vendor/js/bootstrap.min.js" type="text/javascript"></script>

    <?php if (!empty($loket_aktif)): ?>
    <script type="text/javascript">
        $(document).ready(function() {
            var loket = "<?php echo $loket_aktif; ?>";

            function loadCounter() {
                $('#jumlah_antrian').load('get_jumlah_antrian.php?loket=' + loket + '&v=' + Math.random());
                $('#antrian_sekarang').load('get_antrian_sekarang.php?loket=' + loket + '&v=' + Math.random());
                $('#antrian_selanjutnya').load('get_antrian_selanjutnya.php?loket=' + loket + '&v=' + Math.random());
                $('#sisa_antrian').load('get_sisa_antrian.php?loket=' + loket + '&v=' + Math.random());
            }

            function loadTabel() {
                $.ajax({
                    type: 'GET',
                    url: 'get_antrian.php',
                    data: { loket: loket, v: Math.random() },
                    dataType: 'json',
                    success: function(response) {
                        var html = '';
                        if (response.data && response.data.length > 0 && response.data[0].no_antrian !== '-') {
                            $.each(response.data, function(i, val) {
                                var custName = val.nama_customer ? val.nama_customer : '-';
                                var driverName = val.nama_driver ? val.nama_driver : '-';

                                html += '<tr>';
                                html += '<td class="fw-bold fs-5 text-success">' + val.no_antrian + '</td>';
                                html += '<td class="fw-bold text-dark">' + custName + '</td>'; 
                                html += '<td>' + driverName + '</td>';
                                html += '<td class="fw-bold text-uppercase">' + val.plat_nomor + '</td>';
                                
                                // LOGIKA PANGGIL DAN PANGGIL ULANG
                                if(val.status === '0') {
                                    // Belum dipanggil sama sekali
                                    html += '<td class="text-center">';
                                    html += '<button class="btn btn-success btn-sm btn-panggil px-3 fw-bold rounded-pill shadow-sm" data-id="'+val.id+'" data-no="'+val.no_antrian+'">';
                                    html += '<i class="bi-megaphone me-1"></i> Panggil';
                                    html += '</button>';
                                    html += '</td>';
                                } else {
                                    // Sudah dipanggil -> Menampilkan Tombol Panggil Ulang (Warna Kuning)
                                    html += '<td class="text-center">';
                                    html += '<button class="btn btn-warning btn-sm btn-panggil-ulang px-3 fw-bold rounded-pill text-dark shadow-sm me-1" data-id="'+val.id+'" data-no="'+val.no_antrian+'" title="Putar ulang panggilan suara di TV">';
                                    html += '<i class="bi-arrow-clockwise me-1"></i> Panggil Ulang';
                                    html += '</button>';
                                    html += '</td>';
                                }
                                html += '</tr>';
                            });
                        } else {
                            html = '<tr><td colspan="5" class="text-center text-muted py-4 font-monospace">Belum ada antrian yang terdaftar di loket ini hari ini.</td></tr>';
                        }
                        $('#tabelAntrian tbody').html(html);
                    }
                });
            }

            // AKSI PANGGIL PERTAMA KALI
            $(document).on('click', '.btn-panggil', function() {
                var id = $(this).data('id');
                var no = $(this).data('no');
                
                $.ajax({
                    type: 'POST',
                    url: 'update.php',
                    data: { id: id },
                    success: function() {
                        $.ajax({
                            type: 'POST',
                            url: 'create_panggilan.php',
                            data: { antrian: no, loket: loket },
                            success: function() {
                                loadCounter();
                                loadTabel();
                            }
                        });
                    }
                });
            });

            // AKSI PANGGIL ULANG (RECALL)
            $(document).on('click', '.btn-panggil-ulang', function() {
                var no = $(this).data('no');
                
                // Langsung mentrigger suara panggilan ke TV Monitor tanpa mengubah status lagi
                $.ajax({
                    type: 'POST',
                    url: 'create_panggilan.php',
                    data: { antrian: no, loket: loket },
                    success: function() {
                        loadCounter();
                        loadTabel();
                    }
                });
            });

            loadCounter();
            loadTabel();
            setInterval(function() {
                loadCounter();
                loadTabel();
            }, 3000);
        });
    </script>
    <?php endif; ?>
</body>
</html>