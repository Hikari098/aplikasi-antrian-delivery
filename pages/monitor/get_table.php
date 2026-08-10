<?php
require_once "../../config/database.php";

// Tangkap ID loket aktif dari filter URL (Contoh: "11", "12", "13", "14", "15")
$loket_aktif = isset($_GET['loket']) ? mysqli_real_escape_string($mysqli, $_GET['loket']) : '';

date_default_timezone_set("Asia/Jakarta");
$tanggal = date("Y-m-d");

// Jika diakses tanpa parameter (?loket=), tampilkan semua data agar fail-safe tidak error
if (empty($loket_aktif)) {
    $query_str = "SELECT * FROM queue_antrian_admisi WHERE tanggal = '$tanggal' ORDER BY no_antrian ASC";
} else {
    // JOIN ke tabel history menggunakan ID Loket baru (11, 12, 13, 14, 15)
    $query_str = "SELECT a.* FROM queue_antrian_admisi a 
                  INNER JOIN queue_antrian_history h ON a.tanggal = h.tanggal AND a.no_antrian = h.no_antrian
                  WHERE a.tanggal = '$tanggal' AND h.id_loket = '$loket_aktif' 
                  ORDER BY a.no_antrian ASC";
}

$query = mysqli_query($mysqli, $query_str) or die('Ada kesalahan pada query : ' . mysqli_error($mysqli));

if (mysqli_num_rows($query) > 0) {
    while ($row = mysqli_fetch_assoc($query)) {
        $nomor_formatted = str_pad($row['no_antrian'], 3, "0", STR_PAD_LEFT);
        
        // Tentukan teks badge status berdasarkan status operasional
        $status_text = ($row['status'] == '1') ? 'DIPANGGIL' : (($row['status'] == '2') ? 'SELESAI' : 'MENUNGGU');
        $status_class = ($row['status'] == '1') ? 'bg-success' : (($row['status'] == '2') ? 'bg-secondary' : 'bg-warning text-dark');

        echo "<tr>";
        echo "<td><span class='text-success fw-bold fs-4'>" . $nomor_formatted . "</span></td>";
        echo "<td>" . htmlspecialchars($row['nama_customer']) . "</td>";
        echo "<td>" . htmlspecialchars($row['nama_driver']) . "</td>";
        echo "<td><span class='text-uppercase fw-bold'>" . htmlspecialchars($row['plat_nomor']) . "</span></td>";
        echo "<td><span class='badge " . $status_class . " px-3 py-2 rounded-pill'>" . $status_text . "</span></td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='5' class='text-muted py-4 fw-bold'>Belum ada antrian untuk tanggal hari ini di loket ini.</td></tr>";
}
?>