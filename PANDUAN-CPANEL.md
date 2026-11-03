# Panduan Instalasi Akredia di cPanel

Panduan ini untuk hosting cPanel biasa (shared hosting atau VPS dengan cPanel). Ada dua jalur: **dengan Terminal/SSH** (lebih mudah) dan **tanpa Terminal** (memakai File Manager dan Cron Jobs sekali jalan).

Contoh di panduan memakai akun cPanel `userku`, folder program `/home/userku/akredia`, dan alamat `https://akredia.kampusanda.ac.id`. Sesuaikan dengan milik Anda.

## 1. Prasyarat hosting

| Komponen | Ketentuan | Tempat di cPanel |
|---|---|---|
| PHP | **8.3 atau lebih baru** | *MultiPHP Manager* (pilih domain › PHP 8.3) atau *Select PHP Version* (CloudLinux) |
| Ekstensi PHP | `pdo_mysql`, `mbstring`, `intl`, `fileinfo`, `openssl`, `zip`, `xml`, `dom`, `gd`, `curl`, `ctype`, `tokenizer` | *Select PHP Version › Extensions* (terutama **intl**, **zip**, dan **fileinfo**) |
| Database | **MySQL 8** (atau MariaDB 10.6+) | *MySQL Databases* |
| SSL | Sangat disarankan, karena aplikasi menyimpan dokumen internal kampus | *SSL/TLS Status* › Run AutoSSL |
| Cron | Satu cron per menit untuk pengingat, snapshot harian, dan ringkasan mingguan | *Cron Jobs* |
| Ruang disk | ± 150 MB untuk program, ditambah ruang untuk berkas bukti | — |

Tidak perlu Node.js, Redis, atau Supervisor. Grafik dasbor memakai berkas JavaScript statis.

## 2. Buat database

1. Buka **MySQL Databases**.
2. *Create New Database*: misalnya `akredia` (cPanel menjadikannya `userku_akredia`).
3. *Add New User*: misalnya `akredia` (menjadi `userku_akredia`), kata sandi kuat. Catat.
4. *Add User To Database*: pilih user dan database tadi › centang **ALL PRIVILEGES** › Make Changes.

## 3. Taruh program di server

Program diletakkan **di luar `public_html`** (misalnya `/home/userku/akredia`), supaya `.env`, kode, dan berkas bukti tidak bisa dibuka dari browser.

### Jalur A: dengan Terminal (cPanel › Terminal, atau SSH)

```bash
cd ~
git clone https://github.com/rucisoft-web/akredia.git
cd akredia
composer install --no-dev --optimize-autoloader
```

Bila repo privat, `git clone` meminta username GitHub dan *personal access token*. Bila perintah `composer` tidak ada: `curl -sS https://getcomposer.org/installer | php` lalu `php composer.phar install --no-dev --optimize-autoloader`.

Bila `php -v` di Terminal bukan 8.3, panggil PHP 8.3 langsung, misalnya `/opt/cpanel/ea-php83/root/usr/bin/php` (EasyApache) atau `/opt/alt/php83/usr/bin/php` (CloudLinux).

### Jalur B: tanpa Terminal (File Manager)

1. Pakai paket **`akredia-cpanel.zip`** (sudah berisi folder `vendor` versi produksi, tidak perlu Composer).
2. *File Manager* › buka `/home/userku` › **Upload** zip tersebut.
3. Klik kanan zip › **Extract** ke `/home/userku`. Hasilnya folder `/home/userku/akredia`.
4. Hapus file zip setelah diekstrak.

## 4. Arahkan domain ke folder `public`

### Pilihan 1 (disarankan): subdomain atau addon domain

1. *Domains* › **Create A New Domain**, misalnya `akredia.kampusanda.ac.id`.
2. Matikan centang *Share document root*, lalu isi **Document Root**: `akredia/public`.
3. Simpan, lalu aktifkan SSL di *SSL/TLS Status*.

### Pilihan 2: domain utama yang document root-nya terkunci di `public_html`

1. Salin **seluruh isi** `akredia/public` ke `public_html`, termasuk file tersembunyi `.htaccess` (File Manager › *Settings* › centang *Show Hidden Files*).
2. Edit `public_html/index.php`, ganti tiga baris yang berisi `__DIR__.'/../` menjadi:

```php
if (file_exists($maintenance = __DIR__.'/../akredia/storage/framework/maintenance.php')) {
require __DIR__.'/../akredia/vendor/autoload.php';
$app = require_once __DIR__.'/../akredia/bootstrap/app.php';
```

3. Setiap kali memperbarui program, salin ulang isi `akredia/public` ke `public_html` (kecuali `index.php` yang sudah diedit).

> Jangan mengekstrak seluruh program langsung ke `public_html`. Bila terlanjur, `.htaccess` di folder program meneruskan semua permintaan ke `public/` sehingga `.env`, `vendor`, dan `storage` tidak tersaji, tetapi tetap pindahkan secepatnya.

## 5. Isi file `.env`

Di folder `akredia`, salin `.env.example` menjadi `.env` (File Manager › klik kanan › *Copy*, lalu *Rename*), kemudian *Edit*. Bagian yang wajib diubah:

```dotenv
APP_NAME=Akredia
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://akredia.kampusanda.ac.id
APP_TIMEZONE=Asia/Jakarta

DB_CONNECTION=mysql
DB_HOST=localhost
DB_DATABASE=userku_akredia
DB_USERNAME=userku_akredia
DB_PASSWORD="kata-sandi-database"

SESSION_SECURE_COOKIE=true

# Email pengirim pengingat dan ringkasan mingguan (cPanel › Email Accounts › Connect Devices)
MAIL_MAILER=smtp
MAIL_HOST=mail.kampusanda.ac.id
MAIL_PORT=465
MAIL_USERNAME=akredia@kampusanda.ac.id
MAIL_PASSWORD="kata-sandi-email"
MAIL_FROM_ADDRESS="akredia@kampusanda.ac.id"

# Admin platform pertama (dibuat saat migrate --seed). Kosongkan lagi setelah instalasi.
AKREDIA_ADMIN_EMAIL=admin@kampusanda.ac.id
AKREDIA_ADMIN_PASSWORD="SandiSementara#2026"

# Data contoh (Universitas Nusantara Mandiri dan Universitas Negeri Jakarta). false untuk pemakaian sungguhan.
AKREDIA_DEMO=false

# Asisten AI untuk reviu narasi LED (opsional, boleh kosong)
AKREDIA_AI_KEY=
```

`APP_KEY=` dibiarkan kosong; diisi oleh `key:generate` di langkah 6. **Nilai yang mengandung `#` atau spasi harus diberi tanda kutip**, karena tanpa kutip tanda `#` dibaca sebagai awal komentar dan kata sandi terpotong.

## 6. Kunci aplikasi, tabel, dan admin

### Dengan Terminal

```bash
cd ~/akredia
php artisan key:generate --force
php artisan migrate --seed --force
php artisan optimize
php artisan akredia:doctor
```

Perintah `migrate --seed` membuat semua tabel, memuat **Pustaka Instrumen IAPT 4.1** (8 varian, 39 indikator, 512 sub-indikator, 18 rumus metrik), dan membuat admin platform dari `.env`. Admin platform juga bisa dibuat kapan saja:

```bash
php artisan akredia:create-admin admin@kampusanda.ac.id --name="Admin Platform" --password='SandiKuat#2026'
```

### Tanpa Terminal (Cron Jobs sekali jalan)

Tambahkan perintah berikut satu per satu di **Cron Jobs** dengan jadwal *Once Per Minute*. Tunggu 1–2 menit, periksa hasilnya, lalu **hapus** cron itu sebelum menambah yang berikutnya.

1. `cd /home/userku/akredia && php artisan key:generate --force > storage/logs/setup-1.txt 2>&1`
2. `cd /home/userku/akredia && php artisan migrate --seed --force > storage/logs/setup-2.txt 2>&1`
3. `cd /home/userku/akredia && php artisan optimize > storage/logs/setup-3.txt 2>&1`
4. `cd /home/userku/akredia && php artisan akredia:doctor > storage/logs/setup-4.txt 2>&1`

Hasil tiap langkah ada di `akredia/storage/logs/setup-*.txt`. Bila `php` di cron bukan versi 8.3, ganti `php` dengan jalur lengkap seperti `/opt/cpanel/ea-php83/root/usr/bin/php`.

Setelah berhasil, kosongkan `AKREDIA_ADMIN_PASSWORD` di `.env` dan jalankan `php artisan optimize` lagi.

## 7. Pasang cron permanen

Akredia memakai satu cron untuk semua tugas terjadwal (snapshot AKI harian pukul 01.00, pengingat tenggat 07.00, pemeriksaan tautan mingguan, ringkasan mingguan untuk pimpinan setiap Senin 06.30, dan antrean email):

**Di cPanel › Cron Jobs:**

1. *Common Settings*: pilih **Once Per Minute (\* \* \* \* \*)**. Kolom Minute, Hour, Day, Month, dan Weekday otomatis terisi `*`.
2. Kolom **Command** diisi **tanpa** bintang jadwal:

```
cd /home/userku/akredia && php artisan schedule:run >> /dev/null 2>&1
```

3. Klik *Add New Cron Job*.

Ganti `userku` dengan nama akun cPanel Anda yang sebenarnya (lihat jalur folder di File Manager, misalnya `/home/unmadmin`). Bila PHP bawaan cron bukan 8.3, ganti `php` dengan jalur lengkap, misalnya `/opt/cpanel/ea-php83/root/usr/bin/php`.

> Jangan menempelkan `* * * * *` ke kolom Command. cPanel sudah menambahkan jadwal sendiri, sehingga bintang menjadi dobel dan muncul galat `bad command / errors in crontab file, can't install`.

**Lewat Terminal (`crontab -e`)** barulah baris ditulis lengkap dengan jadwalnya:

```
* * * * * cd /home/userku/akredia && php artisan schedule:run >> /dev/null 2>&1
```

Jalankan `php artisan akredia:doctor` beberapa menit kemudian; baris *Penjadwal (cron) berjalan* harus OK.

Tanpa cron aplikasi tetap berjalan; dasbor dihitung ulang saat tombol *Perbarui angka* ditekan, tetapi pengingat dan ringkasan mingguan tidak terkirim.

## 8. Mulai memakai

1. Buka `https://akredia.kampusanda.ac.id/platform`, masuk sebagai admin platform.
2. **Institusi** › tambah perguruan tinggi, pilih **jenis PT** (menentukan varian Buku 3: PTN-BH, PTN/PTS akademik, PJJ, vokasi, PTKL, akademi komunitas).
3. Pada baris institusi, klik **Tambah admin PT** untuk membuat akun administrator kampus.
4. Administrator kampus masuk di `https://akredia.kampusanda.ac.id/app`, lalu:
   1. *Institusi* › Profil Institusi, Unit Kerja, Program Studi, Akreditasi Prodi, Periode Akademik.
   2. *Administrasi* › **Pengguna & Peran**: undang pimpinan, manajer akreditasi (biasanya Kepala LPM), PIC, pemilik data, reviewer, asesor internal, dan pengamat.
   3. **Proyek Akreditasi** › buat proyek, pilih jalur dan tahun TS. Akredia otomatis membangkitkan 64 butir kerja, kertas kerja per kata kunci, dan daftar eligibilitas.
   4. **Butir Kerja** › tetapkan PIC dan reviewer (aksi massal tersedia).
   5. **Impor Data** › unggah data dosen, statistik prodi, kohort, publikasi, dan data PDDikti dari templat XLSX.
   6. Pimpinan memantau lewat **Dasbor Eksekutif** dan membaca menu **Panduan Pimpinan**.

### Situs demo / promosi

Isi `AKREDIA_DEMO=true` lalu jalankan `php artisan akredia:demo` dan `php artisan optimize`. Perintah ini memuat dua perguruan tinggi contoh (sekitar 1 menit):

- **Universitas Negeri Jakarta** (PTN-BH, jalur Unggul) di `/app/unj`, akun `rektor@unj.akredia.test`, dan seterusnya.
- **UIN Sunan Kalijaga Yogyakarta** (PTN akademik/PTKIN, jalur Unggul, persiapan dini) di `/app/uin-suka`, akun `rektor@uin-suka.akredia.test`, dan seterusnya.

Muat satu saja dengan `php artisan akredia:demo unj` (atau `uin-suka`). Contoh fiktif lama Universitas Nusantara Mandiri tetap tersedia dengan `php artisan akredia:demo unm`, tetapi tidak muncul di pilihan tamu; hapus dari server dengan `php artisan akredia:remove-demo unm --force`. Server yang masih memuat contoh lama BINUS dan POLBAN: jalankan `php artisan akredia:remove-demo binus --force` dan `php artisan akredia:remove-demo polban --force`. Daftar akun lengkap ada di README. Kata sandi semua akun: nilai `AKREDIA_DEMO_PASSWORD` (bawaan `Demo#2026akredia`).

Pengunjung dapat melihat perguruan tinggi contoh tanpa akun lewat pilihan **Masuk sebagai Tamu** di halaman login (akun bersama `tamu@demo.akredia.test`, hanya lihat), lalu berganti perguruan tinggi dari pita di atas layar. Akun tamu dibuat oleh `php artisan akredia:demo`. Matikan pilihan tamu dengan `AKREDIA_DEMO_GUEST=false`.

Halaman depan `https://domain-anda/` menjelaskan fitur Akredia dan menampilkan tombol **Masuk sebagai tamu** untuk tiap perguruan tinggi contoh. Halaman publik untuk pimpinan perguruan tinggi: `https://domain-anda/untuk-pimpinan`.

Hapus data contoh kapan saja dengan `php artisan akredia:remove-demo --force`.

## 9. Memperbarui program

Dengan Terminal:

```bash
cd ~/akredia
php artisan down
git pull
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan optimize
php artisan up
```

Tanpa Terminal: upload zip versi baru, ekstrak menimpa folder `akredia` (zip tidak berisi `.env`, dan berkas di `storage` tidak terhapus), lalu jalankan `php artisan migrate --force` dan `php artisan optimize` lewat cron sekali jalan.

Bila catatan rilis menyebut perubahan instrumen (mis. ambang metrik atau syarat perlu disesuaikan dengan Lampiran 3 resmi), jalankan juga `php artisan akredia:instrument --force` lalu `php artisan akredia:snapshot`. Perintah ini menimpa ubahan manual di Pustaka Instrumen.

Selalu **backup database dan folder `storage/app/private`** (berkas bukti) sebelum memperbarui. Di cPanel: *Backup* › *Download a MySQL Database Backup*, dan kompres folder `storage/app/private` lewat File Manager.

## 10. Masalah umum

| Gejala | Solusi |
|---|---|
| Error 500 tanpa keterangan | Lihat `storage/logs/laravel-*.log`. Sementara boleh `APP_DEBUG=true`, lalu kembalikan ke `false`. |
| `Your PHP version … does not satisfy` | Ubah PHP domain ke 8.3, dan pastikan cron/Terminal juga memakai PHP 8.3. |
| `Specified key was too long` | Database bukan utf8mb4 atau MySQL terlalu lama. Pakai MySQL 8 / MariaDB 10.6+. |
| Admin tidak bisa masuk padahal sandi benar | Sandi di `.env` mengandung `#` tanpa tanda kutip. Beri kutip, lalu `php artisan akredia:create-admin email --password='...'`. |
| Halaman tampil tanpa gaya | Document root belum ke `akredia/public`, atau isi `public` (termasuk folder `css/filament` dan `js/filament`) belum tersalin lengkap. |
| `403 Forbidden` / *Access to this resource on the server is denied* | Arahkan Document Root ke `akredia/public`. Pastikan izin folder 755 dan berkas 644, serta `public/index.php` dan `public/.htaccess` ada. Bila memakai paket sebelum 24 Oktober 2026 dan document root mengarah ke folder `akredia`, ganti `akredia/.htaccess` dengan versi terbaru. |
| Semua halaman selain beranda 404 | `.htaccess` dari `public` belum ikut tersalin. |
| `419 Page Expired` saat login | `APP_URL` harus sama persis dengan alamat yang dibuka. Bila belum SSL, hapus `SESSION_SECURE_COOKIE=true`. |
| Unggah bukti gagal | Naikkan `upload_max_filesize` dan `post_max_size` (misalnya 32M) di *MultiPHP INI Editor*; aktifkan ekstensi **fileinfo**. |
| Ekspor XLSX/DOCX gagal | Aktifkan ekstensi **zip** dan **xml**. |
| Pengingat atau ringkasan mingguan tidak terkirim | Periksa cron (langkah 7) dan pengaturan `MAIL_*`. Coba `php artisan akredia:weekly-report`. |
| Pengguna kehilangan ponsel MFA | Admin platform › Pengguna › *Reset MFA*, atau admin PT › Pengguna & Peran. |
| Perubahan `.env` tidak berlaku | `php artisan optimize:clear` lalu `php artisan optimize`. |
