<?php
require_once "../../config/database.php";

date_default_timezone_set("Asia/Jakarta");
$tanggal = date("Y-m-d");
$jam_sekarang = date("H:i:s");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = isset($_POST['id']) ? mysqli_real_escape_string($mysqli, $_POST['id']) : '';

    if (!empty($id)) {
        // 1. Ambil data no_antrian
        $q_admisi = mysqli_query($mysqli, "SELECT no_antrian FROM queue_antrian_admisi WHERE id = '$id' LIMIT 1");
        $d_admisi = mysqli_fetch_assoc($q_admisi);

        if ($d_admisi) {
            $no_antrian = $d_admisi['no_antrian'];

            // 2. Update status admisi menjadi selesai (status = 2)
            mysqli_query($mysqli, "UPDATE queue_antrian_admisi SET status = '2', updated_date = NOW() WHERE id = '$id'");

            // 3. Catat jam_selesai ke queue_antrian_history
            mysqli_query($mysqli, "UPDATE queue_antrian_history 
                                   SET jam_selesai = '$jam_sekarang' 
                                   WHERE no_antrian = '$no_antrian' AND tanggal = '$tanggal'");
        }

        echo json_encode(array("success" => true));
    }
}
?>