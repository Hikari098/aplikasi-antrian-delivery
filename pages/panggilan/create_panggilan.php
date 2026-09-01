<?php
require_once "../../config/database.php";

date_default_timezone_set("Asia/Jakarta");
$tanggal = date("Y-m-d");

$antrian       = isset($_POST['antrian'])       ? mysqli_real_escape_string($mysqli, $_POST['antrian'])       : '';
$loket         = isset($_POST['loket'])         ? mysqli_real_escape_string($mysqli, $_POST['loket'])         : '';
$nama_customer = isset($_POST['nama_customer']) ? mysqli_real_escape_string($mysqli, $_POST['nama_customer']) : '';
$nama_driver   = isset($_POST['nama_driver'])   ? mysqli_real_escape_string($mysqli, $_POST['nama_driver'])   : '';

header('Content-Type: application/json');

if (!empty($antrian)) {
    // 1. Simpan ke queue suara TV
    $query_str = "INSERT INTO queue_penggilan_antrian (antrian, loket, nama_driver) VALUES ('$antrian', '$loket', '$nama_customer')";
    $query = mysqli_query($mysqli, $query_str);

    if ($query) {
        // 2. Ambil detail dari admisi
        $q_admisi = mysqli_query($mysqli, "SELECT nama_customer, nama_driver, plat_nomor FROM queue_antrian_admisi WHERE no_antrian = '$antrian' AND tanggal = '$tanggal' ORDER BY id DESC LIMIT 1");
        $d_admisi = mysqli_fetch_assoc($q_admisi);

        $cust_name   = !empty($nama_customer) ? $nama_customer : (isset($d_admisi['nama_customer']) ? mysqli_real_escape_string($mysqli, $d_admisi['nama_customer']) : '');
        $driver_name = !empty($nama_driver) ? $nama_driver : (isset($d_admisi['nama_driver']) ? mysqli_real_escape_string($mysqli, $d_admisi['nama_driver']) : '');
        $plat_no     = isset($d_admisi['plat_nomor']) ? mysqli_real_escape_string($mysqli, $d_admisi['plat_nomor']) : '';

        // 3. Cek apakah histori antrian hari ini sudah ada
        $q_cek = mysqli_query($mysqli, "SELECT id FROM queue_antrian_history WHERE no_antrian = '$antrian' AND tanggal = '$tanggal' LIMIT 1");
        
        if (mysqli_num_rows($q_cek) > 0) {
            // Update jika dipanggil ulang
            mysqli_query($mysqli, "UPDATE queue_antrian_history 
                                   SET id_loket = '$loket', nama_customer = '$cust_name', nama_driver = '$driver_name', plat_nomor = '$plat_no' 
                                   WHERE no_antrian = '$antrian' AND tanggal = '$tanggal'");
        } else {
            // Insert jika panggilan pertama
            mysqli_query($mysqli, "INSERT INTO queue_antrian_history (tanggal, no_antrian, id_loket, nama_customer, nama_driver, plat_nomor) 
                                   VALUES ('$tanggal', '$antrian', '$loket', '$cust_name', '$driver_name', '$plat_no')");
        }

        echo json_encode(array("success" => true, "message" => "Berhasil dipanggil"));
    } else {
        echo json_encode(array("success" => false, "error" => mysqli_error($mysqli)));
    }
} else {
    echo json_encode(array("success" => false, "message" => "Nomor antrian kosong"));
}
?>