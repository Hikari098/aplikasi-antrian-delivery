<?php
if (file_exists("../../config/database.php")) {
    include_once "../../config/database.php";
}

$conn = null;
if (isset($mysqli) && !$mysqli->connect_error) { $conn = $mysqli; }
elseif (isset($db) && !$db->connect_error) { $conn = $db; }
elseif (isset($koneksi) && !$koneksi->connect_error) { $conn = $koneksi; }

if (!$conn) {
    echo "Gagal koneksi database!";
    exit;
}

$action = isset($_POST['action']) ? $_POST['action'] : '';

// ==========================================
// AKSI 1: TAMBAH MANUAL (DENGAN ANTI-DUPLIKAT)
// ==========================================
if ($action === 'tambah') {
    $nama_customer = isset($_POST['nama_customer']) ? mysqli_real_escape_string($conn, trim($_POST['nama_customer'])) : '';
    
    if (empty($nama_customer)) { 
        echo "Nama customer tidak boleh kosong!"; 
        exit; 
    }

    // Cek apakah nama perusahaan sudah terdaftar (abaikan huruf besar/kecil)
    $cek = mysqli_query($conn, "SELECT id FROM master_customer WHERE LOWER(nama_customer) = LOWER('$nama_customer')");
    
    if (mysqli_num_rows($cek) > 0) {
        echo "Nama customer/perusahaan tersebut sudah terdaftar di database master!";
        exit;
    }

    $insert = mysqli_query($conn, "INSERT INTO master_customer (nama_customer) VALUES ('$nama_customer')");
    echo ($insert) ? "Sukses" : "Gagal: " . mysqli_error($conn);
    exit;
}

// ==========================================
// AKSI 2: UBAH DATA (DENGAN ANTI-DUPLIKAT PINTAR)
// ==========================================
if ($action === 'ubah') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $nama_customer = isset($_POST['nama_customer']) ? mysqli_real_escape_string($conn, trim($_POST['nama_customer'])) : '';
    
    if ($id > 0 && !empty($nama_customer)) {
        
        // Cek duplikat: Apakah nama baru ini sudah dipakai oleh ID LAIN?
        $cek = mysqli_query($conn, "SELECT id FROM master_customer WHERE LOWER(nama_customer) = LOWER('$nama_customer') AND id != $id");
        
        if (mysqli_num_rows($cek) > 0) {
            echo "Nama customer/perusahaan tersebut sudah terdaftar di database master!";
            exit;
        }

        // Jika aman dari duplikat id lain, lakukan update
        $update = mysqli_query($conn, "UPDATE master_customer SET nama_customer = '$nama_customer' WHERE id = $id");
        echo ($update) ? "Sukses" : "Gagal: " . mysqli_error($conn);
    } else {
        echo "Data tidak valid!";
    }
    exit;
}

// ==========================================
// AKSI 3: HAPUS DATA
// ==========================================
if ($action === 'hapus') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    if ($id > 0) {
        $delete = mysqli_query($conn, "DELETE FROM master_customer WHERE id = $id");
        echo ($delete) ? "Sukses" : "Gagal: " . mysqli_error($conn);
    }
    exit;
}

// ==========================================
// AKSI 4: IMPORT FILE EXPLORER VIA AJAX
// ==========================================
if ($action === 'import_file') {
    if (isset($_FILES['file_customer']) && $_FILES['file_customer']['error'] == 0) {
        $file_tmp = $_FILES['file_customer']['tmp_name'];
        
        // Membaca baris file dan mengabaikan baris kosong
        $lines = file($file_tmp, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        if ($lines) {
            $data_baru = 0;
            $data_dobel = 0;

            foreach ($lines as $line) {
                $nama_perusahaan = mysqli_real_escape_string($conn, trim($line));
                
                if (!empty($nama_perusahaan)) {
                    // Cek apakah nama perusahaan sudah ada di database master
                    $cek = mysqli_query($conn, "SELECT id FROM master_customer WHERE nama_customer = '$nama_perusahaan'");
                    
                    if (mysqli_num_rows($cek) == 0) {
                        mysqli_query($conn, "INSERT INTO master_customer (nama_customer) VALUES ('$nama_perusahaan')");
                        $data_baru++;
                    } else {
                        $data_dobel++;
                    }
                }
            }
            // Kirim respons teks khusus ke AJAX
            echo "Sukses|{$data_baru}|{$data_dobel}";
        } else {
            echo "File kosong atau tidak terbaca!";
        }
    } else {
        echo "Gagal mengunggah file!";
    }
    exit;
}
?>