<?php
session_start();
$username = "superadmin";

// Menggunakan password terupdate milikmu
$password = "superadmin@123";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usernameInput = isset($_POST['username']) ? trim($_POST['username']) : '';
    $passwordInput = isset($_POST['password']) ? trim($_POST['password']) : '';

    // Validasi input kosong
    if (empty($usernameInput) || empty($passwordInput)) {
        header("Location: index.php?error=" . urlencode("Username dan password tidak boleh kosong!"));
        exit;
    }

    // Validasi kecocokan username
    if ($usernameInput !== $username) {
        header("Location: index.php?error=" . urlencode("Username yang Anda masukkan salah!"));
        exit;
    }

    // Validasi kecocokan password
    if ($passwordInput !== $password) {
        header("Location: index.php?error=" . urlencode("Password yang Anda masukkan salah!"));
        exit;
    }

    // Jika semua validasi lolos, buat session sukses
    $_SESSION['username'] = $username;
    header("Location: index.php");
    exit;
} else {
    // Jika ada yang mencoba tembak langsung url login.php tanpa POST, kembalikan ke index
    header("Location: index.php");
    exit;
}
?>