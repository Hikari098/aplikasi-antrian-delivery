<?php
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
    require_once "../../config/database.php";

    date_default_timezone_set("Asia/Jakarta");
    $tanggal = date("Y-m-d");
    $loket_aktif = isset($_GET['loket']) ? mysqli_real_escape_string($mysqli, $_GET['loket']) : '';

    // Filter ketat murni berdasarkan id_loket pendaftaran atau history pemanggilan
    $query_str = "SELECT a.* 
                  FROM queue_antrian_admisi a
                  LEFT JOIN queue_antrian_history h ON a.tanggal = h.tanggal AND a.no_antrian = h.no_antrian
                  WHERE a.tanggal = '$tanggal' 
                    AND a.status IN ('0', '1')
                    AND (a.id_loket = '$loket_aktif' OR h.id_loket = '$loket_aktif')
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
            
            $waktu_str = "";
            if (!empty($row["created_at"])) {
                $waktu_str = $row["created_at"];
            } elseif (!empty($row["updated_date"])) {
                $waktu_str = $row["updated_date"];
            }

            $parsed_time = (!empty($waktu_str)) ? strtotime($waktu_str) : 0;
            $data['timestamp'] = ($parsed_time !== false && $parsed_time > 0) ? $parsed_time : 0;

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