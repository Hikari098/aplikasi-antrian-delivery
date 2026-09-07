<?php
require_once "../../config/database.php";

date_default_timezone_set("Asia/Jakarta");
$tanggal     = date("Y-m-d");
$jam_selesai = date("H:i:s");

$id = isset($_POST['id']) ? mysqli_real_escape_string($mysqli, $_POST['id']) : '';

if (!empty($id)) {
    // 1. Ambil detail antrian dari tabel admisi
    $get_data = mysqli_query($mysqli, "SELECT * FROM queue_antrian_admisi WHERE id = '$id'");
    if ($get_data && mysqli_num_rows($get_data) > 0) {
        $row = mysqli_fetch_assoc($get_data);
        $no_antrian  = $row['no_antrian'];
        $id_loket    = $row['id_loket'];
        $tgl_antrian = $row['tanggal'];

        // 2. Ubah status di admisi menjadi '2' (Selesai, otomatis hilang dari layar panggilan)
        mysqli_query($mysqli, "UPDATE queue_antrian_admisi SET status = '2', updated_date = NOW() WHERE id = '$id'");

        // 3. Cari ID history pendaftaran transaksi tersebut yang paling pas (transaksi terakhir)
        $q_hist = mysqli_query($mysqli, "SELECT id FROM queue_antrian_history 
                                        WHERE tanggal = '$tgl_antrian' 
                                          AND no_antrian = '$no_antrian' 
                                          AND id_loket = '$id_loket' 
                                        ORDER BY id DESC LIMIT 1");
        
        if ($q_hist && mysqli_num_rows($q_hist) > 0) {
            $data_hist = mysqli_fetch_assoc($q_hist);
            $id_history = $data_hist['id'];
            
            // Update jam_selesai langsung pada baris history yang valid
            mysqli_query($mysqli, "UPDATE queue_antrian_history SET jam_selesai = '$jam_selesai' WHERE id = '$id_history'");
        }

        echo json_encode(array("status" => "success"));
        exit();
    }
}

echo json_encode(array("status" => "error", "message" => "ID tidak ditemukan"));
exit();
?>