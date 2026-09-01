<?php
require_once "../../config/database.php";

date_default_timezone_set("Asia/Jakarta");
$tanggal = date("Y-m-d");

$conn = null;
if (isset($mysqli) && $mysqli instanceof mysqli) { $conn = $mysqli; }
elseif (isset($db) && $db instanceof mysqli) { $conn = $db; }
elseif (isset($koneksi) && $koneksi instanceof mysqli) { $conn = $koneksi; }

// Ambil nomor antrian paling terakhir yang terdaftar hari ini berdasarkan ID terbesar
$query = mysqli_query($conn, "SELECT no_antrian FROM queue_antrian_admisi WHERE tanggal='$tanggal' ORDER BY id DESC LIMIT 1");
$data  = mysqli_fetch_assoc($query);

if ($data) {
    $no_terakhir = (int)$data['no_antrian'];
    // Jika antrian terakhir sudah >= 50, angka berikutnya yang siap diambil adalah 1 (001)
    if ($no_terakhir >= 50) {
        $no_berikutnya = 1;
    } else {
        $no_berikutnya = $no_terakhir + 1;
    }
} else {
    $no_berikutnya = 1;
}

// Format 3 digit angka (contoh: 001)
echo str_pad($no_berikutnya, 3, "0", STR_PAD_LEFT);
?>