<?php
require_once "../../config/database.php";

date_default_timezone_set("Asia/Jakarta");
$tanggal = date("Y-m-d");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_customer = mysqli_real_escape_string($mysqli, $_POST['nama_customer']);
    $nama_driver   = mysqli_real_escape_string($mysqli, $_POST['nama_driver']);
    $plat_nomor    = mysqli_real_escape_string($mysqli, $_POST['plat_nomor']);
    $id_loket      = isset($_POST['id_loket']) ? mysqli_real_escape_string($mysqli, $_POST['id_loket']) : (isset($_POST['loket']) ? mysqli_real_escape_string($mysqli, $_POST['loket']) : '');

    // 1. Ambil nomor antrian terakhir yang terdaftar hari ini
    $query_no = mysqli_query($mysqli, "SELECT no_antrian FROM queue_antrian_admisi WHERE tanggal = '$tanggal' ORDER BY id DESC LIMIT 1");
    $data_no  = mysqli_fetch_assoc($query_no);

    if ($data_no) {
        $no_terakhir = (int)$data_no['no_antrian'];
        
        // 2. Jika antrian terakhir sudah 50 atau lebih, reset kembali ke 1
        if ($no_terakhir >= 50) {
            $no_antrian = 1;
        } else {
            $no_antrian = $no_terakhir + 1;
        }
    } else {
        // Jika belum ada antrian sama sekali hari ini
        $no_antrian = 1;
    }

    // 3. Simpan data antrian baru ke database
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