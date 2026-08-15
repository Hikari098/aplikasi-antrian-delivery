<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Credentials: true');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header('Access-Control-Allow-Methods: GET, POST');
header("Allow: GET, POST");

if (isset($_SERVER['REQUEST_METHOD']) && ($_SERVER['REQUEST_METHOD'] == 'POST' || $_SERVER['REQUEST_METHOD'] == 'GET')) {
    require_once "../../config/database.php";

    date_default_timezone_set("Asia/Jakarta");
    $tanggal = date("Y-m-d");

    $loket_aktif = isset($_REQUEST['loket']) ? mysqli_real_escape_string($mysqli, $_REQUEST['loket']) : '';

    $query_str = "SELECT p.id, p.antrian, p.loket, a.nama_customer, a.nama_driver, a.plat_nomor, a.created_at
                  FROM queue_penggilan_antrian p
                  LEFT JOIN queue_antrian_admisi a ON p.antrian = a.no_antrian AND a.tanggal = '$tanggal'
                  WHERE (UPPER(p.loket) = UPPER('$loket_aktif') OR p.loket = '$loket_aktif')
                  ORDER BY p.id ASC";

    $query = mysqli_query($mysqli, $query_str) or die('Ada kesalahan pada query monitor: ' . mysqli_error($mysqli));
    
    $dataAntrian = array();

    while ($row = mysqli_fetch_assoc($query)) {
        $dataAntrian[] = array(
            'id'            => $row['id'],
            'antrian'       => $row['antrian'],
            'loket'         => $row['loket'],
            'nama_customer' => isset($row['nama_customer']) ? $row['nama_customer'] : '-',
            'nama_driver'   => isset($row['nama_driver']) ? $row['nama_driver'] : '-',
            'plat_nomor'    => isset($row['plat_nomor']) ? $row['plat_nomor'] : '-',
            'created_at'    => isset($row['created_at']) ? $row['created_at'] : date("Y-m-d H:i:s")
        );
    }

    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'message' => 'Success',
        'data'    => $dataAntrian
    ]);
}
?>