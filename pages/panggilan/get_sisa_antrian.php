<?php
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
    require_once "../../config/database.php";

    date_default_timezone_set("Asia/Jakarta");
    $tanggal = date("Y-m-d");
    $loket_aktif = isset($_GET['loket']) ? mysqli_real_escape_string($mysqli, $_GET['loket']) : '';

    $query = mysqli_query($mysqli, "SELECT COUNT(a.id) as sisa FROM queue_antrian_admisi a 
                                    INNER JOIN queue_antrian_history h ON a.tanggal = h.tanggal AND a.no_antrian = h.no_antrian
                                    WHERE a.tanggal='$tanggal' AND a.status='0' AND h.id_loket='$loket_aktif'") or die(mysqli_error($mysqli));
    
    $data = mysqli_fetch_assoc($query);
    $sisa = ($data) ? (int)$data['sisa'] : 0;

    echo number_format($sisa, 0, '', '');
}
?>