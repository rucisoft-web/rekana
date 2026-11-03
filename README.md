# Akredia

**Akredia** adalah aplikasi web untuk membantu pimpinan dan tim mutu perguruan tinggi menyiapkan **akreditasi institusi** dengan instrumen **IAPT 4.1** (PerBAN-PT Nomor 35 Tahun 2025): 4 kriteria, 39 indikator, 64 sub-indikator per varian, dan 8 varian Buku 3 sesuai jenis perguruan tinggi.

Akredia mengubah pekerjaan akreditasi yang biasanya tersebar di folder, spreadsheet, dan grup percakapan menjadi satu alur yang dapat dipantau: kertas kerja per kata kunci deskriptor, pustaka bukti dengan validasi empat mata, penulisan LED dengan batas kata, perhitungan rasio dari data kinerja, rekonsiliasi dengan PDDikti, simulasi skor, dan dasbor eksekutif untuk rapat pimpinan.

- **Teknologi:** Laravel 13, PHP 8.3, MySQL 8, Filament 5. Tidak membutuhkan Node.js, Redis, atau layanan tambahan, sehingga dapat diunggah ke hosting **cPanel** biasa.
- **Bahasa antarmuka:** Indonesia.
- **Multi-institusi:** satu instalasi dapat melayani beberapa perguruan tinggi; data tiap institusi terisolasi.

![Dasbor eksekutif](docs/demo-dasbor-eksekutif.png)

## Fitur utama

| Modul | Isi |
|---|---|
| **Dasbor Eksekutif** | Indeks Kesiapan Akreditasi (AKI) dari 5 komponen, tren 16 minggu, proyeksi skor Terakreditasi dan Unggul, peta panas 39 indikator, syarat perlu, status bukti, kematangan LED, rasio terhadap ambang, matriks risiko 5×5, tugas per unit, data mahasiswa, dosen, akreditasi prodi, tracer, survei kepuasan, penelitian & PkM, dan panel *Langkah pimpinan minggu ini* yang disusun otomatis dari data. |
| **Panduan Pimpinan** | Enam halaman penjelasan untuk rektor dan pimpinan: mengenal Akredia, peta jalan 12 bulan, memahami IAPT 4.1, peran pimpinan di tiap tahap, cara membaca dasbor, dan tanya jawab. |
| **Proyek Akreditasi** | Jalur Terakreditasi/Unggul yang dapat dikunci, status proyek, daftar eligibilitas, gerbang *Siap Diajukan*, kunci data TS (freeze), duplikasi proyek. |
| **Butir Kerja & Kertas Kerja** | 64 butir per proyek, PIC dan reviewer (tidak boleh orang yang sama), penilaian mandiri, asesor internal, gap per kata kunci, RTL menjadi tugas, warna hijau/kuning/merah/abu-abu otomatis. |
| **Pustaka Bukti** | Unggah berkas atau tautan, versi, validasi empat mata, kedaluwarsa otomatis, kerahasiaan per unit, pemeriksa tautan rusak, portal bukti publik untuk asesor. |
| **Penyusunan LED** | Editor per sub-indikator dengan penghitung kata, sitasi bukti `[B:BKT/2026/001]`, angka hidup `{{DTJA}}`, cakupan kata kunci, reviu dan pengesahan pimpinan, riwayat revisi, komentar, asisten narasi (lokal, dan AI opsional). Ekspor DOCX dan versi cetak. |
| **Data Kinerja & LKPT** | Dosen, statistik prodi, kohort, prestasi, publikasi, HKI, rekognisi, dana penelitian/PkM, audit keuangan, survei 6 kelompok, tracer study, kepuasan pengguna. 18 metrik IAPT dihitung otomatis; rumus dapat diubah admin. 12 tabel LKPT dan ekspor XLSX. Impor dari XLSX/CSV. |
| **Rekonsiliasi PDDikti** | Bandingkan angka internal dan PDDikti, tingkat keparahan otomatis, alur investigasi sampai terverifikasi. |
| **Simulasi Penilaian** | Analisis *what-if*: centang sub-indikator yang akan dipenuhi dan lihat dampaknya pada proyeksi skor dan syarat perlu. |
| **Pengendalian** | Tugas & RTL, papan Kanban, kalender tenggat, register risiko, keputusan pimpinan yang diturunkan menjadi tugas. |
| **SPMI** | Standar mutu, AMI, temuan, CAPA dengan verifikasi efektivitas oleh orang lain, rapat tinjauan manajemen. |
| **Asesmen Lapangan** | Jadwal sesi, narasumber, bank pertanyaan per kelompok dengan status gladi, rekomendasi asesor yang dapat dijadikan tugas atau CAPA. |
| **Laporan** | LKPT XLSX, kertas kerja XLSX, LED DOCX/cetak, laporan syarat perlu, ringkasan mingguan untuk pimpinan, log audit CSV. |
| **Tombol Bantuan** | Tombol *Bantuan* di bilah atas setiap halaman membuka panel berisi penjelasan halaman, langkah kerja, arti setiap isian beserta contoh pengisian, ketentuan resmi BAN-PT, tabel LKPT terkait, dan teks resmi indikator IAPT 4.1 (Lampiran 3c dan Panduan LED) yang dapat ditelusuri untuk indikator 1–39. Isi ada di `resources/help/halaman.php` dan `database/data/iapt41-resmi-3c.php`. |
| **Admin Platform** (`/platform`) | Institusi, pengguna, Pustaka Instrumen (ekspor/impor templat XLSX, ubah deskriptor dan syarat perlu), rumus metrik. |

### Peran pengguna

| Peran | Hak utama |
|---|---|
| Administrator | Semua fitur di institusinya, termasuk pengguna dan konfigurasi. |
| Pimpinan | Melihat semua, mengunci jalur, mengesahkan LED, mencatat keputusan. |
| Manajer Akreditasi | Mengelola proyek, PIC, jadwal, gerbang, kunci data. |
| PIC Indikator | Kertas kerja, LED, dan bukti pada butir yang ditugaskan. |
| Pemilik Data | Bukti dan data kinerja dalam cakupan unitnya. |
| Reviewer | Mereviu bukti dan LED; tidak boleh mereviu karyanya sendiri. |
| Asesor Internal | Penilaian independen dan temuan. |
| Pengamat | Hanya baca. |

## Menjalankan secara lokal

```bash
git clone https://github.com/rucisoft-web/akredia.git
cd akredia
composer install
cp .env.example .env          # sesuaikan DB_*, APP_URL, APP_DEBUG=true, APP_ENV=local
php artisan key:generate
php artisan migrate --seed    # isi AKREDIA_DEMO=true di .env untuk data contoh
php artisan serve
```

Buka `http://localhost:8000/app` untuk aplikasi institusi dan `http://localhost:8000/platform` untuk admin platform.

Untuk instalasi di hosting cPanel (termasuk tanpa Terminal), ikuti [PANDUAN-CPANEL.md](PANDUAN-CPANEL.md).

## Data contoh

Dengan `AKREDIA_DEMO=true`, perintah `php artisan migrate --seed` (atau `php artisan akredia:demo`) memuat dua perguruan tinggi contoh: **Universitas Negeri Jakarta** dan **UIN Sunan Kalijaga Yogyakarta** (lihat di bawah). Profil diambil dari laman resmi masing-masing; angka kinerja dan aktivitas adalah ilustrasi.

Contoh fiktif lama, `php artisan akredia:demo unm`, memuat **Universitas Nusantara Mandiri**, PTS akademik fiktif di Bandung yang sedang menyiapkan *Akreditasi Institusi 2027*: 18 prodi, sekitar 390 dosen, data 8 tahun, 115 dokumen bukti, narasi LED dalam berbagai status, temuan AMI, CAPA, risiko, simulasi visitasi, dan riwayat AKI 16 minggu. Satu syarat perlu (2A) sengaja belum terpenuhi agar alur pengendalian terlihat.

Kata sandi semua akun demo: `Demo#2026akredia` (atau nilai `AKREDIA_DEMO_PASSWORD`).

| Peran | Email |
|---|---|
| Pimpinan (Rektor) | `rektor@akredia.test` |
| Pimpinan (Wakil Rektor I) | `wr1@akredia.test` |
| Manajer Akreditasi (Kepala LPM) | `manajer@akredia.test` |
| Administrator PT | `admin.pt@akredia.test` |
| Reviewer | `reviewer@akredia.test`, `reviewer2@akredia.test` |
| Asesor internal | `asesor@akredia.test` |
| PIC | `pic.lpm@`, `pic.akademik@`, `pic.sdm@`, `pic.keuangan@`, `pic.lppm@`, `pic.kemahasiswaan@`, `pic.sarpras@`, `pic.perencanaan@`, `pic.sekretariat@`, `pic.tik@`, `pic.satgas@akredia.test` |
| Pemilik data | `data.feb@akredia.test` |
| Pengamat (Ketua Yayasan) | `pengamat@akredia.test` |

### Demo Universitas Negeri Jakarta

`php artisan akredia:demo unj` memuat **Universitas Negeri Jakarta** di `/app/unj`: PTN Badan Hukum (PP 31/2024) yang menyiapkan *Reakreditasi Institusi 2027 jalur Unggul* karena SK peringkat Unggul berlaku sampai 2 Februari 2027.

- **Diambil dari unj.ac.id (Oktober 2026):** identitas dan kode PT, status PTN-BH, Rektor dan empat Wakil Rektor, visi-misi, 8 fakultas dan Sekolah Pascasarjana, struktur direktorat, riwayat akreditasi institusi, 59 prodi terakreditasi internasional (ASIIN, AQAS, FIBAA), serta capaian QS/THE yang dijadikan bukti unggulan.
- **Data ilustrasi:** nama pejabat di bawah Wakil Rektor, dosen, angka mahasiswa, publikasi, survei, temuan AMI, risiko, tugas, dan narasi LED. Data ini bukan data resmi UNJ.

Skenario: 91 prodi dengan sekitar 1.190 dosen tetap dan 43 ribu mahasiswa. Satu syarat perlu jalur Unggul (11B, proporsi lektor kepala dan guru besar) sengaja belum terpenuhi. Prodi Gizi dan Bisnis Digital akreditasinya hampir berakhir, dan data mahasiswa aktif masih selisih dengan PDDikti.

![Dasbor UNJ](docs/demo-unj-dasbor.png)

| Peran | Email (`@unj.akredia.test`) |
|---|---|
| Rektor | `rektor@` |
| Wakil Rektor I–IV | `wr1@`, `wr2@`, `wr3@`, `wr4@` |
| Kepala SPM / Ketua Tim Reakreditasi | `manajer@` |
| Administrator | `admin.pt@` |
| Reviewer, asesor internal | `reviewer@`, `reviewer2@`, `asesor@` |
| PIC | `pic.lpm@`, `pic.akademik@`, `pic.sdm@`, `pic.keuangan@`, `pic.lppm@`, `pic.kemahasiswaan@`, `pic.sarpras@`, `pic.perencanaan@`, `pic.kerjasama@`, `pic.sekretariat@`, `pic.spi@`, `pic.tik@`, `pic.satgas@` |
| Pengamat (MWA) | `pengamat@` |

### Demo UIN Sunan Kalijaga Yogyakarta

`php artisan akredia:demo uin-suka` memuat **Universitas Islam Negeri Sunan Kalijaga Yogyakarta** di `/app/uin-suka`: PTN akademik di bawah Kementerian Agama (IAPT 4.1 varian 3b) yang memulai *Reakreditasi Institusi 2028 jalur Unggul* sejak dini (AKI sekitar 71). Syarat perlu terpenuhi; tim memperkuat analisis capaian kelulusan (12B), bukti dampak riset (19B), dan tindak lanjut survei kepuasan (34B).

- **Diambil dari uin-suka.ac.id dan laman LPM (Oktober 2026):** nama, alamat, visi, Rektor 2024-2028 (Prof. Noorhaidi Hasan), delapan fakultas dan Pascasarjana, SK akreditasi institusi Unggul (1121/SK/BAN-PT/Ak.Ppj/PT/XII/2023), ASIIN untuk Matematika, Fisika, Kimia, dan Biologi (sampai 2030), FIBAA untuk Studi Agama-Agama, peringkat pertama PTKIN di Webometrics Juli 2025, dan jabatan Rektor sebagai Presiden AIUA 2026-2028.
- **Data ilustrasi:** kode PT dan prodi, masa berlaku SK, nama Wakil Rektor dan pejabat lain, peringkat dan tanggal akreditasi prodi, dosen, mahasiswa, dan seluruh aktivitas. Akun: `rektor@uin-suka.akredia.test`, `manajer@uin-suka.akredia.test`, dan pola yang sama dengan UNJ (Wakil Rektor `wr1@` s.d. `wr3@`).

### Masuk sebagai tamu

Halaman login menampilkan pilihan **Masuk sebagai Tamu** dengan kartu perguruan tinggi contoh (UNJ dan UIN Sunan Kalijaga). Tamu masuk sebagai Pengamat (hanya lihat): tombol buat, ubah, hapus, dan tindakan pengubah data lain disembunyikan, halaman profil tidak dapat dibuka, dan dapat berganti perguruan tinggi dari pita di atas layar. Setiap halaman institusi contoh menampilkan penanda **DATA DEMO**. Akun tamu bersama `tamu@demo.akredia.test` dibuat oleh `php artisan akredia:demo`; matikan pilihan tamu dengan `AKREDIA_DEMO_GUEST=false`.

![Login tamu](docs/login-tamu.png)

### Halaman depan

Alamat `/` kini menampilkan halaman depan publik: masalah yang sering menghambat persiapan, 12 fitur utama, sorotan Simulasi Penilaian, editor LED, dan Rekonsiliasi PD Dikti (bertanda DATA CONTOH), kartu dua perguruan tinggi contoh dengan tombol **Masuk sebagai tamu** per kampus, keamanan dan AI, pemasangan cPanel, serta tanya jawab. Bila akun tamu belum ada, bagian data contoh disembunyikan dan tombol mengarah ke login. Pengguna yang sudah masuk melihat tombol **Buka aplikasi**.

![Halaman depan](docs/halaman-depan.png)

Halaman pengenalan **Akredia untuk Pimpinan Perguruan Tinggi** tersedia publik di `/untuk-pimpinan` (ditautkan dari halaman login) dan sebagai sub menu pertama grup *Panduan Pimpinan* di aplikasi.

Bagian **Workshop** di halaman depan menawarkan workshop dua hari *Persiapan Akreditasi Institusi Perguruan Tinggi dengan Akredia* bersama Pusat Studi Manajemen Perguruan Tinggi UIN Sunan Kalijaga. Materi lengkap (8 sesi, praktik di Akredia, dan luaran tiap sesi) ada di `/workshop`; isinya diatur di `app/Support/Workshop.php`.

Hapus data contoh kapan saja: `php artisan akredia:remove-demo` (semua) atau `php artisan akredia:remove-demo unm` (satu institusi).

## Perintah artisan

| Perintah | Fungsi |
|---|---|
| `akredia:create-admin` | Buat atau perbarui administrator platform. |
| `akredia:doctor` | Periksa PHP, ekstensi, database, izin folder, aset, dan cron. |
| `akredia:demo [unj\|uin-suka\|unm\|semua]` / `akredia:remove-demo [slug]` | Muat atau hapus data contoh (`semua` = UNJ dan UIN Sunan Kalijaga). |
| `akredia:instrument` | Perbarui Pustaka Instrumen (syarat perlu, ambang metrik, deskriptor) dari data bawaan; menimpa ubahan manual. |
| `akredia:snapshot` | Hitung ulang AKI dan rekam snapshot harian (terjadwal 01.00). |
| `akredia:reminders` | Pengingat tenggat dan eskalasi tugas terlambat (terjadwal 07.00). |
| `akredia:check-links` | Periksa tautan bukti dan hyperlink LED (terjadwal mingguan). |
| `akredia:weekly-report` | Kirim ringkasan mingguan ke pimpinan (terjadwal Senin 06.30). |

Semua jadwal berjalan dari satu cron per menit (lihat PANDUAN-CPANEL.md langkah 7). Di cPanel, kolom Command diisi `cd /home/USER/akredia && php artisan schedule:run` tanpa bintang jadwal.

## Pengujian

```bash
php artisan test
```

Pengujian mencakup evaluator rumus, prinsip empat mata (PIC/reviewer, pengunggah/validator, pelaksana/verifikator CAPA, penulis/reviewer LED), batas kata LED, gerbang syarat perlu, isolasi data antar-institusi, kerahasiaan dokumen per unit, akses halaman per peran, ekspor, portal publik, dan survei.

## Catatan

AKI dan proyeksi skor adalah alat manajemen internal untuk memantau kesiapan, bukan prediksi resmi nilai BAN-PT. Keputusan akreditasi sepenuhnya kewenangan BAN-PT.

© Rucisoft. Data contoh seluruhnya fiktif.
