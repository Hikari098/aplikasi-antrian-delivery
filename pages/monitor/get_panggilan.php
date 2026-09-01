<?php
require_once "../../config/database.php";

date_default_timezone_set("Asia/Jakarta");

$loket_aktif = isset($_REQUEST['loket']) ? mysqli_real_escape_string($mysqli, $_REQUEST['loket']) : '';

if (!empty($loket_aktif)) {
    $query_str = "SELECT * FROM queue_penggilan_antrian 
                  WHERE loket = '$loket_aktif' OR loket IS NULL OR loket = '' 
                  ORDER BY id ASC LIMIT 1";
} else {
    $query_str = "SELECT * FROM queue_penggilan_antrian ORDER BY id ASC LIMIT 1";
}

$query = mysqli_query($mysqli, $query_str);
$response["success"] = false;
$response["data"] = array();

if ($query && mysqli_num_rows($query) > 0) {
    $response["success"] = true;
    while ($row = mysqli_fetch_assoc($query)) {
        $data = array();
        $data['id']          = $row["id"];
        $data['antrian']     = $row["antrian"];
        $data['loket']       = $row["loket"];
        $data['nama_driver'] = isset($row["nama_driver"]) ? $row["nama_driver"] : "";
        array_push($response["data"], $data);
    }
}

header('Content-Type: application/json');
echo json_encode($response);
?>