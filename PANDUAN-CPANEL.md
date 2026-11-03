# Panduan Instalasi Rekana di cPanel

Panduan ini untuk hosting cPanel biasa (shared hosting atau VPS dengan cPanel). Ada dua jalur: **dengan Terminal/SSH** (lebih mudah) dan **tanpa Terminal** (memakai File Manager dan Cron Jobs sekali jalan).

Contoh di panduan memakai akun cPanel `userku`, folder program `/home/userku/rekana`, dan alamat `https://rekana.perusahaananda.co.id`. Sesuaikan dengan milik Anda.

## 1. Prasyarat hosting

| Komponen | Ketentuan | Tempat di cPanel |
|---|---|---|
| PHP | **8.3 atau lebih baru** | *MultiPHP Manager* (pilih domain › PHP 8.3) atau *Select PHP Version* (CloudLinux) |
| Ekstensi PHP | `pdo_mysql`, `mbstring`, `intl`, `fileinfo`, `openssl`, `zip`, `xml`, `dom`, `gd`, `curl`, `ctype`, `tokenizer` | *Select PHP Version › Extensions* (terutama **intl**, **zip**, dan **fileinfo**) |
| Database | **MySQL 8** (atau MariaDB 10.6+) | *MySQL Databases* |
| SSL | Sangat disarankan, karena aplikasi menyimpan dokumen K3 owner dan rekanan | *SSL/TLS Status* › Run AutoSSL |
| Cron | Satu cron per menit untuk kedaluwarsa kartu akses dan pengingat | *Cron Jobs* |
| Ruang disk | ± 150 MB untuk program, ditambah ruang untuk berkas dokumen rekanan | — |

Tidak perlu Node.js, Redis, atau Supervisor. Grafik dasbor memakai berkas JavaScript statis.

## 2. Buat database

1. Buka **MySQL Databases**.
2. *Create New Database*: misalnya `rekana` (cPanel menjadikannya `userku_rekana`).
3. *Add New User*: misalnya `rekana` (menjadi `userku_rekana`), kata sandi kuat. Catat.
4. *Add User To Database*: pilih user dan database tadi › centang **ALL PRIVILEGES** › Make Changes.

## 3. Taruh program di server

Program diletakkan **di luar `public_html`** (misalnya `/home/userku/rekana`), supaya `.env`, kode, dan berkas dokumen rekanan tidak bisa dibuka dari browser.

### Jalur A: dengan Terminal (cPanel › Terminal, atau SSH)

```bash
cd ~
git clone https://github.com/rucisoft-web/rekana.git
cd rekana
composer install --no-dev --optimize-autoloader
```

Bila repo privat, `git clone` meminta username GitHub dan *personal access token*. Bila perintah `composer` tidak ada: `curl -sS https://getcomposer.org/installer | php` lalu `php composer.phar install --no-dev --optimize-autoloader`.

Bila `php -v` di Terminal bukan 8.3, panggil PHP 8.3 langsung, misalnya `/opt/cpanel/ea-php83/root/usr/bin/php` (EasyApache) atau `/opt/alt/php83/usr/bin/php` (CloudLinux).

### Jalur B: tanpa Terminal (File Manager)

1. Pakai paket **`rekana-cpanel.zip`** (sudah berisi folder `vendor` versi produksi, tidak perlu Composer).
2. *File Manager* › buka `/home/userku` › **Upload** zip tersebut.
3. Klik kanan zip › **Extract** ke `/home/userku`. Hasilnya folder `/home/userku/rekana`.
4. Hapus file zip setelah diekstrak.

## 4. Arahkan domain ke folder `public`

### Pilihan 1 (disarankan): subdomain atau addon domain

1. *Domains* › **Create A New Domain**, misalnya `rekana.perusahaananda.co.id`.
2. Matikan centang *Share document root*, lalu isi **Document Root**: `rekana/public`.
3. Simpan, lalu aktifkan SSL di *SSL/TLS Status*.

### Pilihan 2: domain utama yang document root-nya terkunci di `public_html`

1. Salin **seluruh isi** `rekana/public` ke `public_html`, termasuk file tersembunyi `.htaccess` (File Manager › *Settings* › centang *Show Hidden Files*).
2. Edit `public_html/index.php`, ganti tiga baris yang berisi `__DIR__.'/../` menjadi:

```php
if (file_exists($maintenance = __DIR__.'/../rekana/storage/framework/maintenance.php')) {
require __DIR__.'/../rekana/vendor/autoload.php';
$app = require_once __DIR__.'/../rekana/bootstrap/app.php';
```

3. Setiap kali memperbarui program, salin ulang isi `rekana/public` ke `public_html` (kecuali `index.php` yang sudah diedit).

> Jangan mengekstrak seluruh program langsung ke `public_html`. Bila terlanjur, `.htaccess` di folder program meneruskan semua permintaan ke `public/` sehingga `.env`, `vendor`, dan `storage` tidak tersaji, tetapi tetap pindahkan secepatnya.

## 5. Isi file `.env`

Di folder `rekana`, salin `.env.example` menjadi `.env` (File Manager › klik kanan › *Copy*, lalu *Rename*), kemudian *Edit*. Bagian yang wajib diubah:

```dotenv
APP_NAME=Rekana
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://rekana.perusahaananda.co.id
APP_TIMEZONE=Asia/Jakarta

DB_CONNECTION=mysql
DB_HOST=localhost
DB_DATABASE=userku_rekana
DB_USERNAME=userku_rekana
DB_PASSWORD="kata-sandi-database"

SESSION_SECURE_COOKIE=true

# Email pengirim (cPanel › Email Accounts › Connect Devices)
MAIL_MAILER=smtp
MAIL_HOST=mail.perusahaananda.co.id
MAIL_PORT=465
MAIL_USERNAME=rekana@perusahaananda.co.id
MAIL_PASSWORD="kata-sandi-email"
MAIL_FROM_ADDRESS="rekana@perusahaananda.co.id"

# Admin platform (dibuat saat migrate --seed). Kosongkan lagi setelah instalasi.
REKANA_ADMIN_EMAIL=admin@perusahaananda.co.id
REKANA_ADMIN_PASSWORD="SandiSementara#2026"

# Data contoh simulasi (Garudafood, MedcoEnergi, Pakuwon Jati). false untuk pemakaian sungguhan.
REKANA_DEMO=false
```

`APP_KEY=` dibiarkan kosong; diisi oleh `key:generate` di langkah 6. **Nilai yang mengandung `#` atau spasi harus diberi tanda kutip**, karena tanpa kutip tanda `#` dibaca sebagai awal komentar dan kata sandi terpotong.

## 6. Kunci aplikasi, tabel, dan admin

### Dengan Terminal

```bash
cd ~/rekana
php artisan key:generate --force
php artisan migrate --seed --force
php artisan optimize
php artisan rekana:doctor
```

Perintah `migrate --seed` membuat semua tabel dan memuat **template kuesioner PQ bawaan** (ISO 45001 dan SMK3 untuk industri pangan, konstruksi gedung, dan migas). Lalu buat owner pertama beserta administratornya:

```bash
php artisan rekana:owner nama-pabrik --name="PT Contoh · Pabrik Mojokerto" --email=hse@perusahaananda.co.id --password='SandiKuat#2026'
```

### Tanpa Terminal (Cron Jobs sekali jalan)

Tambahkan perintah berikut satu per satu di **Cron Jobs** dengan jadwal *Once Per Minute*. Tunggu 1–2 menit, periksa hasilnya, lalu **hapus** cron itu sebelum menambah yang berikutnya.

1. `cd /home/userku/rekana && php artisan key:generate --force > storage/logs/setup-1.txt 2>&1`
2. `cd /home/userku/rekana && php artisan migrate --seed --force > storage/logs/setup-2.txt 2>&1`
3. `cd /home/userku/rekana && php artisan optimize > storage/logs/setup-3.txt 2>&1`
4. `cd /home/userku/rekana && php artisan rekana:owner nama-pabrik --name="PT Contoh" --email=hse@perusahaananda.co.id --password='SandiKuat#2026' > storage/logs/setup-4.txt 2>&1`
5. `cd /home/userku/rekana && php artisan rekana:doctor > storage/logs/setup-5.txt 2>&1`

Hasil tiap langkah ada di `rekana/storage/logs/setup-*.txt`. Bila `php` di cron bukan versi 8.3, ganti `php` dengan jalur lengkap seperti `/opt/cpanel/ea-php83/root/usr/bin/php`.

Setelah berhasil, kosongkan `REKANA_ADMIN_PASSWORD` di `.env` dan jalankan `php artisan optimize` lagi.

## 7. Pasang cron permanen

Rekana memakai satu cron untuk semua tugas terjadwal (kartu akses kedaluwarsa setiap 00.10, pengingat sertifikat CSMS dan dokumen rekanan yang akan habis setiap 07.00, dan antrean email):

**Di cPanel › Cron Jobs:**

1. *Common Settings*: pilih **Once Per Minute (\* \* \* \* \*)**. Kolom Minute, Hour, Day, Month, dan Weekday otomatis terisi `*`.
2. Kolom **Command** diisi **tanpa** bintang jadwal:

```
cd /home/userku/rekana && php artisan schedule:run >> /dev/null 2>&1
```

3. Klik *Add New Cron Job*.

Ganti `userku` dengan nama akun cPanel Anda yang sebenarnya (lihat jalur folder di File Manager, misalnya `/home/unmadmin`). Bila PHP bawaan cron bukan 8.3, ganti `php` dengan jalur lengkap, misalnya `/opt/cpanel/ea-php83/root/usr/bin/php`.

> Jangan menempelkan `* * * * *` ke kolom Command. cPanel sudah menambahkan jadwal sendiri, sehingga bintang menjadi dobel dan muncul galat `bad command / errors in crontab file, can't install`.

**Lewat Terminal (`crontab -e`)** barulah baris ditulis lengkap dengan jadwalnya:

```
* * * * * cd /home/userku/rekana && php artisan schedule:run >> /dev/null 2>&1
```

Jalankan `php artisan rekana:doctor` beberapa menit kemudian; baris *Penjadwal (cron) berjalan* harus OK.

Tanpa cron aplikasi tetap berjalan dan dasbor tetap dihitung langsung, tetapi status kartu akses tidak otomatis berubah menjadi Kedaluwarsa dan pengingat tidak terkirim.

## 8. Mulai memakai

1. Administrator owner masuk di `https://rekana.perusahaananda.co.id/app/login`.
2. Buka menu **Panduan** (atau tombol **Bantuan** di setiap halaman) dan ikuti urutannya:
   1. **Area kerja**: daftar area dan kelas bahayanya (umum, produksi pangan, terbatas, bahaya).
   2. **Pengguna & Peran**: undang pimpinan, pemilik pekerjaan, pengadaan, evaluator HSE, pengawas lapangan, dan pengamat.
   3. **Template kuesioner PQ**: salin template bawaan dan sesuaikan dengan pedoman CSMS perusahaan.
   4. **Daftar rekanan**: verifikasi rekanan, lalu mulai prakualifikasi.
   5. **Rencana pekerjaan & risiko**, **Seleksi**, **Kontrak & mobilisasi**, **Induksi**, **Kartu akses**, dan **Izin kerja** mengikuti fase IOGP 423.
3. Rekanan masuk di `https://rekana.perusahaananda.co.id/kontraktor/login` untuk mengisi dokumen, statistik K3, pekerja, jawaban PQ, dan HSE Plan.
4. Pimpinan memantau lewat **Dasbor Monitoring**.

### Situs demo / promosi

Isi `REKANA_DEMO=true`, lalu jalankan `php artisan rekana:demo` dan `php artisan optimize`. Perintah ini memuat tiga owner contoh simulasi (sekitar 1 menit):

- **Garudafood · Business Unit Gresik** di `/app/garudafood-gresik` (pabrik biskuit, paling lengkap)
- **MedcoEnergi** di `/app/medco-corridor` (hulu migas)
- **Pakuwon Jati** di `/app/pakuwon-surabaya` (konstruksi gedung)

Akun owner: `admin@garudafood.rekana.test`, `pimpinan@…`, `evaluator@…`, `pengawas@…`, dan seterusnya. Akun rekanan: `admin@<kode>.<domain-owner>`, misalnya `admin@mesin.garudafood.rekana.test`. Kata sandi semua akun: nilai `REKANA_DEMO_PASSWORD` (bawaan `Demo#2026rekana`).

Halaman depan `https://domain-anda/` menjelaskan Rekana dan menampilkan tombol **Masuk sebagai tamu** untuk tiap owner contoh, sebagai owner maupun sebagai rekanan (akun bersama `tamu@demo.rekana.test`, hanya lihat). Matikan pilihan tamu dengan `REKANA_DEMO_GUEST=false`. Semua layar dan cetakan owner contoh berlabel **DATA SIMULASI**.

Muat ulang satu owner dengan `php artisan rekana:demo garudafood-gresik --force`. Hapus semua data contoh kapan saja dengan `php artisan rekana:remove-demo --force`.

## 9. Memperbarui program

Dengan Terminal:

```bash
cd ~/rekana
php artisan down
git pull
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan optimize
php artisan up
```

Tanpa Terminal: upload zip versi baru, ekstrak menimpa folder `rekana` (zip tidak berisi `.env`, dan berkas di `storage` tidak terhapus), lalu jalankan `php artisan migrate --force` dan `php artisan optimize` lewat cron sekali jalan.

Selalu **backup database dan folder `storage/app/private`** (berkas dokumen rekanan) sebelum memperbarui. Di cPanel: *Backup* › *Download a MySQL Database Backup*, dan kompres folder `storage/app/private` lewat File Manager.

## 10. Masalah umum

| Gejala | Solusi |
|---|---|
| Error 500 tanpa keterangan | Lihat `storage/logs/laravel-*.log`. Sementara boleh `APP_DEBUG=true`, lalu kembalikan ke `false`. |
| `Your PHP version … does not satisfy` | Ubah PHP domain ke 8.3, dan pastikan cron/Terminal juga memakai PHP 8.3. |
| `Specified key was too long` | Database bukan utf8mb4 atau MySQL terlalu lama. Pakai MySQL 8 / MariaDB 10.6+. |
| Halaman tampil tanpa gaya | Document root belum ke `rekana/public`, atau isi `public` (termasuk folder `css/filament` dan `js/filament`) belum tersalin lengkap. |
| `403 Forbidden` / *Access to this resource on the server is denied* | Arahkan Document Root ke `rekana/public`. Pastikan izin folder 755 dan berkas 644, serta `public/index.php` dan `public/.htaccess` ada. |
| Semua halaman selain beranda 404 | `.htaccess` dari `public` belum ikut tersalin. |
| `419 Page Expired` saat login | `APP_URL` harus sama persis dengan alamat yang dibuka. Bila belum SSL, hapus `SESSION_SECURE_COOKIE=true`. |
| Unggah dokumen gagal | Naikkan `upload_max_filesize` dan `post_max_size` (misalnya 32M) di *MultiPHP INI Editor*; aktifkan ekstensi **fileinfo**. |
| Admin tidak bisa masuk padahal sandi benar | Sandi di `.env` mengandung `#` tanpa tanda kutip. Beri kutip, lalu buat ulang dengan `php artisan rekana:owner <slug> --email=... --password='...'`. |
| Pengingat tidak terkirim | Periksa cron (langkah 7) dan pengaturan `MAIL_*`. Coba `php artisan rekana:reminders`. |
| Pengguna kehilangan ponsel MFA | Administrator owner › Pengguna & Peran, atau hubungi admin platform. |
| Perubahan `.env` tidak berlaku | `php artisan optimize:clear` lalu `php artisan optimize`. |
