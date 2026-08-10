# 🚚 Aplikasi Antrian Delivery

**Aplikasi Antrian Delivery** adalah sistem manajemen antrian berbasis web yang dirancang untuk mengelola alur pendaftaran, panggilan, hingga pemantauan driver/supplier secara efektif dan *real-time*. Sistem ini membantu meningkatkan efisiensi kinerja operasional dalam melayani pengunjung maupun driver di lingkungan instansi atau perusahaan.

---

## 🛠️ Teknologi yang Digunakan

Aplikasi ini dibangun menggunakan teknologi dan pustaka berikut:

- **Bahasa Pemrograman:** PHP 7.4 / PHP 8.x
- **Database Management System:** MySQL / MariaDB (via MySQLi Extension)
- **Frontend Framework:** Bootstrap 5 & Bootstrap Icons
- **Interaktivitas & CRUD:** jQuery AJAX & SweetAlert2
- **Audio Panggilan:** ResponsiveVoice.JS (Teks ke suara Bahasa Indonesia)
- **Realtime Engine:** Ratchet PHP WebSocket (Server listener untuk sinkronisasi pemanggilan ke layar monitor)
- **Direct Printing:** `mike42/escpos-php` (Untuk mencetak struk antrian ke printer thermal POS)
- **Server Environment:** Laragon (Sangat direkomendasikan) / XAMPP

---

## 🚀 Fitur Utama & Interface

### 1. 🎫 Nomor Antrian (Pendaftaran)
Halaman yang digunakan oleh pengunjung/driver untuk mengambil nomor antrian sesuai kategori. Fitur ini dapat dihubungkan langsung ke printer POS untuk cetak struk antrian fisik secara otomatis.

### 2. 📢 Panggilan Antrian (Dashboard Loket)
Halaman kontrol bagi petugas loket untuk memanggil antrian. Menampilkan informasi jumlah antrian, nomor antrian aktif, antrian berikutnya, serta sisa antrian. Dilengkapi dengan **fitur panggil ulang (recall)** dan tombol pemanggil suara otomatis yang terhubung ke pengeras suara.

### 3. 🖥️ Monitor Antrian
Halaman layar besar (TV/Display) yang menampilkan status antrian secara *real-time* sekaligus mengeluarkan audio panggilan suara saat petugas loket menekan tombol panggil.

### 4. 🏢 Kelola Master Customer & History
Fasilitas untuk mengelola data perusahaan/supplier (input manual & import file) dengan proteksi duplikasi data, serta pencatatan riwayat antrian secara rinci.

### 5. ⚙️ Setting Aplikasi Antrian
Halaman konfigurasi untuk menyesuaikan nama aplikasi, logo, jumlah loket, hingga tampilan styling dashboard monitor.

---

## ⚙️ Cara Instalasi & Jalankan Proyek

### 1. System Requirements
- Laragon (Rekomendasi Utama) / XAMPP
- PHP 7.4 atau lebih baru
- MySQL / MariaDB
- Composer

### 2. Langkah Instalasi

1. **Clone Repository:**
   Buka terminal/Git Bash, lalu jalankan perintah berikut:
   `git clone https://github.com/Hikari098/aplikasi-antrian-delivery.git`

2. **Install Dependensi Composer:**
   Masuk ke folder proyek via terminal, lalu jalankan:
   `composer install`

3. **Setup Database:**
   - Buat database baru di MySQL/HeidiSQL/phpMyAdmin (misalnya: `db_antrian`).
   - Import file `database/antrian_delivery.sql` ke dalam database tersebut.

4. **Konfigurasi Database:**
   - Salin file `config/database.php.example` menjadi `config/database.php`.
   - Sesuaikan host, username, password, dan nama database dengan server lokal Anda.

5. **Jalankan Aplikasi:**
   - Buka browser dan akses aplikasi melalui `http://localhost/aplikasi-antrian/` (atau domain lokal Laragon).

6. **Akses Login Default:**
   - **Username:** `superadmin`
   - **Password:** `superadmin@123`
