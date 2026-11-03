<?php

/*
 * Isi tombol Bantuan per halaman Rekana.
 *
 * Kunci: "<panel>.<halaman>", dengan panel "app" (owner) atau "kontraktor" (portal rekanan) dan
 * halaman = slug resource ("izin-kerja"), slug resource + ".edit"/".view" untuk halaman rinci,
 * atau slug halaman khusus ("dasbor-rekanan"). Setiap entri:
 *  - judul, ringkas : apa halaman ini dan kapan dipakai;
 *  - fase           : fase IOGP 423 yang dilayani halaman ini (opsional);
 *  - konsep         : [istilah, penjelasan] yang perlu dipahami sebelum mengisi;
 *  - langkah        : urutan cara input data;
 *  - isian          : [kolom, penjelasan, contoh pengisian];
 *  - gambar         : keterangan tangkapan layar (berkas public/img/bantuan/<panel>-<halaman>.png);
 *  - tips           : kesalahan umum dan saran.
 *
 * Angka ambang (skor PQ, batas gas, durasi izin) mengikuti config/rekana.php dan dapat diubah per instalasi.
 */

$pq = config('rekana.pq.thresholds');
$gas = config('rekana.gas_limits');
$maxHours = config('rekana.permit_max_hours');

return [

    /* =============================================================== PANEL OWNER */

    'app.dasbor-rekanan' => [
        'judul' => 'Dasbor Monitoring',
        'ringkas' => 'Ringkasan seluruh rekanan dalam satu layar untuk keputusan cepat: siapa yang aman dipakai, siapa yang perlu ditinjau, dokumen apa yang segera kedaluwarsa, dan izin kerja apa yang sedang berjalan. Semua angka dihitung langsung dari data prakualifikasi, kontrak, izin kerja, dan statistik K3 yang dilaporkan rekanan.',
        'konsep' => [
            ['LTIFR', 'Lost Time Injury Frequency Rate: jumlah kecelakaan hilang waktu per 1.000.000 jam kerja. Dihitung dari statistik K3 tiga tahun terakhir yang diisi rekanan. Ambang peringatan Rekana: '.config('rekana.dashboard.ltifr_alert').'.'],
            ['TRIR', 'Total Recordable Incident Rate: semua kasus tercatat (hilang waktu, perawatan medis, kerja terbatas) per 200.000 jam kerja. Ambang peringatan: '.config('rekana.dashboard.trir_alert').'.'],
            ['Kuadran keputusan', 'Grafik gelembung yang memetakan skor PQ (sumbu tegak) terhadap LTIFR (sumbu datar). Kiri atas = andalkan; kanan atas = skor bagus tetapi insiden tinggi, minta rencana perbaikan; kiri bawah = bina; kanan bawah = tinjau ulang kelayakan. Ukuran gelembung = jumlah izin kerja 90 hari terakhir.'],
            ['Corong CSMS', 'Jumlah rekanan di setiap tahap: terdaftar, PQ diajukan, lulus PQ, berkontrak, bekerja (punya izin aktif). Corong yang menyempit tajam di satu tahap menunjukkan hambatan proses.'],
            ['Kartu skor rekanan', 'Tabel per rekanan dengan skor PQ, kategori risiko maksimum sertifikat, LTIFR, TRIR, izin dibatalkan, dan saran tindakan. Warna saran: hijau aman, kuning pantau, merah tinjau.'],
        ],
        'langkah' => [
            'Mulai dari baris kartu angka di atas: perhatikan kartu "Perlu keputusan" dan "Kedaluwarsa ≤ 60 hari".',
            'Baca panel "Keputusan yang menunggu Anda": PQ yang menunggu persetujuan, izin kerja yang menunggu tanda tangan, kontrak yang menunggu izin mulai kerja. Klik tautan untuk langsung membuka halaman tindakannya.',
            'Lihat kuadran keputusan. Rekanan di kanan bawah perlu dipanggil atau dibatasi; rekanan di kanan atas perlu rencana perbaikan K3.',
            'Periksa tren izin kerja 12 minggu dan izin per rekanan untuk melihat beban kerja berisiko di lokasi.',
            'Gulir ke kartu skor untuk saran per rekanan, lalu klik nama rekanan untuk membuka profilnya.',
        ],
        'gambar' => 'Dasbor owner pada data demo Garudafood: kartu angka, keputusan menunggu, dan kuadran skor PQ terhadap LTIFR.',
        'tips' => [
            'Dasbor tidak memerlukan input. Bila sebuah grafik kosong, berarti datanya belum ada: misalnya belum ada rekanan yang mengisi statistik K3 atau belum ada izin kerja.',
            'LTIFR rekanan kecil dengan jam kerja sedikit mudah melonjak oleh satu kasus. Baca bersama jumlah jam kerja di profil rekanan sebelum memutuskan.',
            'Untuk rapat P2K3 bulanan, cukup tampilkan halaman ini dan bahas tiga hal: keputusan tertunda, rekanan di kuadran merah, dan dokumen yang akan kedaluwarsa.',
        ],
    ],

    'app.area' => [
        'judul' => 'Area Kerja',
        'ringkas' => 'Daftar area di lokasi owner beserta kelas bahayanya. Area dipakai oleh rencana pekerjaan, induksi, dan izin kerja, sehingga aturan khusus area ikut terbawa ke setiap izin.',
        'fase' => 'Persiapan (sebelum fase 1)',
        'konsep' => [
            ['Kelas area', 'Umum (kantor, parkir), Produksi pangan (GMP dan halal: aturan benda asing, kaca dan plastik keras), Terbatas (hanya orang berwenang), dan Bahaya (amonia, boiler, gas). Kerja panas di area Bahaya otomatis mewajibkan uji gas sebelum izin diajukan.'],
            ['Kode area', 'Singkatan pendek yang dicetak di kartu akses dan izin kerja, sehingga petugas lapangan cepat membaca lokasi.'],
        ],
        'langkah' => [
            'Klik "Buat Area kerja".',
            'Isi kode singkat dan nama area yang dikenal di lapangan.',
            'Pilih kelas area sesuai bahaya terbesar di area tersebut.',
            'Tulis aturan khusus di keterangan, misalnya APD tambahan, jam kerja terbatas, atau larangan membawa kaca.',
            'Simpan. Ulangi untuk setiap area utama; cukup 6–12 area agar mudah dipilih.',
        ],
        'isian' => [
            ['Kode', 'Singkatan unik per owner, huruf kapital.', 'PRD, UTL, IPL'],
            ['Nama area', 'Nama yang dipakai di lapangan.', 'Utilitas: boiler biomassa dan kompresor'],
            ['Kelas area', 'Umum, Produksi pangan, Terbatas, atau Bahaya.', 'Bahaya untuk ruang mesin amonia'],
            ['Keterangan dan aturan khusus', 'Aturan yang wajib diketahui pekerja kontraktor di area ini.', 'Wajib earplug; dilarang kerja panas tanpa uji LEL; titik kumpul TK-2.'],
        ],
        'gambar' => 'Daftar area demo Garudafood Gresik dengan kelas bahaya berwarna.',
        'tips' => [
            'Jangan membuat area terlalu rinci (per mesin). Rinciannya cukup ditulis di kolom "Lokasi rinci" pada izin kerja.',
        ],
    ],

    'app.template-pq' => [
        'judul' => 'Template Kuesioner PQ',
        'ringkas' => 'Kumpulan pertanyaan prakualifikasi (PQ) yang dijawab rekanan untuk membuktikan sistem manajemen K3-nya. Rekana menyediakan template bawaan; owner dapat menyalin dan menyesuaikannya.',
        'fase' => 'Fase 2: Prakualifikasi',
        'konsep' => [
            ['Bobot', 'Seberapa penting sebuah butir (1–3). Skor PQ = jumlah (bobot × skor butir) dibagi jumlah (bobot × 4), dalam persen.'],
            ['Butir kritis', 'Butir yang tidak boleh bernilai 0. Bila evaluator memberi 0 pada butir kritis, PQ gagal berapa pun skor totalnya. Contoh: kepesertaan BPJS Ketenagakerjaan, kebijakan K3 bertanda tangan pimpinan.'],
            ['Ambang kategori', "Skor minimum untuk boleh mengerjakan pekerjaan berisiko: Rendah {$pq['rendah']}%, Menengah {$pq['menengah']}%, Tinggi {$pq['tinggi']}%."],
            ['Template bawaan', 'Template milik Rekana (ISO 45001 dan SMK3 untuk pangan, konstruksi gedung, migas) dapat dipakai semua owner tetapi tidak dapat diubah. Gunakan tombol "Salin untuk disesuaikan".'],
        ],
        'langkah' => [
            'Pilih template yang paling dekat dengan industri Anda, lalu klik "Salin untuk disesuaikan".',
            'Buka salinan, ubah nama, dan tambah atau hapus butir sesuai pedoman CSMS internal.',
            'Tandai butir kritis hanya untuk hal yang benar-benar menggugurkan (biasanya 3–6 butir).',
            'Simpan. Template siap dipilih saat memulai prakualifikasi di halaman Rekanan.',
        ],
        'gambar' => 'Daftar template: template bawaan Rekana dan salinan milik owner.',
        'tips' => [
            'Template yang sudah dipakai PQ berjalan sebaiknya tidak diubah besar-besaran; buat versi baru agar penilaian lama tetap dapat ditelusuri.',
        ],
    ],

    'app.rekanan' => [
        'judul' => 'Daftar Rekanan',
        'ringkas' => 'Daftar perusahaan kontraktor yang terdaftar pada owner Anda beserta statusnya: menunggu verifikasi, aktif, atau daftar hitam. Dari sini owner memulai prakualifikasi.',
        'fase' => 'Fase 2: Prakualifikasi',
        'konsep' => [
            ['Registrasi', 'Hubungan satu rekanan dengan satu owner. Satu perusahaan kontraktor dapat bekerja untuk banyak owner di Rekana dengan satu akun portal; dokumen dan statistiknya cukup diisi sekali.'],
            ['Verifikasi', 'Pemeriksaan legalitas (NIB, NPWP, SBU) sebelum rekanan boleh mengikuti PQ.'],
            ['Daftar hitam', 'Rekanan yang dilarang bekerja karena pelanggaran K3 berat. Semua sertifikat aktif ikut tidak berlaku dan rekanan tidak dapat diundang seleksi.'],
        ],
        'langkah' => [
            'Buka rekanan berstatus "Menunggu verifikasi", periksa dokumen legalitas pada tab Dokumen, lalu klik "Verifikasi / aktifkan".',
            'Klik "Mulai prakualifikasi", pilih template kuesioner dan kategori risiko yang dituju (Rendah, Menengah, atau Tinggi) sesuai pekerjaan yang akan diberikan.',
            'Rekanan menerima PQ di portalnya. Pantau statusnya di menu Prakualifikasi.',
            'Bila terjadi pelanggaran berat, gunakan "Masukkan daftar hitam" dan tulis alasannya. Tindakan ini tercatat di log audit.',
        ],
        'isian' => [
            ['Template kuesioner', 'Template PQ yang dipakai.', 'CSMS ISO 45001 + SMK3 Industri Pangan'],
            ['Kategori risiko yang dituju', 'Kategori tertinggi yang ingin dicapai rekanan; menentukan ambang skor kelulusan.', 'Tinggi untuk kontraktor mesin yang akan mengelas di area produksi'],
            ['Alasan (daftar hitam)', 'Fakta singkat dan rujukan laporan investigasi.', 'Fatality 12 Mar 2026, laporan investigasi INV-2026-004'],
        ],
        'gambar' => 'Daftar rekanan dengan status registrasi dan tombol tindakan.',
        'tips' => [
            'Kategori risiko yang dituju sebaiknya mengikuti rencana pekerjaan nyata. Meminta semua rekanan lulus "Tinggi" membuat rekanan kecil seperti katering tidak pernah lulus.',
        ],
    ],

    'app.prakualifikasi' => [
        'judul' => 'Prakualifikasi (PQ)',
        'ringkas' => 'Semua prakualifikasi rekanan dan statusnya: draf, diajukan, klarifikasi, dievaluasi, disetujui, atau ditolak. Klik satu baris untuk menilai jawaban.',
        'fase' => 'Fase 2: Prakualifikasi',
        'konsep' => [
            ['Alur status', 'Draf (rekanan mengisi) → Diajukan → (Klarifikasi bila kurang lengkap) → Dievaluasi (skor dihitung) → Disetujui (sertifikat terbit) atau Ditolak.'],
            ['Empat mata', 'Evaluator yang menghitung skor tidak boleh sekaligus menyetujui. Persetujuan dilakukan orang lain dengan peran Pimpinan atau Administrator.'],
            ['Kunjungan lapangan', 'Untuk target kategori Tinggi, evaluator sebaiknya mengunjungi kantor atau proyek rekanan dan mencatat temuan sebelum menilai.'],
        ],
        'langkah' => [
            'Saring status "Diajukan" untuk melihat PQ yang menunggu penilaian.',
            'Klik baris PQ untuk membuka halaman penilaian.',
            'Kolom "Diajukan" menunjukkan berapa lama PQ menunggu; usahakan selesai dalam 14 hari.',
        ],
        'gambar' => 'Daftar PQ dengan saringan status dan skor.',
        'tips' => [
            'PQ berstatus Klarifikasi kembali ke rekanan; mereka melihat pesan Anda di portal dan dapat mengajukan ulang.',
        ],
    ],

    'app.prakualifikasi.edit' => [
        'judul' => 'Menilai Prakualifikasi',
        'ringkas' => 'Halaman kerja evaluator HSE: membaca jawaban dan bukti rekanan, memberi skor per butir, lalu mengirim hasil ke persetujuan.',
        'fase' => 'Fase 2: Prakualifikasi',
        'konsep' => [
            ['Skor butir 0–4', '0 = tidak ada; 1 = ada tetapi tidak memadai; 2 = memadai sebagian; 3 = memadai dan diterapkan; 4 = diterapkan dan dievaluasi berkala dengan bukti.'],
            ['Skor mandiri', 'Skor yang diberikan rekanan sendiri. Hanya sebagai pembanding; yang dihitung adalah skor evaluator.'],
            ['Hasil kategori', "Skor ≥ {$pq['tinggi']}% → boleh pekerjaan Tinggi; ≥ {$pq['menengah']}% → Menengah; ≥ {$pq['rendah']}% → Rendah. Butir kritis bernilai 0 → ditolak."],
            ['Sertifikat CSMS', 'Terbit otomatis saat PQ disetujui, berlaku '.config('rekana.pq.certificate_years').' tahun, mencantumkan kategori risiko maksimum, dan dapat diverifikasi lewat kode QR.'],
        ],
        'langkah' => [
            'Bila ada kunjungan lapangan, isi tanggal dan catatannya di bagian atas.',
            'Untuk setiap butir: baca jawaban, buka dokumen atau bukti yang dilampirkan, pilih skor evaluator, dan tulis catatan bila skor berbeda dari skor mandiri.',
            'Bila bukti kurang, klik "Minta klarifikasi" dan tuliskan dengan jelas apa yang harus dilengkapi.',
            'Bila semua butir sudah dinilai, klik "Hitung skor dan kirim ke persetujuan" dan tulis ringkasan penilaian.',
            'Penyetuju (orang lain) membuka halaman yang sama dan klik "Setujui dan terbitkan sertifikat".',
        ],
        'isian' => [
            ['Skor evaluator', 'Nilai 0–4 berdasarkan bukti, bukan pengakuan.', '3: prosedur LOTO ada, ditandatangani, dan ada catatan pelatihan 2026'],
            ['Catatan evaluator', 'Alasan singkat, terutama untuk skor rendah.', 'Statistik 2024 tidak mencantumkan jam kerja subkontraktor'],
            ['Hal yang perlu dilengkapi rekanan', 'Pesan klarifikasi yang dikirim ke portal rekanan.', 'Lampirkan sertifikat SIO operator crane yang masih berlaku'],
            ['Ringkasan penilaian', 'Kesimpulan untuk penyetuju.', 'Sistem matang; kelemahan di pengawasan subkontraktor, direkomendasikan Menengah'],
        ],
        'gambar' => 'Halaman penilaian PQ: jawaban rekanan, bukti, skor mandiri, dan skor evaluator per butir.',
        'tips' => [
            'Jangan memberi skor 4 tanpa bukti evaluasi berkala (audit internal, tinjauan manajemen).',
            'Penolakan karena butir kritis tidak bisa ditutupi oleh skor tinggi di butir lain; ini disengaja.',
        ],
    ],

    'app.sertifikat' => [
        'judul' => 'Sertifikat CSMS',
        'ringkas' => 'Sertifikat yang terbit dari PQ yang disetujui. Sertifikat menentukan kategori risiko pekerjaan yang boleh diberikan ke rekanan dan berlaku '.config('rekana.pq.certificate_years').' tahun.',
        'fase' => 'Fase 2: Prakualifikasi',
        'konsep' => [
            ['Kategori maksimum', 'Rekanan hanya dapat diundang seleksi untuk pekerjaan dengan kategori risiko sama atau di bawah kategori sertifikatnya.'],
            ['Verifikasi QR', 'Setiap sertifikat cetak memuat QR yang membuka halaman verifikasi publik, sehingga satpam atau mitra dapat memastikan keasliannya.'],
            ['Pencabutan', 'Sertifikat dapat dicabut sebelum habis masa berlaku, misalnya setelah insiden berat. Rekanan tidak lagi memenuhi syarat sampai PQ ulang.'],
        ],
        'langkah' => [
            'Klik "Cetak" untuk membuka sertifikat siap cetak atau simpan sebagai PDF dari peramban.',
            'Pantau kolom masa berlaku; sertifikat yang habis dalam 60 hari juga tampil di dasbor.',
            'Bila perlu, klik "Cabut" dan tulis alasannya.',
        ],
        'gambar' => 'Daftar sertifikat dengan kategori maksimum dan masa berlaku.',
        'tips' => [
            'Mulai PQ ulang sekitar dua bulan sebelum sertifikat habis agar rekanan tidak terhenti di tengah kontrak.',
        ],
    ],

    'app.rencana-pekerjaan' => [
        'judul' => 'Rencana Pekerjaan & Risiko',
        'ringkas' => 'Titik awal setiap pekerjaan kontraktor. User (pemilik pekerjaan) menuliskan lingkup dan aktivitas, Rekana menghitung kategori risiko dan menentukan kewajiban CSMS yang berlaku.',
        'fase' => 'Fase 1: Penilaian Risiko',
        'konsep' => [
            ['Matriks 5×5', 'Nilai risiko per aktivitas = kemungkinan (1–5) × keparahan (1–5). Nilai ≥ '.config('rekana.risk.high_from').' = Tinggi, ≥ '.config('rekana.risk.medium_from').' = Menengah, di bawahnya Rendah. Kategori pekerjaan = aktivitas dengan risiko tertinggi.'],
            ['Bahaya pemaksa Tinggi', 'Ruang terbatas, amonia, kerja panas, ketinggian, H2S, dan intervensi sumur langsung menjadikan pekerjaan Tinggi, berapa pun nilai matriksnya.'],
            ['Mode kontrak IOGP', 'Mode 1: owner mengelola K3 dan kontraktor mengikuti. Mode 2: kontraktor memakai sistem K3-nya tetapi tunduk pada aturan owner. Mode 3: kontraktor mengelola penuh (biasanya proyek terpisah). Mode menentukan kebutuhan bridging document.'],
            ['Kewajiban', 'Setelah dinilai, Rekana menampilkan kewajiban: kategori sertifikat minimum, HSE Plan, kick-off meeting, bridging document, induksi, dan pengawasan.'],
        ],
        'langkah' => [
            'Klik "Buat Rencana pekerjaan". Isi judul, area, mode kontrak, rencana tanggal, perkiraan jumlah pekerja, dan lingkup.',
            'Tambahkan baris aktivitas: satu baris untuk setiap kegiatan berbahaya, pilih jenis bahaya, kemungkinan, keparahan, dan pengendalian awal.',
            'Simpan, lalu klik "Nilai risiko". Rekana menampilkan kategori risiko dan daftar kewajiban.',
            'Bila pekerjaan dilelang, klik "Buka seleksi" untuk membuat paket seleksi dengan batas unggah HSE Plan dan bobot HSE.',
        ],
        'isian' => [
            ['Judul pekerjaan', 'Nama singkat yang dipakai di kontrak dan izin.', 'Overhaul oven tunnel lini biskuit 2'],
            ['Mode kontrak IOGP', 'Siapa yang mengelola sistem K3 di lokasi.', 'Mode 2 untuk kontraktor mesin bersertifikat ISO 45001'],
            ['Aktivitas', 'Satu kegiatan berbahaya per baris.', 'Pengelasan rangka conveyor di dalam ruang produksi'],
            ['Bahaya', 'Jenis bahaya utama aktivitas.', 'Kerja panas (las, gerinda, potong)'],
            ['Kemungkinan / Keparahan', 'Skala 1–5 sebelum pengendalian tambahan.', 'Kemungkinan 3, keparahan 4 → nilai 12 (Menengah), tetapi kerja panas memaksa Tinggi'],
            ['Pengendalian', 'Pengendalian awal yang direncanakan.', 'Fire blanket, APAR, fire watch, izin kerja panas'],
        ],
        'gambar' => 'Daftar rencana pekerjaan dengan kategori risiko hasil penilaian.',
        'tips' => [
            'Pisahkan aktivitas berbeda bahaya ke baris berbeda; menggabungkannya menyembunyikan bahaya tertinggi.',
            'Bila ada subkontraktor, aktifkan "Ada subkontraktor" agar kewajiban pengawasan subkontraktor ikut tercantum.',
        ],
    ],

    'app.seleksi' => [
        'judul' => 'Seleksi Rekanan',
        'ringkas' => 'Paket seleksi dari rencana pekerjaan. Hanya rekanan dengan sertifikat CSMS yang masih berlaku dan kategori cukup yang dapat diundang. Nilai akhir menggabungkan nilai HSE Plan dan nilai teknis.',
        'fase' => 'Fase 3: Seleksi',
        'konsep' => [
            ['Syarat undangan', 'Sertifikat aktif, kategori maksimum ≥ kategori pekerjaan, dan registrasi tidak di daftar hitam.'],
            ['HSE Plan', 'Rencana K3 khusus proyek yang diunggah rekanan dan dinilai dengan rubrik 0–4 per kriteria: organisasi, risiko (JSA/HIRADC), kompetensi, peralatan, tanggap darurat, dan pengendalian khusus lokasi.'],
            ['Ambang lulus HSE', 'Penawaran dengan nilai HSE Plan di bawah ambang gugur, berapa pun harganya.'],
        ],
        'langkah' => [
            'Buka paket seleksi, lalu klik "Undang rekanan". Daftar hanya berisi rekanan yang memenuhi syarat.',
            'Rekanan mengunggah HSE Plan di portal sebelum batas waktu.',
            'Pada tab Penawaran, klik "Nilai HSE Plan" untuk setiap rekanan: beri skor per kriteria, nilai teknis, dan harga.',
            'Klik "Tetapkan pemenang" pada penawaran terbaik, isi nomor kontrak, tanggal, dan nilai. Kontrak terbentuk otomatis.',
        ],
        'gambar' => 'Daftar paket seleksi dan status masing-masing.',
        'tips' => [
            'Bila tidak ada rekanan yang muncul di daftar undangan, periksa apakah sertifikat mereka masih berlaku dan kategorinya cukup.',
        ],
    ],

    'app.seleksi.view' => [
        'judul' => 'Rincian Seleksi',
        'ringkas' => 'Rincian satu paket seleksi: rekanan yang diundang, HSE Plan yang masuk, penilaian, dan penetapan pemenang.',
        'fase' => 'Fase 3: Seleksi',
        'konsep' => [
            ['Nilai akhir', 'Nilai akhir = bobot HSE × nilai HSE Plan + (100% − bobot HSE) × nilai teknis. Bobot ditetapkan saat membuka seleksi.'],
        ],
        'langkah' => [
            'Klik "Undang rekanan" dan pilih rekanan yang memenuhi syarat.',
            'Setelah HSE Plan masuk, klik "Nilai HSE Plan" di baris penawaran dan isi skor tiap kriteria, nilai teknis 0–100, harga, dan catatan.',
            'Klik "Tetapkan pemenang" pada penawaran yang lulus ambang dengan nilai akhir terbaik.',
        ],
        'isian' => [
            ['Nilai teknis (0-100)', 'Nilai dari tim teknis atau pengadaan.', '82'],
            ['Harga penawaran (Rp)', 'Untuk catatan; tidak masuk rumus nilai akhir.', '1.250.000.000'],
            ['Nomor kontrak', 'Nomor dokumen kontrak/PO.', 'GF-GRS/PO/2026/0418'],
        ],
        'gambar' => 'Rincian paket seleksi dengan tabel penawaran dan nilai HSE Plan.',
    ],

    'app.kontrak' => [
        'judul' => 'Kontrak & Mobilisasi',
        'ringkas' => 'Kontrak aktif dengan rekanan dan kesiapan mobilisasinya: kick-off, bridging document, induksi, kartu akses, hingga izin mulai kerja.',
        'fase' => 'Fase 4: Mobilisasi',
        'konsep' => [
            ['Kick-off meeting', 'Pertemuan awal owner–kontraktor untuk menyepakati HSE Plan, jalur komunikasi, dan aturan lokasi. Wajib untuk pekerjaan Tinggi, atau Menengah dengan Mode 2.'],
            ['Bridging document', 'Dokumen yang menjembatani sistem K3 owner dan kontraktor: siapa menerbitkan izin, siapa menginvestigasi insiden, prosedur mana yang dipakai. Wajib pada kondisi yang sama dengan kick-off.'],
            ['Izin mulai kerja', 'Gerbang terakhir sebelum kontrak boleh mengajukan izin kerja harian. Diberikan setelah kick-off, induksi, dan kartu akses terpenuhi.'],
        ],
        'langkah' => [
            'Buka kontrak baru hasil seleksi (atau buat langsung bila tanpa seleksi).',
            'Klik "Catat kick-off": isi notulen dan, bila wajib, bridging document.',
            'Jadwalkan induksi untuk pekerja kontraktor di menu Induksi.',
            'Klik "Terbitkan kartu akses". Kartu hanya terbit untuk pekerja yang lulus induksi dan surat sehatnya berlaku.',
            'Klik "Izinkan mulai kerja". Bila ada syarat belum lengkap, Rekana menampilkan daftarnya.',
            'Gunakan "Cetak kartu akses" untuk mencetak semua kartu kontrak sekaligus.',
        ],
        'isian' => [
            ['Notulen kick-off', 'Kesepakatan utama.', 'Jam kerja 07.00–16.00; izin kerja panas ditandatangani supervisor HSE GF; titik kumpul TK-2'],
            ['Bridging document', 'Pembagian tanggung jawab K3.', 'Izin kerja: sistem owner. Investigasi insiden: bersama, dipimpin owner. APD: disediakan kontraktor sesuai standar owner.'],
        ],
        'gambar' => 'Daftar kontrak dengan status mobilisasi dan kemajuan pekerjaan.',
        'tips' => [
            'Masa berlaku kartu akses = tanggal paling awal di antara akhir kontrak, surat sehat, dan masa berlaku induksi.',
        ],
    ],

    'app.induksi' => [
        'judul' => 'Induksi & Orientasi',
        'ringkas' => 'Catatan sesi induksi K3 untuk pekerja kontraktor sebelum masuk lokasi, lengkap dengan topik, peserta, nilai pre-test dan post-test.',
        'fase' => 'Fase 4: Mobilisasi',
        'konsep' => [
            ['Kelulusan', 'Peserta lulus bila nilai post-test ≥ nilai lulus. Hanya pekerja lulus yang dapat diberi kartu akses.'],
            ['Masa berlaku', 'Induksi berlaku sejumlah bulan tertentu (biasanya 6 atau 12). Setelah habis, pekerja perlu induksi ulang.'],
        ],
        'langkah' => [
            'Klik "Buat Induksi", isi judul, tanggal, kontrak, area, dan pemateri.',
            'Tentukan nilai lulus post-test dan masa berlaku dalam bulan.',
            'Centang topik yang dibawakan.',
            'Tambahkan peserta: pilih pekerja kontraktor, isi nilai pre-test dan post-test.',
            'Simpan. Status lulus dihitung otomatis.',
        ],
        'isian' => [
            ['Nilai lulus post-test', 'Ambang lulus 0–100.', '70'],
            ['Berlaku (bulan)', 'Masa berlaku induksi.', '12'],
            ['Topik', 'Materi yang dibawakan.', 'K3 umum, bahaya amonia, GMP dan halal, izin kerja dan LOTO'],
        ],
        'gambar' => 'Daftar sesi induksi dengan jumlah peserta dan kelulusan.',
        'tips' => [
            'Untuk pabrik pangan, selalu sertakan topik keamanan pangan, kaca dan plastik keras, serta halal.',
        ],
    ],

    'app.kartu-akses' => [
        'judul' => 'Kartu Akses Pekerja',
        'ringkas' => 'Kartu identitas pekerja kontraktor dengan QR verifikasi. Satpam memindai QR di gerbang untuk memastikan kartu masih aktif.',
        'fase' => 'Fase 4: Mobilisasi',
        'konsep' => [
            ['Masa berlaku', 'Tanggal paling awal di antara akhir kontrak, surat sehat, dan induksi. Kartu otomatis kedaluwarsa setelahnya.'],
            ['Blokir', 'Menonaktifkan kartu seketika, misalnya karena pelanggaran aturan keselamatan. Pekerja tidak dapat dimasukkan ke izin kerja.'],
        ],
        'langkah' => [
            'Kartu diterbitkan dari halaman Kontrak. Di sini owner mencetak ulang atau memblokir.',
            'Klik "Cetak" untuk mencetak satu kartu.',
            'Klik "Blokir" dan tulis alasannya bila pekerja melanggar.',
        ],
        'gambar' => 'Daftar kartu akses dengan masa berlaku dan status.',
    ],

    'app.izin-kerja' => [
        'judul' => 'Izin Kerja (Permit to Work)',
        'ringkas' => 'Izin harian untuk pekerjaan berbahaya: kerja panas, ruang terbatas, ketinggian, LOTO, dan kerja umum. Izin mengikat kontrak, area, waktu, pekerja, daftar periksa, dan uji gas.',
        'fase' => 'Fase 5: Pelaksanaan',
        'konsep' => [
            ['Alur izin', 'Draf → Diajukan → Disetujui → Aktif (di lokasi) → Ditutup. Izin dapat dibatalkan kapan saja sebelum ditutup.'],
            ['Syarat pengajuan', 'Kontrak sudah diizinkan mulai kerja, sertifikat CSMS berlaku, semua pekerja punya kartu aktif untuk kontrak itu, daftar periksa lengkap, durasi ≤ '.$maxHours.' jam.'],
            ['Uji gas', "Wajib untuk ruang terbatas (O2 {$gas['o2_min']}–{$gas['o2_max']}%, LEL ≤ {$gas['lel_max']}%, H2S ≤ {$gas['h2s_max']} ppm, CO ≤ {$gas['co_max']} ppm) dan kerja panas di area Bahaya (LEL)."],
            ['Pemohon dan penyetuju', 'Penyetuju tidak boleh orang yang sama dengan pemohon, dan harus berperan Pengawas, Pemilik Pekerjaan, Pimpinan, atau Administrator.'],
        ],
        'langkah' => [
            'Klik "Buat Izin kerja". Pilih kontrak, jenis izin, dan area.',
            'Isi uraian pekerjaan, lokasi rinci, waktu mulai dan selesai (maksimal satu shift).',
            'Pilih pekerja yang tercakup. Hanya pekerja kontraktor itu yang tampil.',
            'Tulis langkah kerja dan bahaya, lalu centang semua butir daftar periksa setelah benar-benar diperiksa di lapangan.',
            'Untuk ruang terbatas atau kerja panas di area bahaya, isi hasil uji gas. Untuk LOTO dan ruang terbatas, tulis titik isolasi.',
            'Klik "Ajukan". Bila ada syarat kurang, Rekana menampilkan daftarnya.',
            'Penyetuju klik "Setujui", lalu pengawas di lokasi klik "Aktifkan di lokasi" saat pekerjaan dimulai.',
            'Setelah selesai dan area aman, klik "Tutup izin" dan tulis catatan penutupan.',
        ],
        'isian' => [
            ['Uraian pekerjaan', 'Pekerjaan spesifik hari ini.', 'Las ulang dudukan motor conveyor C-04'],
            ['Lokasi rinci', 'Titik tepat di dalam area.', 'Lini biskuit 2, sisi timur, dekat panel MCC-2'],
            ['Langkah kerja dan bahaya', 'Ringkasan JSA.', '1) Isolasi motor (LOTO) 2) Pasang fire blanket 3) Las 4) Fire watch 30 menit'],
            ['LEL (%)', 'Hasil detektor gas mudah terbakar.', '0'],
            ['O2 (%)', 'Kadar oksigen ruang terbatas.', '20,8'],
            ['Titik isolasi energi', 'Daftar titik isolasi dan nomor gembok.', 'MCC-2 breaker C04 (gembok #17); katup steam V-112 (gembok #18)'],
        ],
        'gambar' => 'Daftar izin kerja berwarna menurut jenis dan status.',
        'tips' => [
            'Jangan mencentang daftar periksa dari kantor. Centang di lokasi bersama pemohon.',
            'Pekerjaan lebih dari satu shift memerlukan izin baru per shift agar uji gas dan kondisi lokasi diperiksa ulang.',
        ],
    ],

    'app.pengguna' => [
        'judul' => 'Pengguna & Peran',
        'ringkas' => 'Daftar pengguna internal owner dan perannya. Peran menentukan siapa boleh menilai PQ, menyetujui sertifikat, menyetujui izin kerja, atau hanya melihat.',
        'fase' => 'Pengaturan',
        'konsep' => [
            ['Peran', 'Administrator Sistem (semua), Pimpinan (persetujuan akhir), User/Pemilik Pekerjaan (rencana pekerjaan dan izin), Pengadaan (seleksi dan kontrak), Tim Evaluator HSE (menilai PQ dan HSE Plan), Pengawas Lapangan (izin kerja), Pengamat (baca saja).'],
            ['Area tanggung jawab', 'Opsional; membatasi fokus pengawas pada satu area.'],
        ],
        'langkah' => [
            'Klik "Buat Pengguna", isi nama dan surel. Bila surel sudah terdaftar, akun yang ada ditautkan.',
            'Pilih peran dan jabatan.',
            'Nonaktifkan pengguna yang pindah tugas alih-alih menghapusnya, agar jejak audit tetap utuh.',
        ],
        'isian' => [
            ['Surel', 'Alamat surel kerja untuk masuk.', 'hse.officer@garudafood.example'],
            ['Peran', 'Wewenang di Rekana.', 'Pengawas Lapangan (HSE Officer)'],
            ['Jabatan', 'Jabatan di organisasi.', 'HSE Officer Utilitas'],
        ],
        'gambar' => 'Daftar pengguna owner dengan peran berwarna.',
    ],

    'app.log-audit' => [
        'judul' => 'Log Audit',
        'ringkas' => 'Jejak semua tindakan penting: siapa menyetujui PQ, mencabut sertifikat, menyetujui izin, atau memblokir kartu, dan kapan. Tidak dapat diubah.',
        'fase' => 'Pengaturan',
        'langkah' => [
            'Saring menurut jenis peristiwa atau pengguna.',
            'Gunakan saat audit SMK3 atau investigasi insiden untuk membuktikan siapa memutuskan apa.',
        ],
        'gambar' => 'Log audit dengan peristiwa, pengguna, dan waktu.',
    ],

    /* =========================================================== PORTAL KONTRAKTOR */

    'kontraktor.dasbor-kontraktor' => [
        'judul' => 'Dasbor Saya (Kontraktor)',
        'ringkas' => 'Ringkasan untuk perusahaan kontraktor: kontrak aktif di setiap owner, kesiapan pekerja, izin kerja minggu ini, statistik keselamatan, dan dokumen yang akan kedaluwarsa.',
        'konsep' => [
            ['Kesiapan pekerja', 'Siap = kartu akses aktif; Tertahan = ada syarat kurang (surat sehat habis, belum induksi); Cadangan = aktif di perusahaan tetapi belum ditugaskan ke kontrak.'],
            ['Daftar tugas', 'Hal yang harus dikerjakan kontraktor: PQ yang perlu dijawab, klarifikasi dari owner, undangan seleksi, dokumen yang akan kedaluwarsa.'],
            ['Satu akun banyak owner', 'Bila perusahaan Anda bekerja untuk beberapa owner yang memakai Rekana, semua kontrak tampil di sini; dokumen dan pekerja cukup diisi sekali.'],
        ],
        'langkah' => [
            'Kerjakan dulu daftar tugas di panel kanan.',
            'Periksa tabel kesiapan pekerja; pekerja tertahan tidak dapat dimasukkan ke izin kerja.',
            'Pantau kemajuan kontrak dan izin kerja per jenis untuk merencanakan personel.',
        ],
        'gambar' => 'Dasbor kontraktor demo: kontrak, kesiapan pekerja, dan tren izin kerja.',
        'tips' => [
            'Portal kontraktor gratis. Biaya langganan Rekana ditanggung owner.',
        ],
    ],

    'kontraktor.dokumen' => [
        'judul' => 'Dokumen Perusahaan',
        'ringkas' => 'Pustaka dokumen perusahaan kontraktor: legalitas, sertifikat sistem K3, prosedur, BPJS, asuransi. Dokumen dapat dilampirkan ke jawaban PQ untuk semua owner.',
        'konsep' => [
            ['Masa berlaku', 'Isi tanggal berlaku agar Rekana mengingatkan Anda dan owner sebelum dokumen kedaluwarsa.'],
        ],
        'langkah' => [
            'Klik "Buat Dokumen", pilih jenis dokumen.',
            'Isi nama, nomor, tanggal terbit, dan tanggal berlaku.',
            'Unggah berkas PDF atau JPG (maksimal '.round(config('rekana.upload_max_kb') / 1024).' MB).',
            'Simpan. Dokumen siap dipilih sebagai bukti di jawaban PQ.',
        ],
        'isian' => [
            ['Jenis dokumen', 'Kategori dokumen.', 'Sertifikat SMK3'],
            ['Nomor', 'Nomor resmi dokumen.', 'SMK3/1234/2025'],
            ['Berlaku sampai', 'Tanggal kedaluwarsa; kosongkan bila tidak berbatas.', '30-06-2028'],
        ],
        'gambar' => 'Pustaka dokumen kontraktor dengan masa berlaku.',
        'tips' => [
            'Unggah versi bertanda tangan dan berstempel. Dokumen draf biasanya dinilai 1 oleh evaluator.',
        ],
    ],

    'kontraktor.statistik-k3' => [
        'judul' => 'Statistik K3 Tahunan',
        'ringkas' => 'Data keselamatan tahunan perusahaan: jam kerja, kecelakaan fatal, kasus hilang waktu, kasus tercatat, dan hari hilang. Owner memakai data ini untuk menghitung LTIFR dan TRIR.',
        'konsep' => [
            ['Jam kerja', 'Total jam kerja seluruh pekerja (termasuk subkontraktor) dalam setahun. Perkiraan: jumlah pekerja × jam per hari × hari kerja.'],
            ['LTI', 'Kecelakaan yang membuat pekerja tidak dapat bekerja minimal satu shift berikutnya.'],
            ['TRI', 'Semua kasus tercatat: LTI + perawatan medis + kerja terbatas + fatal.'],
        ],
        'langkah' => [
            'Klik "Buat Statistik K3", isi tahun.',
            'Isi total jam kerja dan jumlah kasus per jenis.',
            'Isi untuk tiga tahun terakhir.',
        ],
        'isian' => [
            ['Total jam kerja', 'Jam kerja setahun.', '120 pekerja × 8 jam × 300 hari = 288.000'],
            ['Kasus hilang waktu (LTI)', 'Jumlah kasus.', '1'],
            ['Kasus tercatat (TRI)', 'Termasuk LTI.', '3'],
        ],
        'gambar' => 'Statistik K3 tahunan yang dilaporkan kontraktor.',
        'tips' => [
            'Melaporkan nol kasus dengan jam kerja besar tanpa bukti justru menimbulkan pertanyaan. Laporkan apa adanya; owner menilai tren dan tindakan perbaikan.',
        ],
    ],

    'kontraktor.pekerja' => [
        'judul' => 'Pekerja',
        'ringkas' => 'Data pekerja perusahaan beserta kompetensi, SIO, dan surat sehat. Owner memakai data ini untuk induksi, kartu akses, dan izin kerja.',
        'konsep' => [
            ['Surat sehat', 'Tanggal berlaku surat keterangan sehat (MCU). Bila habis, kartu akses ikut habis.'],
            ['Kompetensi', 'Sertifikat keahlian seperti SIO operator crane, juru las, K3 listrik, atau petugas ruang terbatas.'],
        ],
        'langkah' => [
            'Klik "Buat Pekerja", isi nama lengkap, NIK, jabatan, dan telepon.',
            'Isi tanggal berlaku surat sehat.',
            'Tambahkan kompetensi satu per satu dengan nomor, penerbit, dan masa berlaku.',
            'Nonaktifkan pekerja yang keluar.',
        ],
        'isian' => [
            ['NIK', 'Nomor induk kependudukan 16 digit.', '3525010101900001'],
            ['Surat sehat berlaku s.d.', 'Dari MCU terakhir.', '31-12-2026'],
            ['Nama kompetensi', 'Jenis sertifikat.', 'Juru las 3G SMAW'],
        ],
        'gambar' => 'Daftar pekerja dengan status kartu dan surat sehat.',
    ],

    'kontraktor.prakualifikasi' => [
        'judul' => 'Prakualifikasi (PQ)',
        'ringkas' => 'Kuesioner prakualifikasi dari owner. Jawab setiap butir dan lampirkan bukti, lalu ajukan. Hasilnya sertifikat CSMS yang menentukan pekerjaan apa yang dapat Anda ikuti.',
        'konsep' => [
            ['Status', 'Draf (Anda mengisi), Diajukan (owner menilai), Klarifikasi (owner meminta tambahan), Disetujui (sertifikat terbit), Ditolak.'],
        ],
        'langkah' => [
            'Klik PQ berstatus Draf atau Klarifikasi untuk mulai menjawab.',
        ],
        'gambar' => 'Daftar PQ kontraktor per owner.',
    ],

    'kontraktor.prakualifikasi.edit' => [
        'judul' => 'Menjawab Prakualifikasi',
        'ringkas' => 'Halaman menjawab kuesioner PQ. Setiap butir perlu jawaban singkat, skor mandiri, dan bukti.',
        'konsep' => [
            ['Skor mandiri 0–4', '0 tidak ada; 1 ada tetapi belum memadai; 2 memadai sebagian; 3 memadai dan diterapkan; 4 diterapkan dan dievaluasi berkala.'],
            ['Butir kritis', 'Ditandai khusus. Bila dinilai 0 oleh owner, PQ gagal. Pastikan butir ini berbukti kuat.'],
        ],
        'langkah' => [
            'Untuk setiap butir, tulis jawaban singkat yang menjelaskan apa yang Anda lakukan.',
            'Pilih skor mandiri secara jujur.',
            'Pilih dokumen pendukung dari pustaka Dokumen, atau unggah bukti baru.',
            'Simpan berkala. Setelah semua butir terisi, klik "Ajukan ke owner".',
            'Bila status kembali Klarifikasi, baca pesan owner di bagian atas, lengkapi, lalu ajukan ulang.',
        ],
        'isian' => [
            ['Jawaban', 'Penjelasan singkat dan spesifik.', 'Prosedur LOTO PRO-K3-07 rev. 3; pelatihan ulang tiap tahun, terakhir Feb 2026'],
            ['Dokumen pendukung', 'Pilih dari pustaka dokumen.', 'Prosedur kerja aman: PRO-K3-07'],
        ],
        'gambar' => 'Formulir jawaban PQ dengan skor mandiri dan bukti.',
        'tips' => [
            'Jawaban "sudah ada" tanpa bukti hampir selalu dinilai 0 atau 1.',
        ],
    ],

    'kontraktor.undangan-seleksi' => [
        'judul' => 'Undangan Seleksi',
        'ringkas' => 'Undangan seleksi dari owner. Unggah HSE Plan khusus proyek sebelum batas waktu.',
        'konsep' => [
            ['HSE Plan', 'Rencana K3 khusus pekerjaan: organisasi K3 proyek, JSA/HIRADC per aktivitas, personel kunci dan sertifikatnya, peralatan dan inspeksinya, tanggap darurat, dan pengendalian khusus lokasi (misalnya keamanan pangan).'],
        ],
        'langkah' => [
            'Buka undangan, baca lingkup dan batas waktu.',
            'Klik "Unggah HSE Plan", lampirkan PDF, dan tulis ringkasan.',
            'Tunggu penilaian owner; hasilnya tampil di baris undangan.',
        ],
        'gambar' => 'Daftar undangan seleksi dengan batas waktu.',
        'tips' => [
            'HSE Plan generik yang tidak menyebut aktivitas proyek akan bernilai rendah di kriteria risiko.',
        ],
    ],

    'kontraktor.kontrak' => [
        'judul' => 'Kontrak',
        'ringkas' => 'Kontrak Anda dengan setiap owner, status mobilisasi, dan masa berlakunya. Halaman ini hanya baca; perubahan dilakukan owner.',
        'langkah' => [
            'Periksa status mobilisasi: kick-off, induksi, kartu akses, izin mulai kerja.',
            'Bila masih tertahan, hubungi pengawas owner untuk syarat yang kurang.',
        ],
        'gambar' => 'Daftar kontrak kontraktor dengan status.',
    ],

    'kontraktor.izin-kerja' => [
        'judul' => 'Izin Kerja',
        'ringkas' => 'Semua izin kerja untuk kontrak Anda di setiap owner. Supervisor kontraktor dapat memantau izin yang aktif, disetujui, dan ditutup.',
        'langkah' => [
            'Saring menurut status untuk melihat izin aktif hari ini.',
            'Pastikan setiap izin aktif ditutup bersama pengawas owner setelah pekerjaan selesai.',
        ],
        'gambar' => 'Daftar izin kerja kontraktor.',
    ],

    /* ===================================================================== UMUM */

    '_umum' => [
        'judul' => 'Bantuan Rekana',
        'ringkas' => 'Rekana adalah sistem manajemen keselamatan kontraktor (CSMS) mengikuti enam fase IOGP 423: penilaian risiko, prakualifikasi, seleksi, mobilisasi, pelaksanaan, dan evaluasi akhir.',
        'langkah' => [
            'Buka menu di sisi kiri sesuai fase pekerjaan.',
            'Klik tombol Bantuan di bagian atas pada halaman mana pun untuk penjelasan halaman tersebut.',
        ],
    ],
];
