<?php
date_default_timezone_set("Asia/Jakarta");

if (file_exists("../../config/database.php")) {
    include "../../config/database.php";
}

$conn = null;
if (isset($mysqli) && !$mysqli->connect_error) { $conn = $mysqli; }
elseif (isset($db) && !$db->connect_error) { $conn = $db; }
elseif (isset($koneksi) && !$koneksi->connect_error) { $conn = $koneksi; }

if (!$conn) {
    echo "Koneksi database gagal!";
    exit;
}

$action = isset($_POST['action']) ? $_POST['action'] : '';

// -------------------------------------------------------------------
// 1. PROSES TAMBAH & UBAH MANUAL (AJAX dari tambah_ubah.php)
// -------------------------------------------------------------------
if ($action == 'tambah' || $action == 'ubah') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $nama_customer = isset($_POST['nama_customer']) ? mysqli_real_escape_string($conn, trim($_POST['nama_customer'])) : '';

    if (empty($nama_customer)) {
        echo "Nama customer tidak boleh kosong!";
        exit;
    }

    if ($action == 'tambah') {
        // Cek apakah data nama customer sudah ada sebelumnya
        $cek = mysqli_query($conn, "SELECT id FROM master_customer WHERE LOWER(nama_customer) = LOWER('$nama_customer')");
        if (mysqli_num_rows($cek) > 0) {
            echo "Data customer '$nama_customer' sudah terdaftar di database!";
            exit;
        }

        $query = mysqli_query($conn, "INSERT INTO master_customer (nama_customer) VALUES ('$nama_customer')");
    } else {
        $query = mysqli_query($conn, "UPDATE master_customer SET nama_customer = '$nama_customer' WHERE id = $id");
    }

    if ($query) {
        echo "Sukses";
    } else {
        echo "Gagal menyimpan data ke database: " . mysqli_error($conn);
    }
    exit;
}

// -------------------------------------------------------------------
// 2. PROSES IMPORT FILE EXPLORER (CSV / TXT)
// -------------------------------------------------------------------
elseif ($action == 'import_file') {
    if (isset($_FILES['file_customer']['tmp_name']) && !empty($_FILES['file_customer']['tmp_name'])) {
        $file = $_FILES['file_customer']['tmp_name'];
        $handle = fopen($file, "r");

        $jumlah_baru = 0;
        $jumlah_dobel = 0;

        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                // Bersihkan karakter aneh / baris baru
                $nama = trim(str_replace(array("\r", "\n", '"'), '', $line));
                
                if (!empty($nama)) {
                    $nama_clean = mysqli_real_escape_string($conn, $nama);
                    
                    // Cek duplikasi data
                    $cek = mysqli_query($conn, "SELECT id FROM master_customer WHERE LOWER(nama_customer) = LOWER('$nama_clean')");
                    
                    if (mysqli_num_rows($cek) == 0) {
                        $ins = mysqli_query($conn, "INSERT INTO master_customer (nama_customer) VALUES ('$nama_clean')");
                        if ($ins) {
                            $jumlah_baru++;
                        }
                    } else {
                        $jumlah_dobel++;
                    }
                }
            }
            fclose($handle);

            echo "Sukses|" . $jumlah_baru . "|" . $jumlah_dobel;
        } else {
            echo "Gagal membaca file!";
        }
    } else {
        echo "Silakan pilih file terlebih dahulu!";
    }
    exit;
}

// -------------------------------------------------------------------
// 3. PROSES HAPUS DATA
// -------------------------------------------------------------------
elseif ($action == 'hapus') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

    if ($id > 0) {
        $query = mysqli_query($conn, "DELETE FROM master_customer WHERE id = $id");
        if ($query) {
            echo "Sukses";
        } else {
            echo "Gagal menghapus data: " . mysqli_error($conn);
        }
    } else {
        echo "ID tidak valid!";
    }
    exit;
}

// Default fallback
echo "Aksi tidak dikenali!";
exit;
?>