<?php
require_once "../../config/database.php";

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id         = isset($_POST['id']) ? mysqli_real_escape_string($mysqli, $_POST['id']) : '';
    $keterangan = isset($_POST['keterangan']) ? mysqli_real_escape_string($mysqli, $_POST['keterangan']) : 'Segera Dilayani';

    if (!empty($id)) {
        $query = "UPDATE queue_antrian_admisi SET keterangan_status = '$keterangan' WHERE id = '$id'";
        if (mysqli_query($mysqli, $query)) {
            echo json_encode(array("success" => true, "message" => "Status keterangan berhasil diperbarui"));
        } else {
            echo json_encode(array("success" => false, "error" => mysqli_error($mysqli)));
        }
    } else {
        echo json_encode(array("success" => false, "message" => "ID antrian tidak ditemukan"));
    }
} else {
    echo json_encode(array("success" => false, "message" => "Invalid Request Method"));
}
?>