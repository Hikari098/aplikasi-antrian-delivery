<?php
require_once "../../config/database.php";

date_default_timezone_set("Asia/Jakarta");
$tanggal     = date("Y-m-d");
$jam_selesai = date("H:i:s");

$id = isset($_POST['id']) ? mysqli_real_escape_string($mysqli, $_POST['id']) : '';

if (!empty($id)) {
    // 1. Ambil detail antrian aktif yang akan diselesaikan
    $get_data = mysqli_query($mysqli, "SELECT * FROM queue_antrian_admisi WHERE id = '$id'");
    if ($get_data && mysqli_num_rows($get_data) > 0) {
        $row = mysqli_fetch_assoc($get_data);
        $no_antrian = $row['no_antrian'];
        $id_loket   = $row['id_loket'];
        $tgl_antrian= $row['tanggal'];

        // 2. Ubah status di antrian aktif menjadi '2' (Selesai)
        mysqli_query($mysqli, "UPDATE queue_antrian_admisi SET status = '2', updated_date = NOW() WHERE id = '$id'");

        // 3. Update jam_selesai pada baris history pendaftaran yang SUDAH ADA (Berdasarkan ID transaksi history paling akhir)
        $q_hist = mysqli_query($mysqli, "SELECT id FROM queue_antrian_history 
                                        WHERE tanggal = '$tgl_antrian' 
                                          AND no_antrian = '$no_antrian' 
                                          AND id_loket = '$id_loket' 
                                        ORDER BY id DESC LIMIT 1");
        
        if ($q_hist && mysqli_num_rows($q_hist) > 0) {
            $data_hist = mysqli_fetch_assoc($q_hist);
            $id_history = $data_hist['id'];
            
            // UPDATE murni baris yang sudah ada
            mysqli_query($mysqli, "UPDATE queue_antrian_history SET jam_selesai = '$jam_selesai' WHERE id = '$id_history'");
        } else {
            // Fallback jika tidak ditemukan baris history pendaftaran
            mysqli_query($mysqli, "INSERT INTO queue_antrian_history (tanggal, jam_input, jam_selesai, no_antrian, nama_customer, nama_driver, plat_nomor, id_loket) 
                                   VALUES ('$tgl_antrian', NOW(), '$jam_selesai', '$no_antrian', '{$row['nama_customer']}', '{$row['nama_driver']}', '{$row['plat_nomor']}', '$id_loket')");
        }

        echo json_encode(array("status" => "success"));
        exit();
    }
}

echo json_encode(array("status" => "error", "message" => "ID tidak ditemukan"));
exit();
?>