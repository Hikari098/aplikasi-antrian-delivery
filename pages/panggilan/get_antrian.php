<?php
require_once "../../config/database.php";

date_default_timezone_set("Asia/Jakarta");
$tanggal = date("Y-m-d");

$loket_aktif = isset($_GET['loket']) ? mysqli_real_escape_string($mysqli, $_GET['loket']) : '';

// Filter: Hanya tampilkan data antrian hari ini yang statusnya BELUM SELESAI (status != '2')
if (!empty($loket_aktif)) {
    $query_str = "SELECT * FROM queue_antrian_admisi 
                  WHERE tanggal = '$tanggal' 
                    AND (status = '0' OR status = '1')
                    AND (id_loket = '$loket_aktif' OR id_loket IS NULL OR id_loket = '') 
                  ORDER BY id ASC";
} else {
    $query_str = "SELECT * FROM queue_antrian_admisi 
                  WHERE tanggal = '$tanggal' 
                    AND (status = '0' OR status = '1')
                  ORDER BY id ASC";
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
        $data['timestamp']         = isset($row["updated_date"]) ? strtotime($row["updated_date"]) : 0;
        
        array_push($response["data"], $data);
    }
}

header('Content-Type: application/json');
echo json_encode($response);
exit();
?>