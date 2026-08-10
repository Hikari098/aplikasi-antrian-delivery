<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Credentials: true');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header('Access-Control-Allow-Methods: GET, POST');
header("Allow: GET, POST");

if (isset($_SERVER['REQUEST_METHOD']) && ($_SERVER['REQUEST_METHOD'] == 'POST' || $_SERVER['REQUEST_METHOD'] == 'GET')) {
    require_once "../../config/database.php";

    $id = isset($_POST['id']) ? mysqli_real_escape_string($mysqli, $_POST['id']) : '';

    mysqli_query($mysqli, "DELETE FROM queue_penggilan_antrian WHERE id='$id'") or die('Ada kesalahan pada query delete data : ' . mysqli_error($mysqli));
    $deleted = mysqli_affected_rows($mysqli);

    if($deleted) {
        echo json_encode([
            'success' => true,
            'message' => 'Delete Success on id ' . $id
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Error'
        ]);
    }
}
?>