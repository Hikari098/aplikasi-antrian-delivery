<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Credentials: true');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header('Access-Control-Allow-Methods: GET, POST');
header("Allow: GET, POST");

if (isset($_SERVER['REQUEST_METHOD']) && ($_SERVER['REQUEST_METHOD'] == 'POST' || $_SERVER['REQUEST_METHOD'] == 'GET')) {
    require_once "../../config/database.php";

    // Tangkap parameter nama loket string dari POST AJAX monitor TV
    $loket_aktif = isset($_POST['loket']) ? mysqli_real_escape_string($mysqli, $_POST['loket']) : '';

    // Cari antrian panggilan yang murni ditujukan untuk nama teks loket monitor ini
    $query = mysqli_query($mysqli, "SELECT id, antrian, loket FROM queue_penggilan_antrian WHERE UPPER(loket) = UPPER('$loket_aktif') ORDER BY id ASC") 
             or die('Ada kesalahan pada query : ' . mysqli_error($mysqli));
    
    $dataAntrian = array();

    while ($row = mysqli_fetch_assoc($query)) {
        $dataAntrian[] = array(
            'id' => $row['id'],
            'antrian' => $row['antrian'],
            'loket' => $row['loket'],
            'plat_nomor' => '' // Bypass pencegah crash JavaScript
        );
    }

    echo json_encode([
        'success' => true,
        'message' => 'Success',
        'data' => $dataAntrian
    ]);
}
?>