<?php
require_once "../../config/database.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = isset($_POST['id']) ? mysqli_real_escape_string($mysqli, $_POST['id']) : '';

    if (!empty($id)) {
        $query = "UPDATE queue_antrian_admisi SET status = '1', updated_date = NOW() WHERE id = '$id'";
        mysqli_query($mysqli, $query);
    }
}
?>