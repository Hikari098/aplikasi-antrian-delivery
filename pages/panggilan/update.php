<?php
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
    require_once "../../config/database.php";

    if (isset($_POST['id'])) {
        $id = (int)$_POST['id'];
        $status = "1";
        
        date_default_timezone_set("Asia/Jakarta");
        $updated_date = date("Y-m-d H:i:s");

        $update = mysqli_query($mysqli, "UPDATE queue_antrian_admisi SET status='$status', updated_date='$updated_date' WHERE id='$id'") 
                  or die('Ada kesalahan pada query update : ' . mysqli_error($mysqli));
    }
}
?>