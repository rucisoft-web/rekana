# Rekana

Sistem manajemen keselamatan kontraktor (*Contractor Safety Management System*, CSMS) untuk perusahaan yang memakai banyak kontraktor di lokasinya: pabrik, fasilitas migas, dan proyek konstruksi. Alur kerjanya mengikuti enam fase **IOGP Report 423**:

| Fase | Di Rekana |
|---|---|
| 1. Penilaian risiko | Rencana pekerjaan dengan matriks 5×5, bahaya pemaksa Tinggi, mode kontrak IOGP 1–3, dan daftar kewajiban per kategori |
| 2. Prakualifikasi | Kuesioner PQ berbobot, butir kritis, skor mandiri dan skor evaluator, prinsip empat mata, sertifikat CSMS ber-QR (berlaku 2 tahun) |
| 3. Seleksi | Undangan hanya untuk rekanan bersertifikat yang kategorinya cukup, penilaian HSE Plan dengan rubrik, nilai akhir HSE + teknis |
| 4. Mobilisasi | Kick-off, bridging document, induksi dengan pre/post-test, kartu akses ber-QR, izin mulai kerja |
| 5. Pelaksanaan | Izin kerja (kerja panas, ruang terbatas, ketinggian, LOTO, umum) dengan daftar periksa, uji gas, titik isolasi, maksimal satu shift |
| 6. Evaluasi | Dasbor monitoring: kuadran skor PQ × LTIFR, corong CSMS, tren izin kerja, kartu skor dan saran per rekanan |

Dua panel:

- **Owner** di `/app/{owner}` untuk tim HSE, pengadaan, pemilik pekerjaan, pengawas, dan pimpinan. Satu instalasi dapat melayani banyak owner (SaaS per owner).
- **Portal kontraktor** di `/kontraktor/{rekanan}` (gratis untuk rekanan). Satu akun rekanan dapat bekerja untuk banyak owner; dokumen, statistik K3, dan data pekerja cukup diisi sekali.

Setiap halaman punya tombol **Bantuan** (konsep, cara mengisi, contoh isian, tangkapan layar) dan menu **Panduan** yang memuat semuanya.

## Teknologi

Laravel 13, Filament 5, PHP 8.3+, MySQL 8 atau MariaDB 10.6+ (SQLite untuk pengembangan). Tanpa Node.js dan tanpa build step: grafik memakai Chart.js dari berkas statis `public/js/rekana.js`. QR memakai `chillerlan/php-qrcode`.

## Menjalankan secara lokal

```bash
composer install
cp .env.example .env            # untuk lokal: DB_CONNECTION=sqlite, APP_ENV=local, APP_DEBUG=true
php artisan key:generate
php artisan migrate --seed
php artisan rekana:demo         # memuat tiga owner contoh simulasi
php artisan serve
```

Buka `http://127.0.0.1:8000`. Halaman depan menjelaskan Rekana dan menyediakan tombol **Masuk sebagai tamu** untuk tiap data contoh, baik sebagai owner maupun sebagai rekanan.

## Data contoh simulasi

Semua data contoh adalah **simulasi untuk keperluan demo**. Profil perusahaan (nama, alamat, jenis fasilitas) diambil dari informasi publik; nama rekanan, staf, pekerja, skor, dan aktivitas adalah rekaan. Setiap layar, cetakan sertifikat, dan kartu akses milik owner contoh diberi label **DATA SIMULASI**.

| Owner | Slug | Isi |
|---|---|---|
| Garudafood · Business Unit Gresik | `garudafood-gresik` | Pabrik biskuit: 14 rekanan, overhaul oven, silo tepung, PLTS atap, sanitasi malam, forklift, kantin, induksi GMP dan halal, uji gas ruang terbatas, rekanan gagal butir kritis |
| MedcoEnergi | `medco-corridor` | Hulu migas: workover sumur, inspeksi separator, H2S, SIMOPS, well control |
| Pakuwon Jati | `pakuwon-surabaya` | Konstruksi gedung: struktur mall, gondola fasad, tower crane, perancah, rekanan daftar hitam |

Akun contoh (kata sandi: `REKANA_DEMO_PASSWORD`, bawaan `Demo#2026rekana`):

- Owner: `admin@`, `pimpinan@`, `pemilik@`, `pengadaan@`, `evaluator@`, `pengawas@`, `auditor@` diikuti domain owner, yaitu `garudafood.rekana.test`, `medco.rekana.test`, atau `pakuwon.rekana.test`.
- Rekanan: `admin@<kode-rekanan>.<domain-owner>`, misalnya `admin@mesin.garudafood.rekana.test`.
- Tamu (hanya lihat): tombol **Masuk sebagai tamu** di halaman depan atau login. Matikan dengan `REKANA_DEMO_GUEST=false`.

Perintah terkait:

```bash
php artisan rekana:demo garudafood-gresik --force   # muat ulang satu owner contoh
php artisan rekana:remove-demo --force              # hapus semua owner, rekanan, dan akun contoh
```

## Memulai owner sungguhan

```bash
php artisan rekana:owner ajinomoto-mojokerto --name="Ajinomoto Indonesia · Pabrik Mojokerto" --email=hse@contoh.co.id
```

Administrator owner lalu masuk di `/app/ajinomoto-mojokerto` dan mengikuti menu **Panduan**: area kerja, pengguna dan peran, salin template PQ, registrasi rekanan, dan seterusnya.

## Perintah operasional

| Perintah | Fungsi | Jadwal |
|---|---|---|
| `rekana:expire-cards` | Kartu akses yang lewat masa berlaku menjadi Kedaluwarsa | Harian 00.10 |
| `rekana:reminders` | Notifikasi sertifikat CSMS rekanan dan dokumen perusahaan yang habis dalam 60 hari | Harian 07.00 |
| `rekana:doctor` | Pemeriksaan server: PHP, ekstensi, database, izin folder, cron | Manual |
| `rekana:owner` | Buat owner baru beserta administrator pertamanya | Manual |
| `rekana:demo`, `rekana:remove-demo` | Muat atau hapus data contoh | Manual |

Cukup satu cron per menit: `php artisan schedule:run`. Lihat [PANDUAN-CPANEL.md](PANDUAN-CPANEL.md).

## Aturan bisnis yang dapat disesuaikan

Semua ambang ada di `config/rekana.php`, antara lain: matriks risiko (`medium_from` 6, `high_from` 15), bahaya pemaksa Tinggi, ambang skor PQ (Rendah 35%, Menengah 55%, Tinggi 75%), masa berlaku sertifikat, rubrik HSE Plan, batas uji gas ruang terbatas (O2 19,5–23,5%, LEL ≤ 10%, H2S ≤ 10 ppm, CO ≤ 25 ppm), durasi izin maksimal 12 jam, ambang dasbor (LTIFR 1,5; TRIR 3,0), dan topik induksi. Angka bawaan adalah usulan rancangan, bukan kutipan regulasi; sesuaikan dengan pedoman CSMS yang dipakai owner.

## Struktur kode

- `app/Services`: aturan bisnis per fase (`RiskAssessor`, `PrequalificationService`, `TenderService`, `MobilizationService`, `PermitService`) dan penyusun dasbor.
- `app/Filament/App` dan `app/Filament/Kontraktor`: resource dan halaman kedua panel.
- `app/Models/Concerns/BelongsToOwner.php`: isolasi data per owner (global scope dan pemeriksaan induk lintas owner).
- `resources/help/halaman.php`: isi Bantuan per halaman; tangkapan layar di `public/img/bantuan`.
- `database/seeders`: template PQ bawaan dan skenario data contoh (`DemoScenario` sebagai mesin, satu kelas per owner).

## Pengujian

```bash
php artisan test
```

Uji mencakup render semua halaman owner dan portal (admin, tamu, rekanan), isolasi owner dan portal rekanan, skor PQ dan butir kritis, empat mata, kelayakan undangan seleksi, pemblokir kartu akses dan kedaluwarsa harian, aturan izin kerja dan uji gas, serta perintah operasional.
