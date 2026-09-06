<?php
require_once "../../config/database.php";

date_default_timezone_set("Asia/Jakarta");
$tanggal = date("Y-m-d");

$loket_aktif = isset($_GET['loket']) ? mysqli_real_escape_string($mysqli, $_GET['loket']) : '';

// Ambil data antrian aktif hari ini yang belum selesai (status '0' & '1')
if (!empty($loket_aktif)) {
    $query_str = "SELECT a.*, 
                    (SELECT h.jam_input FROM queue_antrian_history h 
                     WHERE h.no_antrian = a.no_antrian AND h.tanggal = a.tanggal AND h.id_loket = a.id_loket 
                     ORDER BY h.id DESC LIMIT 1) AS jam_input
                  FROM queue_antrian_admisi a
                  WHERE a.tanggal = '$tanggal' 
                    AND a.status IN ('0', '1')
                    AND a.id_loket = '$loket_aktif' 
                  ORDER BY a.id ASC";
} else {
    $query_str = "SELECT a.*, 
                    (SELECT h.jam_input FROM queue_antrian_history h 
                     WHERE h.no_antrian = a.no_antrian AND h.tanggal = a.tanggal AND h.id_loket = a.id_loket 
                     ORDER BY h.id DESC LIMIT 1) AS jam_input
                  FROM queue_antrian_admisi a
                  WHERE a.tanggal = '$tanggal' 
                    AND a.status IN ('0', '1')
                  ORDER BY a.id ASC";
}

$query = mysqli_query($mysqli, $query_str);
$response = array();
$response["data"] = array();

if ($query && mysqli_num_rows($query) > 0) {
    while ($row = mysqli_fetch_assoc($query)) {
        $data = array();
        $data['id']                = $row["id"];
        $data['no_antrian']        = str_pad($row["no_antrian"], 3, "0", STR_PAD_LEFT);
        $data['nama_customer']     = isset($row["nama_customer"]) ? $row["nama_customer"] : "";
        $data['nama_driver']       = isset($row["nama_driver"]) ? $row["nama_driver"] : "";
        $data['plat_nomor']        = isset($row["plat_nomor"]) ? $row["plat_nomor"] : "";
        $data['status']            = isset($row["status"]) ? $row["status"] : "0";
        $data['keterangan_status'] = (isset($row["keterangan_status"]) && !empty($row["keterangan_status"])) ? $row["keterangan_status"] : "Segera Dilayani";
        
        // Ambil timestamp riil dari jam_input transaksi spesifik
        if (!empty($row["jam_input"])) {
            $data['timestamp'] = strtotime($tanggal . ' ' . $row["jam_input"]);
        } elseif (isset($row["updated_date"]) && !empty($row["updated_date"])) {
            $data['timestamp'] = strtotime($row["updated_date"]);
        } else {
            $data['timestamp'] = time();
        }
        
        array_push($response["data"], $data);
    }
} else {
    $data = array();
    $data['no_antrian'] = '-';
    array_push($response["data"], $data);
}

header('Content-Type: application/json');
echo json_encode($response);
exit();
?>