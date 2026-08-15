<?php
require_once "../../config/database.php";

date_default_timezone_set("Asia/Jakarta");
$tanggal = date("Y-m-d");

// Array Konversi Nama Hari ke Bahasa Indonesia
$nama_hari_array = array(
    'Sunday'    => 'Minggu',
    'Monday'    => 'Senin',
    'Tuesday'   => 'Selasa',
    'Wednesday' => 'Rabu',
    'Thursday'  => 'Kamis',
    'Friday'    => 'Jumat',
    'Saturday'  => 'Sabtu'
);
$hari_ini = $nama_hari_array[date('l')];

$loket_aktif = isset($_GET['loket']) ? mysqli_real_escape_string($mysqli, $_GET['loket']) : '';

$nama_loket_teks = "SEMUA LOKET";
if ($loket_aktif == '11') $nama_loket_teks = "HT & ISONITE";
elseif ($loket_aktif == '12') $nama_loket_teks = "PHOSPHATE COATING";
elseif ($loket_aktif == '13') $nama_loket_teks = "RAW MATERIAL";
elseif ($loket_aktif == '14') $nama_loket_teks = "AUDIT / MEETING";
elseif ($loket_aktif == '15') $nama_loket_teks = "SUPPLIER";
?>
<!doctype html>
<html lang="id" class="h-100">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Layar Monitor Antrian TV - <?php echo $nama_loket_teks; ?></title>

    <link href="../../assets/img/LOGO%20PMTI.jpg" type="image/jpeg" rel="icon">
    <link href="../../assets/vendor/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="../../assets/css/style.css" rel="stylesheet">

    <style>
        body {
            background-color: #0d1b2a;
            color: #ffffff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
        }
        .header-monitor {
            background: linear-gradient(90deg, #1b263b 0%, #0d1b2a 100%);
            border-bottom: 3px solid #00b4d8;
        }
        .table-custom {
            font-size: 1.35rem;
            background-color: #ffffff;
            color: #000000;
            border-radius: 12px;
            overflow: hidden;
        }
        .table-custom th {
            background-color: #198754 !important;
            color: #ffffff !important;
            text-align: center !important;
            vertical-align: middle !important;
            padding: 18px 15px;
            font-size: 1.4rem;
        }
        .table-custom td {
            vertical-align: middle !important;
            padding: 18px 15px;
            text-align: center !important;
        }
        .bg-expired {
            background-color: #f8d7da !important;
            color: #842029 !important;
            font-weight: bold;
        }
        .running-text {
            background-color: #000814;
            color: #ffb703;
            font-size: 1.2rem;
            font-weight: bold;
            padding: 10px 0;
            border-top: 2px solid #00b4d8;
        }
    </style>
</head>

<body class="d-flex flex-column h-100">

    <!-- HEADER MONITOR TV -->
    <header class="header-monitor py-3 px-4 shadow-sm">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <i class="bi-tv-fill text-info fs-1 me-3"></i>
                <div>
                    <h2 class="fw-bold mb-0 text-white">INFORMASI ANTRIAN DELIVERY</h2>
                    <h4 class="text-warning fw-bold mb-0"><?php echo $nama_loket_teks; ?></h4>
                </div>
            </div>
            <div class="text-end">
                <h4 class="fw-bold text-white mb-1" id="clock">00:00:00 WIB</h4>
                <span class="badge bg-info text-dark fw-bold fs-6">
                    <i class="bi-calendar-date me-1"></i> <?php echo $hari_ini . ', ' . date('d M Y'); ?>
                </span>
            </div>
        </div>
    </header>

    <!-- MAIN CONTENT TABLE -->
    <main class="flex-grow-1 p-4">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-lg rounded-3 overflow-hidden">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover align-middle mb-0 table-custom" id="tabelMonitor">
                                <thead>
                                    <tr>
                                        <th width="15%">NO. ANTRIAN</th>
                                        <th width="32%">NAMA CUSTOMER</th>
                                        <th width="23%">NAMA DRIVER</th>
                                        <th width="15%">COUNTDOWN</th>
                                        <th width="15%">STATUS</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- RUNNING TEXT FOOTER -->
    <footer class="mt-auto running-text">
        <marquee behavior="scroll" direction="left" scrollamount="6">
            <i class="bi-broadcast me-2"></i> MOHON BERSABAR MENUNGGU PANGGILAN ANTRIAN. TERIMAKASIH ATAS PERHATIAN DAN KERJASAMANYA YANG BAIK. 整理番号が呼ばれるまで、恐れ入りますがそのままお待ちください。
        </marquee>
    </footer>

    <!-- SCRIPT ASSETS -->
    <script src="../../assets/vendor/js/jquery-3.6.0.min.js"></script>
    <script src="../../assets/vendor/js/bootstrap.min.js"></script>

    <script type="text/javascript">
        if (!window.antrianTimestamps) window.antrianTimestamps = {};

        $(document).ready(function() {
            var loket = "<?php echo $loket_aktif; ?>";
            var isSpeaking = false;

            // Jam Digital Realtime
            setInterval(function() {
                var d = new Date();
                var timeStr = d.toLocaleTimeString('id-ID') + ' WIB';
                $('#clock').text(timeStr);
            }, 1000);

            // Cek Izin Suara Autoplay Browser
            $(document).on('click keydown', function() {
                if ('speechSynthesis' in window) {
                    window.speechSynthesis.resume();
                }
            });

            // Load Tabel Monitor
            function fetchMonitorData() {
                $.ajax({
                    type: 'GET',
                    url: '../panggilan/get_antrian.php',
                    data: { loket: loket, v: Math.random() },
                    dataType: 'json',
                    success: function(response) {
                        var html = '';
                        if (response.data && response.data.length > 0 && response.data[0].no_antrian !== '-') {
                            $.each(response.data, function(i, val) {
                                var custName = val.nama_customer ? val.nama_customer : '-';
                                var driverName = val.nama_driver ? val.nama_driver : '-';

                                var idAntrian = val.id;
                                var timestampMasuk = 0;

                                if (val.timestamp && val.timestamp > 0) {
                                    timestampMasuk = val.timestamp * 1000;
                                } else {
                                    if (!window.antrianTimestamps[idAntrian]) {
                                        window.antrianTimestamps[idAntrian] = new Date().getTime();
                                    }
                                    timestampMasuk = window.antrianTimestamps[idAntrian];
                                }

                                var waktuSekarang = new Date().getTime();
                                var selisihDetik = Math.floor((waktuSekarang - timestampMasuk) / 1000);
                                if (selisihDetik < 0) selisihDetik = 0;

                                var totalBatasDetik = 3600;
                                var sisaDetik = totalBatasDetik - selisihDetik;
                                var isExpired = sisaDetik <= 0;

                                var durasiTeks = "";
                                if (isExpired) {
                                    durasiTeks = "00:00";
                                } else {
                                    var menit = Math.floor(sisaDetik / 60);
                                    var detik = sisaDetik % 60;
                                    var strMenit = menit < 10 ? "0" + menit : menit;
                                    var strDetik = detik < 10 ? "0" + detik : detik;
                                    durasiTeks = strMenit + ":" + strDetik;
                                }

                                var rowClass = isExpired ? 'bg-expired' : '';
                                var badgeStatus = val.status === '1' 
                                    ? '<span class="badge bg-success fs-5 px-3 py-2"><i class="bi-megaphone-fill me-1"></i> DIPANGGIL</span>' 
                                    : '<span class="badge bg-secondary fs-5 px-3 py-2">MENUNGGU</span>';

                                html += '<tr class="' + rowClass + '">';
                                html += '<td class="fw-bold fs-2 text-primary text-center">' + val.no_antrian + '</td>';
                                html += '<td class="fw-bold text-center fs-4">' + custName + '</td>';
                                html += '<td class="text-center fs-4">' + driverName + '</td>';
                                
                                if(isExpired) {
                                    html += '<td class="text-center"><span class="badge bg-danger fs-5 px-3 py-2"><i class="bi-exclamation-triangle-fill me-1"></i> ' + durasiTeks + '</span></td>';
                                } else {
                                    html += '<td class="text-center"><span class="badge bg-dark fs-5 px-3 py-2"><i class="bi-hourglass-split me-1"></i> ' + durasiTeks + '</span></td>';
                                }

                                html += '<td class="text-center">' + badgeStatus + '</td>';
                                html += '</tr>';
                            });
                        } else {
                            html = '<tr><td colspan="5" class="text-center text-muted py-5 font-monospace fs-4">Belum Ada Antrian Yang Terdaftar Hari Ini</td></tr>';
                        }
                        $('#tabelMonitor tbody').html(html);
                    }
                });
            }

            // Fungsi Menghapus Antrian Panggilan
            function hapusQueuePanggilan(itemId) {
                $.ajax({
                    type: 'POST',
                    url: 'delete_panggilan.php',
                    data: { id: itemId },
                    complete: function() {
                        isSpeaking = false;
                        fetchMonitorData();
                    }
                });
            }

            // Fungsi Panggilan Suara Menggunakan Native Browser Web Speech API
            function bunyikanPanggilan(teks, itemId) {
                if (!('speechSynthesis' in window)) {
                    hapusQueuePanggilan(itemId);
                    return;
                }

                window.speechSynthesis.cancel();

                var utterance = new SpeechSynthesisUtterance(teks);
                utterance.lang = 'id-ID';
                utterance.rate = 0.85;
                utterance.pitch = 1.0;

                var isFinished = false;
                function selesai() {
                    if (!isFinished) {
                        isFinished = true;
                        hapusQueuePanggilan(itemId);
                    }
                }

                utterance.onend = selesai;
                utterance.onerror = selesai;

                setTimeout(selesai, 6000);

                window.speechSynthesis.speak(utterance);
            }

            // Pengecekan Trigger Panggilan
            function checkVoiceQueue() {
                if (isSpeaking) return;

                $.ajax({
                    type: 'POST',
                    url: 'get_panggilan.php',
                    data: { loket: loket },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success && response.data && response.data.length > 0) {
                            var item = response.data[0];
                            var noAntrianRaw = item.antrian;
                            var noAngka = noAntrianRaw.replace(/[^0-9]/g, '');
                            
                            var teksPanggilan = "Nomor Antrian " + noAngka + " Silahkan Menuju Loket";

                            isSpeaking = true;
                            bunyikanPanggilan(teksPanggilan, item.id);
                        }
                    }
                });
            }

            fetchMonitorData();
            checkVoiceQueue();

            setInterval(function() {
                checkVoiceQueue();
            }, 500);

            setInterval(function() {
                fetchMonitorData();
            }, 1000);
        });
    </script>
</body>
</html>