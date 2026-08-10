<?php
require_once "../../config/database.php";

date_default_timezone_set("Asia/Jakarta");
$tanggal = date("Y-m-d");

// Ambil data pengaturan layout antrian dari database
$query_setting = mysqli_query($mysqli, "SELECT * FROM queue_setting ORDER BY id DESC LIMIT 1") or die(mysqli_error($mysqli));
$data_setting = mysqli_fetch_assoc($query_setting) ?: [];

// Tangkap parameter ID loket dari URL (Contoh: ?loket=11)
$loket_aktif = isset($_GET['loket']) ? mysqli_real_escape_string($mysqli, $_GET['loket']) : '';

// Tentukan Nama Display Monitor berdasarkan ID Angka agar judul di TV rapi
$nama_loket_tampil = "GLOBAL MONITOR";
if ($loket_aktif == '11') $nama_loket_tampil = "HT & ISONITE";
elseif ($loket_aktif == '12') $nama_loket_tampil = "PHOSPHATE COATING";
elseif ($loket_aktif == '13') $nama_loket_tampil = "RAW MATERIAL";
elseif ($loket_aktif == '14') $nama_loket_tampil = "AUDIT / MEETING";
elseif ($loket_aktif == '15') $nama_loket_tampil = "SUPPLIER";
?>
<!DOCTYPE html>
<html lang="en" class="h-100">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Layar Monitor Antrian TV - <?= htmlspecialchars($nama_loket_tampil); ?></title>
    
    <link href="../../assets/img/LOGO%20PMTI.jpg" type="image/jpeg" rel="icon">
    <link href="../../assets/vendor/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="../../assets/vendor/css/swap.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/style.css">
    
    <style>
        body { overflow: hidden; }
        main { height: calc(100vh - 140px); position: relative; z-index: 0; }
        .table-samsat th { background-color: #198754 !important; color: #fff !important; font-weight: bold; text-transform: uppercase; font-size: 1.2rem; padding: 15px; }
        .table-samsat td { font-size: 1.3rem; font-weight: bold; padding: 15px; vertical-align: middle; }
        .table-samsat tbody tr { background-color: #ffffff !important; color: #212529 !important; border-bottom: 2px solid #dee2e6 !important; }
        .monitor-sidebar { display: flex; flex-direction: column; gap: 15px; }
    </style>
</head>

<body class="d-flex flex-column h-100" style="background-color:<?= $data_setting['warna_background'] ?? '#2B303A' ?>;">
    
    <header style="background-color:<?= $data_setting['warna_primary'] ?? '#198754' ?>;" class="d-flex justify-content-between align-items-center py-3 px-5 border-bottom">
        <span style="color:<?= $data_setting['warna_text'] ?? '#fff' ?>;" class="fs-3 fw-bold">
            INFORMASI ANTRIAN DELIVERY - <?= htmlspecialchars($nama_loket_tampil); ?>
        </span>
        <div class="d-flex align-items-center fs-4" style="color:<?= $data_setting['warna_text'] ?? '#fff' ?>;">
            <div class="me-5"><i class="bi bi-calendar3 me-2"></i><span id="date"><?= date('d M Y'); ?></span></div>
            <div><i class="bi bi-clock me-2"></i><span id="time">00:00:00 WIB</span></div>
        </div>
    </header>

    <main class="px-5 my-3 flex-grow-1">
        <div class="row h-100">
            <div class="col-md-8 h-100 overflow-auto">
                <div class="card shadow border-0 h-100">
                    <div class="card-body p-0">
                        <table class="table table-bordered text-center align-middle mb-0 table-samsat">
                            <thead>
                                <tr>
                                    <th style="width: 15%;">No. Antrian</th>
                                    <th style="width: 25%;">Nama Customer</th>
                                    <th style="width: 25%;">Nama Driver</th>
                                    <th style="width: 20%;">Plat No.</th>
                                    <th style="width: 15%;">Status</th>
                                </tr>
                            </thead>
                            <tbody id="tabelAntrianSamsat">
                                <tr>
                                    <td colspan="5" class="text-muted py-4 fw-bold">Memuat data antrian loket...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-4 d-flex flex-column monitor-sidebar">
                <div class="card text-center text-white bg-success shadow border-0 w-100 mb-2 py-2">
                    <h5 class="fw-bold pt-2">NOMOR ANTRIAN DIPANGGIL</h5>
                    <h1 id="antrian-sekarang" class="fw-bold display-1 py-3">-</h1>
                    <h4 class="card-footer fw-bold py-2" style="margin: 0;"><?= htmlspecialchars($nama_loket_tampil); ?></h4>
                </div>
                
                <div class="row g-2">
                    <div class="col-6">
                        <div class="card text-center text-white bg-warning shadow border-0 w-100 py-2">
                            <h6 style="font-size: 0.9rem;" class="text-dark fw-bold">SELANJUTNYA</h6>
                            <h3 id="antrian-selanjutnya" class="fw-bold my-1 text-dark">-</h3>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card text-center text-white bg-primary shadow border-0 w-100 py-2">
                            <h6 style="font-size: 0.9rem;" class="fw-bold">TOTAL ANTRIAN</h6>
                            <h3 id="jumlah-antrian" class="fw-bold my-1">-</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="overflow-hidden w-100 p-2 mt-auto" style="background-color: <?= $data_setting['warna_primary'] ?? '#198754' ?>; height: 50px;">
        <marquee class="text-white mt-1" scrollamount="6">
            <b><?= htmlspecialchars($data_setting['running_text'] ?? 'Selamat Datang di Layanan Sistem Antrian Kendaraan Delivery PMTI'); ?></b>
        </marquee>
    </footer>

    <audio id="tingtung" src="../../assets/audio/tingtung.mp3"></audio>

    <script src="../../assets/vendor/js/jquery-3.6.0.min.js" type="text/javascript"></script>
    <script src="../../assets/vendor/js/bootstrap.min.js" type="text/javascript"></script>
    <script src="../../assets/vendor/js/responsivevoice.js" type="text/javascript"></script>

    <script>
    $(document).ready(function() {
        var bell = document.getElementById('tingtung');
        var queuePanggil = [];
        var isPlay = false;
        var loketAktif = "<?php echo $loket_aktif; ?>";

        // Memicu izin audio browser agar fitur suara tidak diblokir otomatis
        $(document).on('click', function() {
            if (typeof responsiveVoice !== 'undefined') {
                responsiveVoice.speak("", "Indonesian Female", { volume: 0 });
            }
        });

        // Fungsi mengambil baris tabel HTML dari get_table.php
        const get_tabel_samsat = () => {
            $.ajax({
                url: 'get_table.php',
                method: 'GET',
                data: { loket: loketAktif, v: Math.random() },
                success: function(htmlData) {
                    $('#tabelAntrianSamsat').html(htmlData);
                },
                error: function(xhr, status, error) {
                    console.error("Gagal memuat get_table.php:", error);
                }
            });
        }

        // Fungsi mendeteksi instruksi panggilan suara baru dari petugas loket
        const get_panggilan = () => {
            $.ajax({
                url: 'get_panggilan.php',
                method: 'POST',
                data: { loket: loketAktif },
                dataType: 'json',
                success: function(result) {
                    if (result.success && Array.isArray(result.data)) {
                        result.data.forEach(function(el) {
                            if (!queuePanggil.some(item => item.id === el.id)) {
                                queuePanggil.push(el);
                                if (!isPlay) panggilAntrian();
                            }
                        });
                    }
                }
            });
        }

        // Jalankan pemuatan data awal
        $('#jumlah-antrian').load('../panggilan/get_jumlah_antrian.php?loket=' + encodeURIComponent(loketAktif));
        $('#antrian-sekarang').load('../panggilan/get_antrian_sekarang.php?loket=' + encodeURIComponent(loketAktif));
        $('#antrian-selanjutnya').load('../panggilan/get_antrian_selanjutnya.php?loket=' + encodeURIComponent(loketAktif));
        get_tabel_samsat();
        get_panggilan();

        // Refresh realtime berkala setiap 1.5 detik
        setInterval(function() {
            $('#jumlah-antrian').load('../panggilan/get_jumlah_antrian.php?loket=' + encodeURIComponent(loketAktif));
            $('#antrian-sekarang').load('../panggilan/get_antrian_sekarang.php?loket=' + encodeURIComponent(loketAktif));
            $('#antrian-selanjutnya').load('../panggilan/get_antrian_selanjutnya.php?loket=' + encodeURIComponent(loketAktif));
            get_tabel_samsat();
            get_panggilan();
        }, 1500);

        // Pengolahan Mesin Suara Panggilan (ResponsiveVoice)
        function panggilAntrian() {
            if (queuePanggil.length > 0) {
                let val = queuePanggil[0];
                if (!isPlay) {
                    isPlay = true;
                    let formatted = String(val.antrian).padStart(3, '0');
                    $("#antrian-sekarang").html(formatted).fadeOut(200).fadeIn(200);
                    
                    // KONVERSI SUARA: Mengubah sebutan internal ID (11-15) menjadi suara Loket (1-5)
                    var nomorLoketSuara = "";
                    if (val.loket == "11") nomorLoketSuara = "1";
                    else if (val.loket == "12") nomorLoketSuara = "2";
                    else if (val.loket == "13") nomorLoketSuara = "3";
                    else if (val.loket == "14") nomorLoketSuara = "4";
                    else if (val.loket == "15") nomorLoketSuara = "5";
                    else nomorLoketSuara = val.loket;

                    bell.currentTime = 0;
                    bell.play().then(_ => {
                        setTimeout(function() {
                            let spelled = formatted.split('').map(d => d === '0' ? 'kosong' : d).join(' ');
                            responsiveVoice.speak("Nomor Antrian, " + spelled + ", menuju loket " + nomorLoketSuara, "Indonesian Female", {
                                rate: 0.85, 
                                pitch: 1,
                                volume: 1,
                                onend: () => {
                                    $.ajax({ url: 'delete_panggilan.php', method: 'POST', data: { id: val.id } });
                                    queuePanggil.shift();
                                    isPlay = false;
                                    if (queuePanggil.length > 0) panggilAntrian();
                                }
                            });
                        }, 2000);
                    }).catch(() => { isPlay = false; queuePanggil.shift(); });
                }
            }
        }
    });

    // Menjalankan Jam Digital Realtime di Pojok Kanan Atas Header
    setInterval(function jam() {
        var d = new Date();
        var h = String(d.getHours()).padStart(2,'0'), m = String(d.getMinutes()).padStart(2,'0'), s = String(d.getSeconds()).padStart(2,'0');
        document.getElementById("time").innerHTML = h + ":" + m + ":" + s + " WIB";
    }, 1000);
    </script>
</body>
</html>