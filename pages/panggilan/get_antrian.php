<?php
require_once "../../config/database.php";

date_default_timezone_set("Asia/Jakarta");
$tanggal = date("Y-m-d");

$loket_aktif = isset($_GET['loket']) ? mysqli_real_escape_string($mysqli, $_GET['loket']) : '';

// Query aman tanpa pemanggilan kolom created_date yang tidak ada di database
if (!empty($loket_aktif)) {
    $query_str = "SELECT 
                    a.id,
                    a.no_antrian,
                    a.nama_customer,
                    a.nama_driver,
                    a.plat_nomor,
                    a.status,
                    a.keterangan_status,
                    a.updated_date,
                    MAX(h.jam_input) AS jam_input
                  FROM queue_antrian_admisi a
                  LEFT JOIN queue_antrian_history h 
                    ON a.no_antrian = h.no_antrian 
                   AND a.tanggal = h.tanggal 
                   AND a.id_loket = h.id_loket
                  WHERE a.tanggal = '$tanggal' 
                    AND a.status IN ('0', '1')
                    AND a.id_loket = '$loket_aktif' 
                  GROUP BY a.id
                  ORDER BY a.id ASC";
} else {
    $query_str = "SELECT 
                    a.id,
                    a.no_antrian,
                    a.nama_customer,
                    a.nama_driver,
                    a.plat_nomor,
                    a.status,
                    a.keterangan_status,
                    a.updated_date,
                    MAX(h.jam_input) AS jam_input
                  FROM queue_antrian_admisi a
                  LEFT JOIN queue_antrian_history h 
                    ON a.no_antrian = h.no_antrian 
                   AND a.tanggal = h.tanggal 
                   AND a.id_loket = h.id_loket
                  WHERE a.tanggal = '$tanggal' 
                    AND a.status IN ('0', '1')
                  GROUP BY a.id
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
        
        // Membaca timestamp countdown riil dari jam_input pendaftaran history
        if (!empty($row["jam_input"]) && $row["jam_input"] != '00:00:00') {
            $data['timestamp'] = strtotime($tanggal . ' ' . $row["jam_input"]);
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