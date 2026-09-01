<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Credentials: true');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, x-requested-with, Content-Type, Accept, Access-Control-Request-Method");
header('Access-Control-Allow-Methods: GET, POST');
header("Allow: GET, POST");

if (file_exists("../../config/database.php")) {
    include_once "../../config/database.php";
}

$conn = null;
if (isset($mysqli) && $mysqli instanceof mysqli) { $conn = $mysqli; }
elseif (isset($db) && $db instanceof mysqli) { $conn = $db; }
elseif (isset($koneksi) && $koneksi instanceof mysqli) { $conn = $koneksi; }

if (!$conn || $conn->connect_error) {
    echo "Gagal koneksi database!";
    exit;
}

$nama_customer = isset($_POST['nama_customer']) ? mysqli_real_escape_string($conn, trim($_POST['nama_customer'])) : '';
$nama_driver   = isset($_POST['nama_driver']) ? mysqli_real_escape_string($conn, trim($_POST['nama_driver'])) : '';
$plat_nomor    = isset($_POST['plat_nomor']) ? mysqli_real_escape_string($conn, strtoupper(trim($_POST['plat_nomor']))) : '';
$id_loket      = isset($_POST['id_loket']) ? mysqli_real_escape_string($conn, trim($_POST['id_loket'])) : ''; 

date_default_timezone_set("Asia/Jakarta");
$tanggal = date("Y-m-d");

// 1. Ambil nomor antrian terakhir yang terdaftar HARI INI berdasarkan id paling akhir
$query_no = mysqli_query($conn, "SELECT no_antrian FROM queue_antrian_admisi WHERE tanggal = '$tanggal' ORDER BY id DESC LIMIT 1");
$data_no  = mysqli_fetch_assoc($query_no);

if ($data_no) {
    $no_terakhir = (int)$data_no['no_antrian'];
    
    // Jika nomor antrian terakhir sudah 50 atau lebih, RESET KEMBALI KE 1
    if ($no_terakhir >= 50) {
        $no_antrian = 1;
    } else {
        $no_antrian = $no_terakhir + 1;
    }
} else {
    $no_antrian = 1;
}

// 2. Simpan data antrian baru ke tabel admisi
$insert = mysqli_query($conn, "INSERT INTO queue_antrian_admisi(tanggal, no_antrian, status, nama_customer, nama_driver, plat_nomor) 
                               VALUES('$tanggal', '$no_antrian', '0', '$nama_customer', '$nama_driver', '$plat_nomor')");
          
if ($insert) {
    // 3. Catat riwayat ke queue_antrian_history dengan id_loket yang lengkap
    mysqli_query($conn, "INSERT INTO queue_antrian_history(tanggal, no_antrian, nama_customer, nama_driver, plat_nomor, id_loket) 
                         VALUES('$tanggal', '$no_antrian', '$nama_customer', '$nama_driver', '$plat_nomor', '$id_loket')");
    
    echo "Sukses";
} else {
    echo "Gagal Query Utama: " . mysqli_error($conn);
}
?>