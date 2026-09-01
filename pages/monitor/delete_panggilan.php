<?php
require_once "../../config/database.php";

header('Content-Type: application/json');

if (isset($_REQUEST['id'])) {
    $id = mysqli_real_escape_string($mysqli, $_REQUEST['id']);
    $query = mysqli_query($mysqli, "DELETE FROM queue_penggilan_antrian WHERE id = '$id'");
    
    if ($query) {
        echo json_encode(array("success" => true));
    } else {
        echo json_encode(array("success" => false, "error" => mysqli_error($mysqli)));
    }
} else {
    echo json_encode(array("success" => false, "message" => "ID tidak ditemukan"));
}
?>