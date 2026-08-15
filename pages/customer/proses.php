<?php
require_once "../../config/database.php";

date_default_timezone_set("Asia/Jakarta");
$tanggal = date("Y-m-d");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_customer = mysqli_real_escape_string($mysqli, $_POST['nama_customer']);
    $nama_driver   = mysqli_real_escape_string($mysqli, $_POST['nama_driver']);
    $plat_nomor    = mysqli_real_escape_string($mysqli, $_POST['plat_nomor']);
    $id_loket      = isset($_POST['id_loket']) ? mysqli_real_escape_string($mysqli, $_POST['id_loket']) : (isset($_POST['loket']) ? mysqli_real_escape_string($mysqli, $_POST['loket']) : '');

    // Generasi Nomor Antrian Harian per Loket / Global
    $query_no = mysqli_query($mysqli, "SELECT MAX(no_antrian) AS max_no FROM queue_antrian_admisi WHERE tanggal = '$tanggal'");
    $data_no  = mysqli_fetch_assoc($query_no);
    $no_antrian = ($data_no['max_no']) ? $data_no['max_no'] + 1 : 1;

    // Simpan ke database beserta id_loket
    $query_insert = "INSERT INTO queue_antrian_admisi (tanggal, no_antrian, status, nama_customer, nama_driver, plat_nomor, id_loket, updated_date) 
                     VALUES ('$tanggal', '$no_antrian', '0', '$nama_customer', '$nama_driver', '$plat_nomor', '$id_loket', NOW())";

    if (mysqli_query($mysqli, $query_insert)) {
        header("Location: index.php?status=success&no=" . $no_antrian);
        exit();
    } else {
        header("Location: index.php?status=error");
        exit();
    }
}
?>