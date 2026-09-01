<?php
require_once "../../config/database.php";

date_default_timezone_set("Asia/Jakarta");
$tanggal = date("Y-m-d");

// Hitung murni total antrian unik terdaftar di admisi hari ini
$query = mysqli_query($mysqli, "SELECT COUNT(*) as total FROM queue_antrian_admisi WHERE tanggal = '$tanggal'");
$data = mysqli_fetch_assoc($query);

echo ($data) ? $data['total'] : 0;
?>