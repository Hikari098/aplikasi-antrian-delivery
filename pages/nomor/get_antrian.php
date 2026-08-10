<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Credentials: true');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header('Access-Control-Allow-Methods: GET, POST');
header("Allow: GET, POST");

if (isset($_SERVER['REQUEST_METHOD']) && ($_SERVER['REQUEST_METHOD'] == 'POST' || $_SERVER['REQUEST_METHOD'] == 'GET')) {
    
    if (file_exists("../../config/database.php")) {
        @include_once "../../config/database.php";
    }

    $conn = null;
    if (isset($mysqli) && $mysqli instanceof mysqli) { $conn = $mysqli; }
    elseif (isset($db) && $db instanceof mysqli) { $conn = $db; }
    elseif (isset($koneksi) && $koneksi instanceof mysqli) { $conn = $koneksi; }

    date_default_timezone_set("Asia/Jakarta");
    $tanggal = date("Y-m-d");

    $nomor_berikutnya = 1;

    try {
        if ($conn && !$conn->connect_error) {
            $query = @mysqli_query($conn, "SELECT max(no_antrian) as nomor FROM queue_antrian_admisi WHERE tanggal='$tanggal'");
            
            if ($query) {
                $data = mysqli_fetch_assoc($query);
                if ($data['nomor'] != null) {
                    $nomor_berikutnya = (int)$data['nomor'] + 1;
                }
            }
        }
    } catch (\Throwable $e) {
        $nomor_berikutnya = 1;
    }

    echo sprintf("%03d", $nomor_berikutnya);
}
?>