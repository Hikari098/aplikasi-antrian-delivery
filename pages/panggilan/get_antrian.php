<?php
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
    require_once "../../config/database.php";

    date_default_timezone_set("Asia/Jakarta");
    $tanggal = date("Y-m-d");
    $loket_aktif = isset($_GET['loket']) ? mysqli_real_escape_string($mysqli, $_GET['loket']) : '';

    $query_str = "SELECT a.id, a.no_antrian, a.status 
                  FROM queue_antrian_admisi a
                  INNER JOIN queue_antrian_history h ON a.tanggal = h.tanggal AND a.no_antrian = h.no_antrian
                  WHERE a.tanggal = '$tanggal' AND h.id_loket = '$loket_aktif'";

    $query = mysqli_query($mysqli, $query_str) or die('Ada kesalahan pada query tampil data : ' . mysqli_error($mysqli));
    $rows = mysqli_num_rows($query);

    $response["data"] = array();

    if ($rows > 0) {
        while ($row = mysqli_fetch_assoc($query)) {
            $data = array();
            $data['id']         = $row["id"];
            $data['no_antrian'] = str_pad($row["no_antrian"], 3, "0", STR_PAD_LEFT);
            $data['status']     = $row["status"];
            array_push($response["data"], $data);
        }
    } else {
        $data = array();
        $data['id']         = "";
        $data['no_antrian'] = "-";
        $data['status']     = "";
        array_push($response["data"], $data);
    }

    echo json_encode($response);
}
?>