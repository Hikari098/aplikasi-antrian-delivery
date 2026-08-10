<?php
$db_host        = "localhost";
$db_user        = "root";
$db_pass        = "";
$db_database    = "db_antrian"; // Kunci utamanya di sini, arahkan ke db_antrian!

$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_database);

if ($mysqli->connect_error) {
    die('Koneksi Database Gagal : ' . $mysqli->connect_error);
}

// Variabel cadangan agar aman dibaca oleh file lainnya
$conn = $mysqli;
$koneksi = $mysqli;
$db = $mysqli;