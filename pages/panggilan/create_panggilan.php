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
    // 1. Reset antrian pemanggilan TV
    if (!empty($loket)) {
        mysqli_query($mysqli, "DELETE FROM queue_penggilan_antrian WHERE loket = '$loket'");
    } else {
        mysqli_query($mysqli, "TRUNCATE TABLE queue_penggilan_antrian");
    }

    $query_str = "INSERT INTO queue_penggilan_antrian (antrian, loket, nama_driver) VALUES ('$antrian', '$loket', '$nama_customer')";
    $query = mysqli_query($mysqli, $query_str);

    if ($query) {
        $q_admisi = mysqli_query($mysqli, "SELECT nama_customer, nama_driver, plat_nomor FROM queue_antrian_admisi WHERE no_antrian = '$antrian' AND tanggal = '$tanggal' ORDER BY id DESC LIMIT 1");
        $d_admisi = mysqli_fetch_assoc($q_admisi);

        $cust_name   = !empty($nama_customer) ? $nama_customer : (isset($d_admisi['nama_customer']) ? mysqli_real_escape_string($mysqli, $d_admisi['nama_customer']) : '');
        $driver_name = !empty($nama_driver) ? $nama_driver : (isset($d_admisi['nama_driver']) ? mysqli_real_escape_string($mysqli, $d_admisi['nama_driver']) : '');
        $plat_no     = isset($d_admisi['plat_nomor']) ? mysqli_real_escape_string($mysqli, $d_admisi['plat_nomor']) : '';

        // 2. UPDATE histori tanpa menambah baris duplikat baru
        mysqli_query($mysqli, "INSERT INTO queue_antrian_history (tanggal, no_antrian, id_loket, nama_customer, nama_driver, plat_nomor) 
                               VALUES ('$tanggal', '$antrian', '$loket', '$cust_name', '$driver_name', '$plat_no')
                               ON DUPLICATE KEY UPDATE id_loket = '$loket', nama_customer = '$cust_name', nama_driver = '$driver_name', plat_nomor = '$plat_no'");

        echo json_encode(array("success" => true, "message" => "Berhasil dipanggil"));
    } else {
        echo json_encode(array("success" => false, "error" => mysqli_error($mysqli)));
    }
} else {
    echo json_encode(array("success" => false, "message" => "Nomor antrian kosong"));
}
?>