<?php
date_default_timezone_set("Asia/Jakarta");
if (file_exists("../../config/database.php")) {
    include "../../config/database.php";
}
$conn = null;
if (isset($mysqli) && !$mysqli->connect_error) { $conn = $mysqli; }
elseif (isset($db) && !$db->connect_error) { $conn = $db; }
elseif (isset($koneksi) && !$koneksi->connect_error) { $conn = $koneksi; }

if (!$conn) { echo "Koneksi database gagal!"; exit; }

$action = isset($_POST['action']) ? $_POST['action'] : '';

if ($action == 'tambah' || $action == 'ubah') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $nama_driver = isset($_POST['nama_driver']) ? mysqli_real_escape_string($conn, trim($_POST['nama_driver'])) : '';

    if (empty($nama_driver)) { echo "Nama driver tidak boleh kosong!"; exit; }

    if ($action == 'tambah') {
        $cek = mysqli_query($conn, "SELECT id FROM master_driver WHERE LOWER(nama_driver) = LOWER('$nama_driver')");
        if (mysqli_num_rows($cek) > 0) { echo "Data driver '$nama_driver' sudah terdaftar!"; exit; }
        $query = mysqli_query($conn, "INSERT INTO master_driver (nama_driver) VALUES ('$nama_driver')");
    } else {
        $query = mysqli_query($conn, "UPDATE master_driver SET nama_driver = '$nama_driver' WHERE id = $id");
    }
    echo $query ? "Sukses" : "Gagal menyimpan data: " . mysqli_error($conn);
    exit;
}
elseif ($action == 'import_file') {
    if (isset($_FILES['file_driver']['tmp_name']) && !empty($_FILES['file_driver']['tmp_name'])) {
        $file = $_FILES['file_driver']['tmp_name'];
        $handle = fopen($file, "r");
        $jumlah_baru = 0; $jumlah_dobel = 0;

        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                $nama = trim(str_replace(array("\r", "\n", '"'), '', $line));
                if (!empty($nama)) {
                    $nama_clean = mysqli_real_escape_string($conn, $nama);
                    $cek = mysqli_query($conn, "SELECT id FROM master_driver WHERE LOWER(nama_driver) = LOWER('$nama_clean')");
                    if (mysqli_num_rows($cek) == 0) {
                        $ins = mysqli_query($conn, "INSERT INTO master_driver (nama_driver) VALUES ('$nama_clean')");
                        if ($ins) { $jumlah_baru++; }
                    } else { $jumlah_dobel++; }
                }
            }
            fclose($handle);
            echo "Sukses|" . $jumlah_baru . "|" . $jumlah_dobel;
        } else { echo "Gagal membaca file!"; }
    } else { echo "Silakan pilih file terlebih dahulu!"; }
    exit;
}
elseif ($action == 'hapus') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    if ($id > 0) {
        $query = mysqli_query($conn, "DELETE FROM master_driver WHERE id = $id");
        echo $query ? "Sukses" : "Gagal menghapus data: " . mysqli_error($conn);
    } else { echo "ID tidak valid!"; }
    exit;
}
echo "Aksi tidak dikenali!";
exit;
?>