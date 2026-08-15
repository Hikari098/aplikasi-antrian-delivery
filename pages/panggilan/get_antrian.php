<?php
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
    require_once "../../config/database.php";

    date_default_timezone_set("Asia/Jakarta");
    $tanggal = date("Y-m-d");
    $loket_aktif = isset($_GET['loket']) ? mysqli_real_escape_string($mysqli, $_GET['loket']) : '';

    $query_str = "SELECT a.* 
                  FROM queue_antrian_admisi a
                  LEFT JOIN queue_antrian_history h ON a.tanggal = h.tanggal AND a.no_antrian = h.no_antrian
                  WHERE a.tanggal = '$tanggal' 
                    AND (h.id_loket = '$loket_aktif' OR a.status = '0')
                    AND a.status IN ('0', '1')
                  GROUP BY a.id
                  ORDER BY a.id ASC";

    $query = mysqli_query($mysqli, $query_str);
    $response["data"] = array();

    if ($query && mysqli_num_rows($query) > 0) {
        while ($row = mysqli_fetch_assoc($query)) {
            $data = array();
            $data['id']         = $row["id"];
            $data['no_antrian'] = str_pad($row["no_antrian"], 3, "0", STR_PAD_LEFT);
            $data['status']     = (string)$row["status"];
            
            $data['nama_customer'] = !empty($row["nama_customer"]) ? $row["nama_customer"] : "-";
            $data['nama_driver']   = !empty($row["nama_driver"]) ? $row["nama_driver"] : "-";
            $data['plat_nomor']    = !empty($row["plat_nomor"]) ? $row["plat_nomor"] : "-";
            
            // Pengambilan jam pendaftaran asli dari database
            $waktu_str = "";
            if (!empty($row["created_at"])) {
                $waktu_str = $row["created_at"];
            } elseif (!empty($row["waktu"])) {
                $waktu_str = (strlen($row["waktu"]) <= 8) ? $row["tanggal"] . " " . $row["waktu"] : $row["waktu"];
            } elseif (!empty($row["jam"])) {
                $waktu_str = (strlen($row["jam"]) <= 8) ? $row["tanggal"] . " " . $row["jam"] : $row["jam"];
            }

            // Bernilai 0 jika jam di DB kosong, agar waktu dikunci secara individual oleh JS
            $data['timestamp'] = (!empty($waktu_str) && strtotime($waktu_str) !== false) ? strtotime($waktu_str) : 0;

            array_push($response["data"], $data);
        }
    } else {
        $data = array();
        $data['id']            = "";
        $data['no_antrian']    = "-";
        $data['status']        = "";
        $data['nama_customer'] = "-";
        $data['nama_driver']   = "-";
        $data['plat_nomor']    = "-";
        $data['timestamp']     = 0;
        array_push($response["data"], $data);
    }

    header('Content-Type: application/json');
    echo json_encode($response);
}
?>