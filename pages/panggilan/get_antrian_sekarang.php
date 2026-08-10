<?php
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
    require_once "../../config/database.php";

    date_default_timezone_set("Asia/Jakarta");
    $tanggal = date("Y-m-d");
    $loket_aktif = isset($_GET['loket']) ? mysqli_real_escape_string($mysqli, $_GET['loket']) : '';

    $query = mysqli_query($mysqli, "SELECT a.no_antrian FROM queue_antrian_admisi a 
                                    INNER JOIN queue_antrian_history h ON a.tanggal = h.tanggal AND a.no_antrian = h.no_antrian
                                    WHERE a.tanggal='$tanggal' AND a.status='1' AND h.id_loket='$loket_aktif' 
                                    ORDER BY a.no_antrian DESC LIMIT 1") or die(mysqli_error($mysqli));
    
    $data = mysqli_fetch_assoc($query);
    if ($data) {
        echo str_pad((int)$data['no_antrian'], 3, "0", STR_PAD_LEFT);
    } else {
        echo "-";
    }
}
?>