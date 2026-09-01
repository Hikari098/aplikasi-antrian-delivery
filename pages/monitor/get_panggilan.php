<?php
require_once "../../config/database.php";

$loket_aktif = isset($_GET['loket']) ? mysqli_real_escape_string($mysqli, $_GET['loket']) : '';

$response = array("success" => false, "data" => array());

if (!empty($loket_aktif)) {
    $query_str = "SELECT * FROM queue_penggilan_antrian WHERE loket = '$loket_aktif' ORDER BY id ASC";
} else {
    $query_str = "SELECT * FROM queue_penggilan_antrian ORDER BY id ASC";
}

$query = mysqli_query($mysqli, $query_str);

if ($query && mysqli_num_rows($query) > 0) {
    $rows = array();
    while ($row = mysqli_fetch_assoc($query)) {
        $rows[] = $row;
    }

    // Jika antrean menumpuk lebih dari 1 (karena diklik berkali-kali),
    // Hapus panggilan lama yang tertunda dan sisakan panggilan TERBARU saja
    if (count($rows) > 1) {
        $last_item = array_pop($rows); // Ambil item paling baru
        
        // Clean up antrean sampah lama
        foreach ($rows as $old_item) {
            $old_id = $old_item['id'];
            mysqli_query($mysqli, "DELETE FROM queue_penggilan_antrian WHERE id = '$old_id'");
        }
        
        $response["data"][] = $last_item;
    } else {
        $response["data"][] = $rows[0];
    }

    $response["success"] = true;
}

header('Content-Type: application/json');
echo json_encode($response);
exit();
?>