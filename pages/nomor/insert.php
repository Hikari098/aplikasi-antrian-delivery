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

// Bersihkan data live hari kemarin agar penomoran mereset dari 1 setiap pagi
mysqli_query($conn, "DELETE FROM queue_antrian_admisi WHERE tanggal < '$tanggal'");

// Hitung nomor urutan antrian berjalan hari ini
$query = mysqli_query($conn, "SELECT COUNT(*) as total FROM queue_antrian_admisi WHERE tanggal='$tanggal'");
$data = mysqli_fetch_assoc($query);
$total_antrian = ($data) ? (int)$data['total'] : 0;
$no_antrian = $total_antrian + 1;

// FIX UTAMA: Menghapus id_loket dari query INSERT tabel admisi karena kolom tersebut tidak ada di struktur database kamu
$insert = mysqli_query($conn, "INSERT INTO queue_antrian_admisi(tanggal, no_antrian, status, nama_customer, nama_driver, plat_nomor) 
                               VALUES('$tanggal', '$no_antrian', '0', '$nama_customer', '$nama_driver', '$plat_nomor')");
          
if ($insert) {
    // History tetap mencatat id_loket dengan aman karena kolomnya terdaftar resmi di DB kamu
    mysqli_query($conn, "INSERT INTO queue_antrian_history(tanggal, no_antrian, nama_customer, nama_driver, plat_nomor, id_loket) 
                         VALUES('$tanggal', '$no_antrian', '$nama_customer', '$nama_driver', '$plat_nomor', '$id_loket')");
    
    echo "Sukses";
} else {
    echo "Gagal Query Utama: " . mysqli_error($conn);
}
?>