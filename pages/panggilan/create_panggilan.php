<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Credentials: true');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header('Access-Control-Allow-Methods: GET, POST');
header("Allow: GET, POST");

if (isset($_SERVER['REQUEST_METHOD']) && ($_SERVER['REQUEST_METHOD'] == 'POST' || $_SERVER['REQUEST_METHOD'] == 'GET')) {
    require_once "../../config/database.php";

    date_default_timezone_set("Asia/Jakarta");

    $antrian = isset($_POST['antrian']) ? mysqli_real_escape_string($mysqli, $_POST['antrian']) : '';
    $loket   = isset($_POST['loket']) ? mysqli_real_escape_string($mysqli, $_POST['loket']) : '';

    if (!empty($antrian) && !empty($loket)) {
        $query = mysqli_query($mysqli, "INSERT INTO queue_penggilan_antrian(antrian, loket) VALUES('$antrian', '$loket')") 
                 or die('Ada kesalahan pada query insert: ' . mysqli_error($mysqli));
        
        if ($query) {
            echo json_encode([
                'success' => true,
                'message' => 'Success create untuk panggilan ' . $antrian
            ]);
        }
    }
}
?>