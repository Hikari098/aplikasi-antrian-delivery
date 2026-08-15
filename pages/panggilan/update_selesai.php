<?php
if (isset($_POST['id'])) {
    require_once "../../config/database.php";

    $id = mysqli_real_escape_string($mysqli, $_POST['id']);

    // Mengubah status antrian menjadi 2 (Selesai Dilayani)
    $query = mysqli_query($mysqli, "UPDATE queue_antrian_admisi SET status = '2' WHERE id = '$id'")
             or die('Ada kesalahan query update selesai: ' . mysqli_error($mysqli));

    if ($query) {
        header('Content-Type: application/json');
        echo json_encode(array('status' => 'success'));
    }
}
?>