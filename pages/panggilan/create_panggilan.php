<?php
require_once "../../config/database.php";

date_default_timezone_set("Asia/Jakarta");

$antrian     = isset($_POST['antrian'])     ? mysqli_real_escape_string($mysqli, $_POST['antrian'])     : '';
$loket       = isset($_POST['loket'])       ? mysqli_real_escape_string($mysqli, $_POST['loket'])       : '';
$nama_driver = isset($_POST['nama_driver']) ? mysqli_real_escape_string($mysqli, $_POST['nama_driver']) : '';

header('Content-Type: application/json');

if (!empty($antrian)) {
    // Simpan nomor antrian, loket, dan nama driver ke tabel pemanggilan
    $query_str = "INSERT INTO queue_penggilan_antrian (antrian, loket, nama_driver) VALUES ('$antrian', '$loket', '$nama_driver')";
    $query = mysqli_query($mysqli, $query_str);

    if ($query) {
        echo json_encode(array("success" => true, "message" => "Berhasil dikirim ke pemutar suara"));
    } else {
        echo json_encode(array("success" => false, "error" => mysqli_error($mysqli)));
    }
} else {
    echo json_encode(array("success" => false, "message" => "Nomor antrian kosong"));
}
?>