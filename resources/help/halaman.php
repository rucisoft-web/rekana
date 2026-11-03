<?php

/*
 * Isi tombol Bantuan per halaman panel Akredia.
 *
 * Kunci: slug resource (mis. "documents") atau slug halaman (mis. "tabel-lkpt",
 * "panduan.memahami-iapt"). Setiap entri:
 *  - judul, ringkas     : apa halaman ini dan mengapa penting untuk akreditasi;
 *  - langkah            : urutan kerja yang disarankan;
 *  - isian              : [kolom, penjelasan, contoh pengisian];
 *  - indikator          : nomor indikator IAPT 4.1 yang dirujuk (teks resmi dimuat dari
 *                         database/data/iapt41-resmi-3c.php);
 *  - lkpt               : tabel LKPT (Lampiran 4b) yang terkait;
 *  - resmi              : kutipan/ketentuan penting dari dokumen resmi BAN-PT;
 *  - tips               : kesalahan umum dan saran.
 *
 * Rujukan: PerBAN-PT No. 35/2025 (IAPT 4.1) beserta Lampiran 2 (Kriteria dan Prosedur),
 * Lampiran 3c (Sistem dan Acuan Penilaian PTS Akademik), Lampiran 4a (Panduan LED), dan
 * Lampiran 4b (Panduan LKPT).
 */

return [

    /* ------------------------------------------------------------------ Ringkasan */

    'dasbor-eksekutif' => [
        'judul' => 'Dasbor Eksekutif',
        'ringkas' => 'Gambaran kesiapan akreditasi institusi dalam satu layar: Indeks Kesiapan Akredia (AKI), proyeksi skor untuk status Terakreditasi dan Terakreditasi Unggul, syarat perlu yang belum terpenuhi, dan gerbang "Siap Diajukan". Angka di sini dihitung dari butir kerja, bukti, LED, dan data kinerja yang diisi tim.',
        'langkah' => [
            'Pastikan ada proyek akreditasi aktif (menu Persiapan Akreditasi → Proyek akreditasi) dengan varian instrumen dan Tahun Sekarang (TS) yang benar.',
            'Baca kartu "Syarat perlu gagal" lebih dulu. Selama masih ada syarat perlu yang gagal, status yang dituju tidak dapat diperoleh berapa pun skor totalnya.',
            'Lihat proyeksi: BAN-PT menetapkan status Terakreditasi bila seluruh syarat perlu Terakreditasi terpenuhi dan skor total ≥ 80%; Unggul bila seluruh syarat perlu Unggul terpenuhi dan skor ≥ 80%.',
            'Klik kartu atau peta panas kriteria untuk turun ke butir kerja yang merah atau kuning, lalu tetapkan tugas.',
            'Tekan "Hitung ulang" setelah data kinerja diperbarui agar metrik dan proyeksi memakai angka terbaru.',
        ],
        'isian' => [
            ['AKI (Indeks Kesiapan Akredia)', 'Ukuran internal Akredia (0–100) yang menggabungkan status butir kerja, kata kunci berbukti, LED yang disetujui, dan data yang terekonsiliasi. Bukan skor resmi BAN-PT.', 'AKI 72 berarti sebagian besar butir sudah berbukti tetapi LED belum banyak disetujui.'],
            ['Proyeksi Terakreditasi / Unggul', 'Persentase deskriptor yang diperkirakan bernilai 1 (memenuhi) dari seluruh deskriptor jalur itu. Setiap deskriptor IAPT 4.1 bernilai 1 atau 0.', 'Proyeksi Unggul 84% tetapi 1 syarat perlu Unggul gagal → status yang realistis tetap Terakreditasi.'],
            ['Gerbang Siap Diajukan', 'Daftar periksa sebelum pengajuan di SAPTO: syarat perlu, LED disahkan, data TS dikunci, rekonsiliasi PD Dikti, dan bukti publik dapat diakses.', 'Gerbang 6/7: tinggal "data TS dikunci" yang belum.'],
        ],
        'indikator' => [4, 7, 11],
        'resmi' => [
            'Lampiran 2: Status TERAKREDITASI diberikan bila seluruh syarat perlu Terakreditasi terpenuhi dan skor total ≥ 80%; status TERAKREDITASI UNGGUL bila seluruh syarat perlu Unggul terpenuhi dan skor total ≥ 80%. Akreditasi berlaku 8 tahun.',
            'Lampiran 2: Asesmen terdiri atas Asesmen Kecukupan (AK), Asesmen Lapangan (AL) bila diputuskan Dewan Eksekutif (daring atau luring), lalu validasi melalui SAPTO 2.0.',
            'Lampiran 3c: Syarat perlu Terakreditasi pada PTS Akademik antara lain Indikator 1 (SPMI), Indikator 4 (100% prodi terakreditasi), Indikator 7A (minimal 5 dosen per prodi), dan Indikator 11 (DTJA). Daftar lengkap ada pada penanda "Syarat perlu" di rujukan resmi setiap indikator.',
        ],
        'tips' => [
            'Angka proyeksi hanya sebaik data yang dimasukkan. Prioritaskan rekonsiliasi dengan PD Dikti sebelum mempercayai proyeksi.',
            'Bagi pimpinan: cukup tanyakan tiga hal setiap minggu, yaitu berapa syarat perlu gagal, tugas apa yang terlambat, dan keputusan apa yang ditunggu tim.',
        ],
    ],

    'pekerjaan-saya' => [
        'judul' => 'Pekerjaan Saya',
        'ringkas' => 'Daftar pekerjaan pribadi Anda: sub-indikator yang Anda pegang sebagai PIC, tugas terbuka, LED yang menunggu reviu Anda, dan bukti yang menunggu validasi. Mulailah hari dari halaman ini.',
        'langkah' => [
            'Kerjakan dulu yang tenggatnya paling dekat atau sudah terlambat (ditandai merah).',
            'Untuk sub-indikator yang Anda pegang: buka butir kerja → isi analisis kesenjangan → lengkapi kertas kerja per kata kunci → tautkan bukti → tulis narasi LED.',
            'Bila Anda reviewer: buka LED yang menunggu reviu, beri komentar per paragraf, lalu setujui atau minta revisi.',
            'Bila Anda validator bukti: periksa dokumen (nomor, tanggal, pengesah, masa berlaku) sebelum menandai valid.',
        ],
        'isian' => [
            ['Sub-indikator yang saya pegang', 'Butir kerja yang PIC utamanya Anda. Warna menunjukkan kesiapan: merah belum dianalisis, kuning sedang berjalan, hijau disetujui.', 'Sub-indikator 7A "Kecukupan dosen" berwarna kuning karena 1 prodi baru punya 4 dosen tetap.'],
            ['Tugas terbuka', 'Tugas yang ditugaskan kepada Anda dari butir kerja, temuan audit, keputusan pimpinan, atau risiko.', 'T-0042 "Unggah SK pengangkatan dosen tetap prodi Farmasi", tenggat 15 November.'],
        ],
        'indikator' => [],
        'tips' => [
            'Jika daftar kosong padahal Anda merasa memegang pekerjaan, minta Manajer menetapkan Anda sebagai PIC atau penanggung jawab tugas.',
        ],
    ],

    'laporan' => [
        'judul' => 'Laporan & Ekspor',
        'ringkas' => 'Pusat unduhan: laporan mingguan pimpinan, status syarat perlu, LED lengkap (DOCX/PDF), Tabel LKPT (XLSX), kertas kerja PIC, daftar bukti publik, dan log audit.',
        'langkah' => [
            'Laporan mingguan pimpinan: cetak setiap pekan untuk rapat koordinasi akreditasi.',
            'LED lengkap (DOCX): unduh setelah semua bagian disahkan, lalu sesuaikan tata letak dengan ketentuan Panduan LED sebelum diunggah ke SAPTO.',
            'Tabel LKPT (XLSX): unduh setelah data TS dikunci. Isi LKPT di SAPTO memakai data PD Dikti; tabel ini dipakai untuk memeriksa kecocokan dan menyiapkan URL bukti untuk data yang tidak ada di PD Dikti.',
            'Daftar bukti publik: berisi URL yang dapat dibuka asesor tanpa login. Tempelkan URL ini pada isian LED/LKPT yang meminta tautan bukti.',
        ],
        'isian' => [],
        'indikator' => [],
        'resmi' => [
            'Lampiran 4a (Panduan LED): dokumen LED ditulis pada kertas A4, huruf Arial 11, spasi 1,15, paling banyak 300 halaman. Ringkasan eksekutif paling banyak 10 halaman, Bab I Pendahuluan paling banyak 20 halaman, dan Bab III Penutup paling banyak 5 halaman.',
            'Lampiran 2: Data LKPT diambil dari PD Dikti per TS. Data yang tidak tersedia di PD Dikti diisi dengan tautan (URL) ke bukti.',
        ],
        'tips' => [
            'Periksa ulang semua tautan bukti di menu Pustaka Bukti → Pemeriksaan tautan sebelum mengekspor. Tautan mati membuat asesor menganggap bukti tidak ada.',
        ],
    ],

    /* ------------------------------------------------------------------ Persiapan Akreditasi */

    'accreditation-projects' => [
        'judul' => 'Proyek Akreditasi',
        'ringkas' => 'Satu proyek mewakili satu siklus pengajuan akreditasi institusi. Proyek menentukan varian instrumen (Lampiran 3a–3h), Tahun Sekarang (TS), jalur target, dan target tanggal pengajuan. Semua butir kerja, LED, dan perhitungan metrik melekat pada proyek.',
        'langkah' => [
            'Pilih varian instrumen sesuai jenis PT. Contoh: universitas swasta → Lampiran 3c PTS Akademik; politeknik negeri → 3e PTN Vokasi. Varian menentukan sub-indikator, ambang, dan syarat perlu.',
            'Tetapkan TS, yaitu tahun akademik penuh terakhir yang datanya sudah lengkap di PD Dikti.',
            'Pilih jalur target (Terakreditasi atau Unggul). Kedua jalur tetap diproyeksikan; jalur target menentukan gerbang yang diperiksa.',
            'Setelah proyek dibuat, Akredia membuat butir kerja untuk setiap sub-indikator. Tetapkan PIC dan reviewer di menu Butir Kerja.',
            'Menjelang pengajuan: kunci data TS (agar angka tidak berubah) lalu ubah status ke "Siap diajukan".',
        ],
        'isian' => [
            ['Nama proyek', 'Nama singkat siklus akreditasi.', 'Akreditasi Institusi 2026 (IAPT 4.1)'],
            ['Varian instrumen', 'Lampiran 3 yang berlaku untuk PT Anda. Tidak dapat diganti setelah butir kerja dibuat.', 'PTS Akademik (Lampiran 3c)'],
            ['Tahun Sekarang (TS)', 'Tahun akademik penuh terakhir. Data TS-1, TS-2, dan seterusnya dihitung mundur dari sini.', 'Bila diajukan Maret 2026, TS = 2024/2025 karena 2025/2026 belum selesai.'],
            ['Jalur target', 'Status yang dituju: Terakreditasi atau Terakreditasi Unggul.', 'Unggul, bila semua prodi terakreditasi dan minimal 15–20% prodi sudah Unggul/A.'],
            ['Target pengajuan', 'Tanggal rencana unggah dokumen di SAPTO. Dipakai untuk menghitung mundur tenggat tugas.', '30 Juni 2026'],
            ['Batas kata per sub-indikator LED', 'Batas internal agar narasi ringkas dan total LED tidak melampaui 300 halaman.', '600 kata'],
        ],
        'indikator' => [],
        'resmi' => [
            'PerBAN-PT 35/2025: IAPT 4.1 menggantikan PerBAN-PT 5/2024, 11/2024, dan 27/2024, dan wajib dipakai paling lambat 6 bulan sejak ditetapkan (2 Desember 2025).',
            'Lampiran 4b: TS adalah tahun akademik penuh terakhir; TS-n adalah tahun akademik n tahun sebelum TS.',
            'Lampiran 2: Jumlah indikator dan sub-indikator yang dinilai 62–65 butir, tergantung varian instrumen.',
        ],
        'tips' => [
            'Kesalahan paling sering: memilih TS tahun berjalan. Akibatnya data kohort dan tracer belum lengkap dan rasio terlihat buruk.',
        ],
    ],

    'work-items' => [
        'judul' => 'Butir Kerja (per sub-indikator)',
        'ringkas' => 'Setiap baris adalah satu sub-indikator IAPT 4.1 yang harus dibuktikan. Butir kerja memandu PIC dari analisis kesenjangan, kertas kerja per kata kunci, penautan bukti, sampai narasi LED. Status butir menentukan warna peta panas di dasbor.',
        'langkah' => [
            'Manajer menetapkan PIC utama, PIC pendamping, reviewer, unit, dan tenggat untuk setiap butir (aksi "Tetapkan PIC / reviewer").',
            'PIC membaca rujukan resmi BAN-PT (tombol Bantuan pada halaman butir) untuk memahami aspek penilaian dan deskripsi pemenuhan Terakreditasi maupun Unggul.',
            'Isi "Kondisi saat ini": bandingkan keadaan nyata dengan deskripsi pemenuhan. Tulis apa yang sudah ada, apa yang kurang, dan buktinya.',
            'Lengkapi kertas kerja: setiap kata kunci deskriptor diberi status Belum/Sebagian/Terbukti beserta rencana tindak lanjut (RTL).',
            'Tautkan dokumen bukti yang sudah divalidasi, lalu tulis narasi LED dan ajukan reviu.',
        ],
        'isian' => [
            ['PIC utama / pendamping', 'Orang yang bertanggung jawab menulis dan mengumpulkan bukti. Pilih pejabat yang menguasai data.', 'Sub-indikator 7B (dosen tidak tetap) → Kepala Biro SDM; pendamping: Wakil Dekan II.'],
            ['Reviewer', 'Orang yang memeriksa kecukupan bukti dan mutu narasi, idealnya auditor mutu internal atau asesor internal.', 'Ketua LPM'],
            ['Tenggat', 'Batas penyelesaian butir. Susun mundur dari target pengajuan.', '2 bulan sebelum target pengajuan untuk butir syarat perlu.'],
            ['Terhambat', 'Centang bila butir menunggu keputusan atau data dari pihak lain, agar terlihat di dasbor pimpinan.', 'Menunggu SK Rektor tentang kebijakan MBKM.'],
            ['Kondisi saat ini (analisis kesenjangan)', 'Uraian jujur tentang keadaan sekarang dibanding deskripsi pemenuhan resmi, beserta angka dan sumbernya.', 'Rasio mahasiswa:dosen tetap TS = 38,2 (PD Dikti, Okt 2025). Memenuhi Terakreditasi (≤ 40); untuk Unggul ditargetkan 20 < RM/DT < 40, sudah memenuhi. Prodi Hukum 46,0 perlu tambahan 3 dosen.'],
        ],
        'indikator' => [],
        'resmi' => [
            'Lampiran 2: Setiap deskriptor dinilai 1 (memenuhi) atau 0 (tidak memenuhi). Asesor menilai berdasarkan LED, LKPT, dan bukti yang dapat diakses.',
            'Lampiran 4a: Untuk setiap indikator, LED meminta perguruan tinggi "menjelaskan" kebijakan, implementasi, dan hasil evaluasi, disertai bukti dalam bentuk dokumen, laman URL, atau lainnya.',
        ],
        'tips' => [
            'Kode sub-indikator di Akredia mengikuti rancangan aplikasi dan dapat berbeda dari huruf sub-aspek resmi (mis. 6A–6C, 7A–7C). Bagian "Rujukan resmi" pada panel ini selalu memakai nomor indikator resmi 1–39.',
            'Gunakan bahasa deskripsi pemenuhan resmi sebagai kerangka narasi: asesor mencocokkan narasi Anda dengan kalimat itu.',
        ],
    ],

    'gap-items' => [
        'judul' => 'Kertas Kerja (Analisis Kesenjangan per Kata Kunci)',
        'ringkas' => 'Deskripsi pemenuhan setiap sub-indikator dipecah menjadi kata kunci yang harus dibuktikan. Kertas kerja mencatat status pembuktian tiap kata kunci, kondisi implementasi, rencana tindak lanjut (RTL), penanggung jawab, dan tenggat.',
        'langkah' => [
            'Baca kata kunci; setiap kata kunci adalah satu klaim yang harus didukung bukti.',
            'Tetapkan status: Belum (tidak ada bukti), Sebagian (bukti ada tetapi tidak lengkap atau belum ditetapkan), Terbukti (bukti sahih dan valid).',
            'Tulis kondisi implementasi secara faktual, termasuk nomor dokumen bila ada.',
            'Untuk status Belum/Sebagian, tulis RTL yang konkret, lalu ubah menjadi tugas agar terpantau.',
        ],
        'isian' => [
            ['Kata kunci', 'Potongan deskripsi pemenuhan yang harus dibuktikan.', '"Renstra pengembangan dosen memenuhi 4 unsur" (Indikator 6A).'],
            ['Status', 'Belum / Sebagian / Terbukti.', 'Sebagian: Renstra SDM ada, tetapi belum memuat analisis beban kerja dosen.'],
            ['Kondisi implementasi', 'Fakta saat ini dan bukti yang sudah ada.', 'Renstra SDM 2021–2025 ditetapkan SK Rektor No. 12/2021; capaian 2023–2025 dilaporkan di laporan tahunan.'],
            ['Rencana tindak lanjut (RTL)', 'Tindakan untuk menutup kesenjangan, dengan hasil yang terukur.', 'Menyusun analisis beban kerja dosen per prodi dan menetapkannya sebagai lampiran Renstra SDM paling lambat 30 November.'],
        ],
        'indikator' => [6, 10],
        'tips' => [
            'Status "Terbukti" hanya untuk bukti yang sudah divalidasi di Pustaka Bukti. Bukti draf atau kedaluwarsa tidak dihitung.',
        ],
    ],

    'led-sections' => [
        'judul' => 'Penyusunan LED (Laporan Evaluasi Diri)',
        'ringkas' => 'Editor narasi LED per sub-indikator. Setiap bagian ditulis PIC, direviu, direvisi, lalu disahkan. Akredia menghitung jumlah kata, cakupan kata kunci, dan sitasi bukti, serta dapat menyisipkan angka hidup dari Data Kinerja.',
        'langkah' => [
            'Buka bagian LED dari Butir Kerja atau daftar ini. Baca panel Bantuan untuk petunjuk resmi Panduan LED pada indikator tersebut.',
            'Tulis narasi mengikuti pola Panduan LED: kebijakan/dasar → implementasi → hasil dan analisis (data, tren, akar masalah) → tindak lanjut perbaikan.',
            'Sitasi bukti dengan memilih dokumen dari Pustaka Bukti. Pada ekspor, sitasi diganti judul dan URL publik.',
            'Sisipkan angka hidup, misalnya {{RMDT}} atau {{PDTT}}, agar angka di narasi selalu sama dengan data terbaru.',
            'Ajukan reviu. Setelah reviewer menyetujui, pimpinan mengesahkan.',
        ],
        'isian' => [
            ['Narasi', 'Teks LED untuk sub-indikator ini. Jawab setiap kata kunci deskripsi pemenuhan.', 'Contoh pola untuk Indikator 6C: "Pada TS (2024/2025) rasio mahasiswa aktif program sarjana terhadap dosen tetap adalah {{RMDT}} (Tabel II-1.A.1 LKPT). Angka ini memenuhi ketentuan RM/DT ≤ 40. Tiga prodi dengan rasio di atas 40 telah ditindaklanjuti melalui rekrutmen 9 dosen tetap (SK Rektor No. ...)."'],
            ['Kata kunci', 'Persentase kata kunci deskriptor yang muncul di narasi. Bukan jaminan mutu, tetapi indikator awal kelengkapan.', '8/10 kata kunci → periksa dua yang belum disebut.'],
            ['Sitasi', 'Jumlah bukti yang dikutip. Setiap klaim penting sebaiknya punya bukti.', 'Minimal satu bukti untuk setiap klaim kebijakan dan satu data untuk setiap klaim capaian.'],
        ],
        'indikator' => [],
        'resmi' => [
            'Lampiran 4a: Sistematika LED terdiri atas Bab I Pendahuluan (dasar penyusunan, tim penyusun dan tanggung jawabnya, mekanisme kerja), Bab II Laporan Evaluasi Diri (Kriteria 1–4), dan Bab III Penutup.',
            'Lampiran 4a: A4, Arial 11, spasi 1,15, paling banyak 300 halaman; ringkasan eksekutif ≤ 10 halaman; Pendahuluan ≤ 20 halaman; Penutup ≤ 5 halaman.',
        ],
        'tips' => [
            'Hindari narasi normatif ("PT berkomitmen meningkatkan mutu"). Asesor mencari bukti, angka, dan perbaikan nyata.',
            'Selalu sebutkan tabel LKPT yang dirujuk agar asesor mudah mencocokkan angka.',
        ],
    ],

    'peta-instrumen' => [
        'judul' => 'Peta Instrumen IAPT 4.1',
        'ringkas' => 'Peta seluruh kriteria, indikator, dan sub-indikator varian instrumen proyek, lengkap dengan penanda syarat perlu, metrik, tabel LKPT, bukti utama, dan kata kunci deskriptor. Gunakan untuk memahami struktur instrumen sebelum membagi tugas.',
        'langkah' => [
            'Mulai dari 4 kriteria: Budaya Mutu, Relevansi (Pendidikan, Penelitian, PkM), Akuntabilitas, dan Diferensiasi Misi.',
            'Perhatikan penanda "syarat perlu"; butir ini menentukan status, bukan sekadar menambah skor.',
            'Gunakan pemilih indikator di panel Bantuan untuk membaca teks resmi Lampiran 3c (aspek penilaian, sumber data, deskripsi Terakreditasi dan Unggul) serta petunjuk Panduan LED.',
        ],
        'isian' => [
            ['Sasaran mutu', 'Setiap indikator dikelompokkan menurut sasaran: Masukan, Proses, Luaran/Capaian, dan Dampak.', 'Indikator 6 (SDM) = Masukan; Indikator 12 (penyelesaian studi) = Luaran; Indikator 14 (pengakuan lulusan) = Dampak.'],
            ['SP Terakreditasi / SP Unggul', 'Penanda syarat perlu. Satu saja gagal, status itu tidak dapat diperoleh.', 'Indikator 4: 100% prodi terakreditasi adalah syarat perlu Terakreditasi.'],
        ],
        'indikator' => [1, 4, 7, 11],
        'resmi' => [
            'Lampiran 2: IAPT 4.1 memiliki 4 kriteria dan 39 indikator; setiap kriteria dinilai menurut sasaran mutu masukan, proses, luaran/capaian, dan dampak.',
        ],
        'tips' => [
            'Akredia meringkas rumusan sub-indikator dari rancangan aplikasi. Sebelum pengajuan, cocokkan kembali dengan Lampiran 3 resmi varian institusi Anda (teks resmi 3c tersedia di panel ini).',
        ],
    ],

    'simulasi-penilaian' => [
        'judul' => 'Simulasi Penilaian (What-if)',
        'ringkas' => 'Simulasikan penilaian asesor: centang sub-indikator yang dianggap memenuhi untuk melihat apakah status Terakreditasi atau Unggul tercapai. Berguna untuk memilih perbaikan dengan dampak terbesar.',
        'langkah' => [
            'Mulai dengan menyimulasikan kondisi jujur saat ini (hanya yang benar-benar berbukti).',
            'Centang semua syarat perlu untuk melihat skor maksimal bila syarat perlu dipenuhi.',
            'Uji skenario: "jika 3 sub-indikator proses diperbaiki, apakah ≥ 80%?"',
        ],
        'isian' => [
            ['Deskriptor Terakreditasi / Unggul', 'Kalimat pemenuhan yang dinilai asesor. Skor 1 bila terpenuhi, 0 bila tidak.', '62 deskriptor; 50 terpenuhi → 80,6%, cukup bila tidak ada syarat perlu yang gagal.'],
        ],
        'indikator' => [],
        'resmi' => [
            'Lampiran 2: Skor total adalah persentase deskriptor bernilai 1. Ambang kelulusan 80% untuk kedua status, ditambah seluruh syarat perlu status tersebut.',
        ],
        'tips' => [
            'Simulasi bukan prediksi resmi. Asesor dapat menilai bukti tidak sahih bila tidak dapat diakses atau tidak ditetapkan pejabat berwenang.',
        ],
    ],

    /* ------------------------------------------------------------------ Pustaka Bukti */

    'documents' => [
        'judul' => 'Dokumen Bukti',
        'ringkas' => 'Pustaka seluruh bukti akreditasi: kebijakan, SK, pedoman, renstra, laporan, notulen, data, sertifikat, dan kerja sama. Setiap dokumen punya kode, versi, pemilik, masa berlaku, tingkat kerahasiaan, dan status validasi. Bukti yang valid dan publik dapat dikutip di LED dengan URL yang bisa dibuka asesor.',
        'langkah' => [
            'Unggah berkas (PDF disarankan) atau tempel URL, lalu isi metadata selengkap mungkin.',
            'Ajukan validasi. Validator memeriksa keabsahan: ditetapkan pejabat berwenang, bernomor, bertanggal, dan masih berlaku.',
            'Tautkan dokumen yang valid ke butir kerja dan sitasi di LED.',
            'Centang "Tampilkan di daftar bukti publik" untuk dokumen yang akan dibuka asesor tanpa login.',
        ],
        'isian' => [
            ['Judul dokumen', 'Judul resmi sesuai dokumen.', 'Peraturan Rektor tentang Kebijakan Sistem Penjaminan Mutu Internal'],
            ['Jenis', 'Kebijakan, Peraturan, SK, Pedoman, Renstra, Laporan, Notulen, Data, Sertifikat, Kerja sama, Laman, atau Lainnya.', 'Kebijakan SPMI → Kebijakan; SK tim penjaminan mutu → SK.'],
            ['Nomor dokumen', 'Nomor resmi. Dokumen tanpa nomor biasanya dianggap belum ditetapkan.', '015/PR/UNX/III/2024'],
            ['Tanggal dokumen / Disahkan oleh', 'Tanggal penetapan dan pejabat yang menandatangani.', '12 Maret 2024, Rektor'],
            ['Unit pemilik', 'Unit yang memelihara dokumen; menentukan siapa boleh melihat dokumen rahasia.', 'Lembaga Penjaminan Mutu'],
            ['Berlaku sampai', 'Kosongkan bila tidak kedaluwarsa. Bukti kedaluwarsa tidak dihitung.', 'Sertifikat ISO 9001 berlaku sampai 20 Agustus 2027.'],
            ['Kerahasiaan', 'Rahasia hanya dapat dilihat unit pemilik, manajer, pimpinan, dan reviewer.', 'Laporan audit keuangan internal → Rahasia; Kebijakan SPMI → Internal/Publik.'],
            ['Uraian singkat', 'Ringkas isi dan relevansinya dengan indikator.', 'Menetapkan siklus PPEPP dan standar mutu akademik/non-akademik; bukti Indikator 1 dan 2.'],
        ],
        'indikator' => [1, 2, 3],
        'resmi' => [
            'Lampiran 4a: Bukti dapat berupa dokumen, laman URL, atau lainnya. Contoh bukti Kriteria 1: kebijakan SPMI; pedoman siklus PPEPP; standar mutu; pedoman pendokumentasian dan organisasi SPMI; implementasi PPEPP; laporan tahunan SPMI dan RTM; sertifikat akreditasi PT dan prodi.',
            'Lampiran 2: Data yang tidak tersedia di PD Dikti dibuktikan dengan URL ke dokumen bukti.',
        ],
        'tips' => [
            'Asesor tidak dapat membuka folder Google Drive yang dikunci. Gunakan URL publik Akredia atau pastikan izin aksesnya terbuka.',
            'Satu dokumen boleh dipakai untuk banyak indikator; cukup unggah sekali lalu tautkan berkali-kali.',
        ],
    ],

    'link-checks' => [
        'judul' => 'Pemeriksaan Tautan',
        'ringkas' => 'Akredia memeriksa secara berkala apakah URL bukti masih dapat dibuka (HTTP 200). Tautan yang gagal beruntun perlu diperbaiki sebelum pengajuan karena asesor menilai dari tautan tersebut.',
        'langkah' => [
            'Urutkan menurut "Gagal beruntun" lalu perbaiki yang terbanyak.',
            'Galat 403/401 biasanya berarti izin akses (mis. Google Drive privat). Galat 404 berarti berkas dipindah atau dihapus.',
            'Setelah diperbaiki, jalankan pemeriksaan ulang.',
        ],
        'isian' => [
            ['URL', 'Alamat bukti yang diperiksa.', 'https://lpm.univ.ac.id/dokumen/kebijakan-spmi.pdf'],
            ['HTTP', 'Kode jawaban server. 200 = baik; 3xx = dialihkan; 4xx/5xx = gagal.', '404 → berkas tidak ditemukan.'],
        ],
        'indikator' => [26],
        'tips' => [
            'Gunakan laman resmi PT (bukan tautan pendek) agar tautan stabil selama masa asesmen.',
        ],
    ],

    /* ------------------------------------------------------------------ Data Kinerja & LKPT */

    'tabel-lkpt' => [
        'judul' => 'Tabel LKPT',
        'ringkas' => 'Pratinjau tabel Laporan Kinerja Perguruan Tinggi (Lampiran 4b) yang disusun otomatis dari Data Kinerja untuk TS proyek. Gunakan untuk memeriksa angka sebelum mengisi atau mencocokkan LKPT di SAPTO.',
        'langkah' => [
            'Pastikan data dosen, statistik mahasiswa, kohort, tracer, survei, prestasi, publikasi, HKI, rekognisi, dan audit keuangan sudah diisi atau diimpor.',
            'Cocokkan setiap tabel dengan data PD Dikti. Bila berbeda, catat di menu Rekonsiliasi dan perbaiki di sumbernya (PD Dikti atau data internal).',
            'Unduh XLSX dari Laporan & Ekspor untuk arsip tim.',
        ],
        'isian' => [
            ['Tabel 1 Akreditasi Program Studi (Ind. 4)', 'Jumlah prodi per status/peringkat dan jenjang.', 'Unggul 3, Baik Sekali 9, Baik 2, Tidak terakreditasi 0 → 100% terakreditasi.'],
            ['Tabel II-1.A.1 Rasio Dosen terhadap Mahasiswa (Ind. 6C)', 'Jumlah mahasiswa program utama dan dosen tetap per prodi pada TS.', '820 mahasiswa : 22 dosen tetap = 37,3.'],
            ['Tabel II-1.A.4 / II-1.A.2 Dosen tetap dan tidak tetap (Ind. 7A, 7B)', 'Jumlah dosen per pendidikan tertinggi per prodi.', 'Prodi Akuntansi: 3 doktor, 9 magister; DTT 4 orang.'],
            ['Tabel II-1.C.1 Jabatan akademik (Ind. 11)', 'Jumlah dosen tetap menurut jabatan GB, LK, L, AA.', 'GB 4, LK 30, L 61, AA 40 dari 160 DT → DTJA 84,4%.'],
            ['Tabel II-1.C.2–C.6 Kelulusan (Ind. 12A)', 'Kohort masuk dan lulus: tepat masa tempuh dan ≤ 2× masa tempuh.', 'Masuk TS-3: 400; lulus tepat waktu 172 → PKMTK 43%.'],
            ['Tabel II-1.D.2 Kepuasan pengguna (Ind. 14B)', 'Persentase SB/B/C/K untuk 7 aspek.', 'Etika: SB 45%, B 45%, C 10%, K 0%.'],
            ['Tabel 2c Penurunan mahasiswa baru (Ind. 15A)', 'Mahasiswa baru TS-4 s.d. TS per prodi dan persentase penurunan.', 'Rerata penurunan 3,2% → memenuhi PPM ≤ 20%.'],
            ['Tabel 24 Kepuasan pemangku kepentingan (Ind. 34)', 'Instrumen, responden, dan hasil SB/B/C/KB per kelompok.', 'Mahasiswa: 1.240 responden, SB+B 81%.'],
            ['Tabel 25 Audit eksternal keuangan (Ind. 35)', 'Lembaga audit, tahun, dan opini.', 'KAP XYZ, 2024, Wajar Tanpa Pengecualian.'],
        ],
        'indikator' => [4, 6, 7, 11, 12, 14, 15, 18, 23, 34, 35],
        'lkpt' => ['Tabel 1', 'Tabel II-1.A.1', 'Tabel II-1.A.2', 'Tabel II-1.A.3', 'Tabel II-1.A.4', 'Tabel 23', 'Tabel II-1.C.1–C.6', 'Tabel II-1.D.2–D.4', 'Tabel 2c', 'Tabel II-2.C.2', 'Tabel 21', 'Tabel 22a/22b', 'Tabel 24', 'Tabel 25'],
        'resmi' => [
            'Lampiran 4b: Dosen tetap adalah dosen yang bekerja penuh waktu pada perguruan tinggi, memenuhi beban kerja 12 sks atau lebih, dan memiliki jabatan akademik.',
            'Lampiran 4b: Jumlah mahasiswa adalah mahasiswa (reguler dan transfer) yang terdaftar di PD Dikti selama masa tempuh kurikulum pada program utama. Program utama PT akademik umumnya program sarjana.',
            'Lampiran 4b: Huruf penanda pada tabel kelulusan (mis. d1, e1, f1) harus tetap dicantumkan.',
        ],
        'tips' => [
            'Sumber utama LKPT adalah PD Dikti. Bila angka internal lebih baik daripada PD Dikti, yang dipakai asesor tetap PD Dikti. Perbaiki pelaporan PD Dikti jauh sebelum TS dikunci.',
        ],
    ],

    'impor-data' => [
        'judul' => 'Impor Data Kinerja & PD Dikti',
        'ringkas' => 'Masukkan data dalam jumlah besar dari XLSX/CSV: dosen, statistik mahasiswa per prodi per tahun, kohort kelulusan, publikasi, dan rekap PD Dikti untuk rekonsiliasi. Unduh templat agar nama kolom sesuai.',
        'langkah' => [
            'Pilih jenis data lalu unduh templat.',
            'Salin data dari ekspor PD Dikti/SISTER/SIAKAD ke templat tanpa mengubah judul kolom.',
            'Unggah berkas. Baris dengan galat dilaporkan nomor barisnya; perbaiki lalu unggah ulang (data dicocokkan sehingga tidak dobel).',
        ],
        'isian' => [
            ['Dosen', 'Kolom nidn, nama, kode_prodi, status (tetap/tidak_tetap), pendidikan (S1/S2/S3), jabatan (gb/lk/l/aa/none), serdos (1/0), aktif (1/0). Dicocokkan dengan NIDN.', '0412078801, Dr. Sari Wulandari, 61201, tetap, S3, lk, 1, 1'],
            ['Statistik mahasiswa', 'Satu baris per prodi per tahun: pendaftar, mahasiswa_baru, mahasiswa_aktif, lulusan, luar_prodi_10sks, rerata_masa_studi_bulan, rerata_ipk.', '61201, 2024, 1350, 310, 1180, 265, 96, 50, 3,42'],
            ['Kohort', 'Satu baris per prodi per tahun masuk: jumlah_masuk, lulus_tepat_waktu, lulus_dua_kali_masa, masih_aktif, keluar.', '61201, 2020, 300, 141, 0, 22, 18 (S1 masuk TS-4, dihitung sampai akhir TS)'],
            ['Publikasi', 'nidn, tahun, judul, jenis (jurnal_intl_bereputasi, jurnal_intl, jurnal_nas_terakreditasi, jurnal_nas, seminar_intl, seminar_nas, media_massa), kategori (penelitian/pkm), media, sitasi, url.', '0412078801, 2024, "Model ...", jurnal_nas_terakreditasi, penelitian, Jurnal Akuntansi (Sinta 2), 4, https://doi.org/...'],
            ['Rekap PD Dikti', 'kode metrik atau variabel, prodi (kode PD Dikti atau kosong untuk institusi), nilai_pddikti.', 'RMDT, , 38,9'],
        ],
        'indikator' => [6, 7, 11, 12, 18],
        'tips' => [
            'Gunakan titik atau koma desimal secara konsisten dalam satu berkas.',
            'Kode prodi harus sama dengan Kode PD Dikti di menu Program studi.',
        ],
    ],

    'lecturers' => [
        'judul' => 'Data Dosen',
        'ringkas' => 'Daftar dosen beserta homebase, status (tetap/tidak tetap), pendidikan tertinggi, jabatan akademik, dan sertifikasi. Data ini menghasilkan metrik RM/DT, kecukupan dosen per prodi, PDTT, persentase doktor, dan DTJA.',
        'langkah' => [
            'Impor dari PD Dikti/SISTER melalui Impor Data agar sama dengan data resmi.',
            'Periksa status dosen tetap: penuh waktu, beban kerja ≥ 12 sks, dan memiliki jabatan akademik (definisi Lampiran 4b).',
            'Pastikan setiap prodi memiliki minimal 5 dosen (syarat perlu Indikator 7A).',
        ],
        'isian' => [
            ['NIDN / NUPTK', 'Nomor induk dosen sesuai PD Dikti; dipakai untuk mencocokkan data.', '0412078801'],
            ['Homebase', 'Prodi tempat dosen tercatat di PD Dikti.', 'S1 Akuntansi'],
            ['Status', 'Tetap atau tidak tetap. Dosen tidak tetap dihitung di PDTT = NDTT / (NDTT + NDT) × 100%.', 'Dosen praktisi kontrak per semester → tidak tetap.'],
            ['Pendidikan tertinggi', 'S1, S2/Magister, S3/Doktor. Dipakai untuk kualifikasi minimal Magister (7A) dan persentase doktor (Unggul ≥ 20%).', 'S3'],
            ['Jabatan akademik', 'Guru Besar, Lektor Kepala, Lektor, Asisten Ahli, atau belum ada. Dipakai untuk DTJA (Ind. 11) dan Guru Besar prodi doktor (7C).', 'Lektor Kepala'],
            ['Bersertifikat pendidik', 'Centang bila memiliki serdos.', 'Ya'],
        ],
        'indikator' => [6, 7, 11],
        'lkpt' => ['Tabel II-1.A.1', 'Tabel II-1.A.2', 'Tabel II-1.A.3', 'Tabel II-1.A.4', 'Tabel II-1.C.1'],
        'resmi' => [
            'Lampiran 3c Ind. 6C: RM/DT ≤ 40 untuk Terakreditasi; 20 < RM/DT < 40 untuk Unggul.',
            'Lampiran 3c Ind. 7A: minimal 5 dosen per program studi (syarat perlu). Unggul: dosen tetap berkualifikasi doktor sekurang-kurangnya 20%.',
            'Lampiran 3c Ind. 7B: PDTT ≤ 40% (Terakreditasi), < 40% (Unggul). Ind. 7C: setiap prodi doktor minimal 2 Guru Besar (Terakreditasi), > 50% Guru Besar (Unggul).',
            'Lampiran 3c Ind. 11: DTJA = ((NDTGB + NDTLK + NDTL + NDTAA) / NDT) × 100%; universitas/institut ≥ 60%, sekolah tinggi ≥ 30% (syarat perlu). Unggul: dosen GB + LK sekurang-kurangnya 10% dari dosen tetap.',
        ],
        'tips' => [
            'Dosen dengan NIDK/NUP harus jelas status kepegawaiannya agar tidak terhitung ganda.',
        ],
    ],

    'program-year-stats' => [
        'judul' => 'Statistik Mahasiswa per Prodi per Tahun',
        'ringkas' => 'Rekap tahunan per prodi: pendaftar, mahasiswa baru, mahasiswa aktif, lulusan, mahasiswa yang mengambil ≥ 10 sks di luar prodi, rerata masa studi, dan IPK. Dasar perhitungan RM/DT, penurunan lulusan (RPL), penurunan mahasiswa baru (PPM), dan pembelajaran di luar prodi.',
        'langkah' => [
            'Isi minimal TS-4 s.d. TS agar tren 5 tahun (Tabel 2c) dapat dihitung.',
            'Mahasiswa aktif = terdaftar di PD Dikti pada semester ganjil TS, termasuk transfer.',
            'Kolom ≥ 10 sks di luar prodi diisi dari data MBKM/pertukaran/magang yang diakui.',
        ],
        'isian' => [
            ['Tahun', 'Tahun awal tahun akademik.', '2024 untuk 2024/2025'],
            ['Mahasiswa baru', 'Mahasiswa baru reguler dan transfer pada tahun itu.', '310'],
            ['Mahasiswa aktif', 'Student body pada TS.', '1.180'],
            ['Lulusan', 'Jumlah lulusan pada tahun akademik itu (untuk RPL).', '265'],
            ['Mahasiswa ≥ 10 sks di luar prodi', 'Mahasiswa yang memperoleh minimal 10 sks di luar prodi (Ind. 10B, Tabel 23).', '96 (8,1% dari mahasiswa aktif)'],
            ['Rerata masa studi (bulan)', 'Rerata lama studi lulusan.', '50 bulan'],
        ],
        'indikator' => [6, 10, 12, 15],
        'lkpt' => ['Tabel II-1.A.1', 'Tabel 23', 'Tabel II-1.C.4', 'Tabel 2c'],
        'resmi' => [
            'Lampiran 3c Ind. 12A: RPL ≤ 20% (sekolah tinggi ≤ 30%) untuk Terakreditasi.',
            'Lampiran 3c Ind. 15A: PPM (penurunan mahasiswa baru) ≤ 20% untuk Terakreditasi, < 20% untuk Unggul.',
            'Lampiran 3c Ind. 10B: persentase mahasiswa yang memperoleh minimal 10 sks di luar prodi pada TS; deskripsi Unggul > 10%.',
        ],
    ],

    'student-cohorts' => [
        'judul' => 'Kohort Kelulusan',
        'ringkas' => 'Data per angkatan: jumlah masuk, lulus tepat masa tempuh, lulus ≤ 2× masa tempuh, masih aktif, dan keluar/DO. Dasar perhitungan PKMTK dan PK2MTK (Indikator 12A).',
        'langkah' => [
            'Untuk S1/D4: kohort tepat waktu memakai angkatan TS-3 (lulus sampai akhir TS). Untuk D3: angkatan TS-2.',
            'Untuk dua kali masa tempuh S1: angkatan TS-7 (atau TS-6 sesuai catatan tabel) yang lulus sampai akhir TS.',
            'Isi per prodi per tahun masuk, lalu cocokkan dengan PD Dikti.',
        ],
        'isian' => [
            ['Tahun masuk', 'Angkatan.', '2021 (= TS-3 bila TS 2024/2025)'],
            ['Jumlah masuk', 'Mahasiswa baru angkatan itu.', '300'],
            ['Lulus tepat masa tempuh', 'Lulus ≤ 4 tahun untuk S1, ≤ 3 tahun untuk D3.', '129 → PKMTK 43%'],
            ['Lulus ≤ 2× masa tempuh', 'Lulus ≤ 8 tahun untuk S1.', '219 dari 300 → PK2MTK 73%'],
            ['Mengundurkan diri / DO', 'Keluar sebelum lulus.', '27'],
        ],
        'indikator' => [12],
        'lkpt' => ['Tabel II-1.C.2', 'Tabel II-1.C.3', 'Tabel II-1.C.5', 'Tabel II-1.C.6'],
        'resmi' => [
            'Lampiran 3c Ind. 12A Terakreditasi: RPL ≤ 20% (ST ≤ 30%), PKMTK ≥ 0%, PK2MTK ≥ 60%.',
            'Lampiran 3c Ind. 12A Unggul: RPL < 20%, PKMTK > 35% (ST > 30%), PK2MTK > 60%.',
        ],
        'tips' => [
            'Mahasiswa yang cuti tetap dihitung di kohort angkatannya.',
        ],
    ],

    'metric-values' => [
        'judul' => 'Nilai Metrik',
        'ringkas' => 'Hasil hitung otomatis metrik kuantitatif IAPT 4.1 per TS dan per prodi (RMDT, PDTT, PDOK, DTJA, RPL, PKMTK, PK2MTK, PPM, PMAP, PLLK, prodi terakreditasi, dan lainnya) beserta masukan rumus dan status verifikasi.',
        'langkah' => [
            'Tekan "Hitung" setelah data kinerja lengkap.',
            'Buka masukan rumus untuk memeriksa angka penyusun (pembilang dan penyebut).',
            'Verifikasi nilai: pemilik data mencocokkan dengan PD Dikti, lalu menandai terverifikasi.',
        ],
        'isian' => [
            ['RMDT', 'Rasio mahasiswa aktif program utama : dosen tetap.', '1.180 / 31 = 38,1 (≤ 40 memenuhi Terakreditasi).'],
            ['PDTT', 'Dosen tidak tetap / (DTT + DT) × 100%.', '18 / (18 + 160) = 10,1%.'],
            ['DTJA', 'Dosen tetap berjabatan akademik / dosen tetap × 100%.', '135 / 160 = 84,4% (universitas ≥ 60%).'],
            ['PKMTK / PK2MTK', 'Kelulusan tepat masa tempuh / ≤ 2× masa tempuh.', '43% / 73%.'],
            ['PPM / PMAP / PLLK', 'Penurunan mahasiswa baru; mahasiswa aktif berprestasi juara 1–3 provinsi/nasional/internasional; lulusan terserap ≤ 1 tahun.', 'PPM 3,2%; PMAP 0,6%; PLLK 58%.'],
        ],
        'indikator' => [6, 7, 11, 12, 15],
        'resmi' => [
            'Ambang Lampiran 3c (Terakreditasi): RM/DT ≤ 40; PDTT ≤ 40%; DTJA ≥ 60% (ST ≥ 30%); RPL ≤ 20%; PKMTK ≥ 0%; PK2MTK ≥ 60%; PPM ≤ 20%; PMAP ≥ 0,01%; PLLK ≥ 20%.',
        ],
        'tips' => [
            'Nilai yang belum diverifikasi sebaiknya tidak dikutip di LED.',
        ],
    ],

    'reconciliations' => [
        'judul' => 'Rekonsiliasi dengan PD Dikti',
        'ringkas' => 'Membandingkan nilai internal dengan nilai PD Dikti untuk setiap metrik/variabel. Selisih di atas toleransi ditandai, diinvestigasi, diperbaiki di sumbernya, dan diverifikasi. Asesor memakai data PD Dikti.',
        'langkah' => [
            'Impor rekap PD Dikti (Impor Data → Rekap PD Dikti).',
            'Urutkan menurut keparahan. Tetapkan unit dan penanggung jawab untuk selisih tinggi.',
            'Investigasi penyebab (mis. dosen belum diperbarui homebase-nya, mahasiswa belum dilaporkan lulus), perbaiki di PD Dikti atau data internal, lalu unggah bukti perbaikan.',
        ],
        'isian' => [
            ['Kode data / metrik', 'Metrik (RMDT, DTJA, ...) atau variabel (dosen_tetap, mhs_aktif_utama, ...).', 'dosen_tetap'],
            ['Cakupan', 'Institusi atau prodi tertentu.', 'S1 Hukum'],
            ['Nilai internal / PD Dikti', 'Angka dari SIAKAD/SDM dan angka di PD Dikti.', 'Internal 12, PD Dikti 9'],
            ['Toleransi (%)', 'Selisih yang masih dapat diterima.', '2%'],
            ['Catatan investigasi', 'Penyebab selisih dan tindakan.', '3 dosen baru belum diangkat sebagai dosen tetap di PD Dikti; usulan NIDN diajukan 4 Oktober.'],
        ],
        'indikator' => [26],
        'resmi' => [
            'Lampiran 2: Data LKPT diambil dari PD Dikti per TS. Data yang tidak tersedia di PD Dikti diisi dengan URL bukti.',
            'Lampiran 3c Ind. 26: PT memiliki sistem pengelolaan data dan informasi berbasis TIK, dengan kebijakan formal dan rencana pengembangan TIK untuk manajemen data.',
        ],
    ],

    'achievements' => [
        'judul' => 'Prestasi Mahasiswa',
        'ringkas' => 'Daftar prestasi akademik dan non-akademik mahasiswa 5 tahun terakhir. Dipakai untuk metrik PMAP (mahasiswa aktif berprestasi juara 1–3 tingkat provinsi, nasional, internasional) pada Indikator 15A.',
        'langkah' => [
            'Catat setiap prestasi dengan tingkat, capaian, dan jumlah mahasiswa.',
            'Unggah sertifikat/piagam ke Pustaka Bukti.',
        ],
        'isian' => [
            ['Nama kegiatan / prestasi', 'Nama lomba dan bidang.', 'Juara 2 Debat Hukum Nasional 2024'],
            ['Tingkat', 'Lokal, provinsi, nasional, internasional. Yang dihitung PMAP: provinsi ke atas.', 'Nasional'],
            ['Capaian', 'Juara 1/2/3 atau lainnya.', 'Juara 2'],
            ['Jumlah mahasiswa', 'Mahasiswa yang terlibat (tim dihitung per orang).', '3'],
        ],
        'indikator' => [15],
        'lkpt' => ['Tabel II-1.D.2 Prestasi Mahasiswa'],
        'resmi' => [
            'Lampiran 3c Ind. 15A: PMAP ≥ 0,01% (Terakreditasi), > 0,01% (Unggul).',
        ],
    ],

    'publications' => [
        'judul' => 'Publikasi Dosen',
        'ringkas' => 'Luaran publikasi dosen tetap dari penelitian dan PkM dalam 3 tahun (TS-2 s.d. TS): jurnal internasional bereputasi, internasional, nasional terakreditasi, nasional, seminar, dan media massa, beserta sitasi.',
        'langkah' => [
            'Impor dari SINTA/SISTER atau isi manual.',
            'Pilih jenis sesuai kategori LKPT agar tabel II-2.C.2 tersusun benar.',
            'Isi URL/DOI agar asesor dapat memverifikasi.',
        ],
        'isian' => [
            ['Jenis', 'Jurnal internasional bereputasi, jurnal internasional, jurnal nasional terakreditasi, jurnal nasional, seminar internasional, seminar nasional, media massa.', 'Jurnal nasional terakreditasi (Sinta 2)'],
            ['Sumber', 'Penelitian atau PkM.', 'Penelitian'],
            ['Sitasi', 'Jumlah sitasi (Google Scholar/Scopus).', '12'],
        ],
        'indikator' => [18, 22],
        'lkpt' => ['Tabel II-2.C.2'],
    ],

    'research-grants' => [
        'judul' => 'Dana Penelitian dan PkM',
        'ringkas' => 'Pendanaan penelitian dan pengabdian kepada masyarakat menurut skema dan sumber dana (internal PT, pemerintah, industri, luar negeri). Mendukung analisis keterlaksanaan Renstra penelitian/PkM.',
        'langkah' => ['Catat setiap hibah per tahun beserta sumber dan jumlah.', 'Lampirkan kontrak atau SK pendanaan sebagai bukti.'],
        'isian' => [
            ['Judul / skema', 'Judul atau nama skema hibah.', 'Penelitian Dasar Kemdiktisaintek 2025'],
            ['Sumber dana', 'Internal PT, pemerintah, industri, luar negeri.', 'Pemerintah'],
            ['Jumlah (Rp)', 'Nilai hibah.', '125000000'],
        ],
        'indikator' => [16, 17, 20, 21],
    ],

    'ip-rights' => [
        'judul' => 'HKI dan Karya yang Diadopsi',
        'ringkas' => 'Hak kekayaan intelektual dosen (paten, paten sederhana, hak cipta, merek, desain) dan karya yang diadopsi masyarakat/industri. Mendukung Indikator 19 dan 23B serta Tabel 22a/22b.',
        'langkah' => ['Catat setiap HKI dengan nomor pencatatan.', 'Tandai bila karya diadopsi dan sebutkan pengadopsinya, sertakan bukti pemanfaatan.'],
        'isian' => [
            ['Nomor pencatatan / paten', 'Nomor dari DJKI.', 'EC00202412345'],
            ['Diadopsi masyarakat / industri', 'Centang bila ada bukti pemanfaatan.', 'Ya'],
            ['Pengadopsi', 'Nama mitra yang memanfaatkan.', 'Koperasi Tani Makmur, Kab. Bandung'],
        ],
        'indikator' => [19, 23],
        'lkpt' => ['Tabel 22a', 'Tabel 22b'],
    ],

    'recognitions' => [
        'judul' => 'Rekognisi Dosen',
        'ringkas' => 'Pengakuan atas kepakaran dosen tetap: narasumber, editor/reviewer jurnal, juri, saksi ahli, penghargaan, keanggotaan asosiasi bereputasi. Dasar Tabel 21 (Indikator 23B).',
        'langkah' => ['Catat rekognisi TS-2 s.d. TS per dosen dengan tingkatnya.', 'Unggah surat undangan/SK/sertifikat sebagai bukti.'],
        'isian' => [
            ['Rekognisi', 'Bentuk pengakuan.', 'Reviewer Jurnal Internasional Q2 (Elsevier)'],
            ['Tingkat', 'Lokal, provinsi, nasional, internasional.', 'Internasional'],
        ],
        'indikator' => [23, 39],
        'lkpt' => ['Tabel 21'],
    ],

    'financial-audits' => [
        'judul' => 'Audit Keuangan',
        'ringkas' => 'Hasil audit eksternal laporan keuangan oleh Kantor Akuntan Publik (KAP) per tahun beserta opininya. Dasar Tabel 25 (Indikator 35).',
        'langkah' => ['Isi untuk TS-2 s.d. TS.', 'Tautkan laporan auditor dari Pustaka Bukti (boleh rahasia).'],
        'isian' => [
            ['Kantor Akuntan Publik', 'Nama KAP pemeriksa.', 'KAP Santoso & Rekan'],
            ['Opini', 'WTP, WDP, TW, atau TMP.', 'Wajar Tanpa Pengecualian'],
            ['Tanggal laporan', 'Tanggal laporan auditor independen.', '28 April 2025'],
        ],
        'indikator' => [35],
        'lkpt' => ['Tabel 25'],
        'resmi' => [
            'Lampiran 3c Ind. 35 Terakreditasi (PTS): Badan Penyelenggara menyampaikan laporan keuangan perguruan tinggi kepada pemangku kepentingan internal.',
            'Lampiran 3c Ind. 35 Unggul: Badan Penyelenggara diaudit KAP dengan opini Wajar Tanpa Pengecualian dan ikhtisar laporan keuangan dipublikasikan secara luas kepada masyarakat.',
        ],
    ],

    /* ------------------------------------------------------------------ Survei & Tracer */

    'surveys' => [
        'judul' => 'Survei Kepuasan Pemangku Kepentingan',
        'ringkas' => 'Pengukuran kepuasan mahasiswa, dosen, tenaga kependidikan, mitra, lulusan, dan pengguna lulusan terhadap layanan pengelola. Dasar Tabel 24 (Indikator 34). Kuesioner publik dapat dibuka agar jawaban masuk otomatis.',
        'langkah' => [
            'Buat satu survei per kelompok per tahun, dengan instrumen yang sahih dan andal.',
            'Bagikan tautan kuesioner publik atau isi rekap hasil.',
            'Hitung SB + B (Akredia otomatis) dan tulis tindak lanjut hasil survei, misalnya perbaikan layanan yang dilakukan.',
        ],
        'isian' => [
            ['Kelompok', 'Mahasiswa, Dosen, Tendik, Mitra, Lulusan, Pengguna.', 'Mahasiswa'],
            ['Populasi / Responden', 'Untuk menghitung keterwakilan responden.', 'Populasi 9.800; responden 1.240 (12,7%).'],
            ['Jawaban SB/B/C/KB', 'Jumlah jawaban per kategori.', 'SB 420, B 590, C 200, KB 30 → SB+B 81,5%.'],
            ['Tindak lanjut', 'Perbaikan nyata berdasarkan hasil survei.', 'Menambah layanan konsultasi akademik daring; waktu tanggap helpdesk turun dari 3 hari menjadi 1 hari.'],
        ],
        'indikator' => [34],
        'lkpt' => ['Tabel 24'],
        'resmi' => [
            'Lampiran 3c Ind. 34 Terakreditasi: pengukuran kepuasan mencakup 4 aspek, dan minimal 75% pemangku kepentingan menjawab puas.',
            'Lampiran 3c Ind. 34 Unggul: 1) instrumen sahih, andal, mudah digunakan; 2) dilaksanakan berkala dan datanya terekam komprehensif; 3) dianalisis dengan metode yang tepat dan bermanfaat untuk keputusan; 4) tingkat kepuasan dan tindak lanjutnya.',
        ],
    ],

    'employer-satisfactions' => [
        'judul' => 'Kepuasan Pengguna Lulusan',
        'ringkas' => 'Hasil survei pengguna lulusan (atasan/pemberi kerja) untuk 7 aspek: etika, keahlian bidang ilmu, bahasa asing, teknologi informasi, komunikasi, kerja sama, dan pengembangan diri. Dasar Tabel II-1.D.2 (Indikator 14B).',
        'langkah' => [
            'Survei pengguna lulusan yang lulus pada TS-1 s.d. TS-3.',
            'Isi persentase atau jumlah jawaban SB/B/C/K untuk setiap aspek.',
            'Tulis rencana tindak lanjut dan bukti perbaikan kurikulum.',
        ],
        'isian' => [
            ['Aspek', 'Salah satu dari 7 aspek Lampiran 4b.', 'Kemampuan berbahasa asing'],
            ['SB / B / C / K', 'Jumlah atau persentase jawaban.', 'SB 20%, B 48%, C 27%, K 5%.'],
            ['Rencana tindak lanjut', 'Perbaikan kurikulum atau layanan.', 'Mewajibkan sertifikat TOEFL ITP ≥ 450 dan menambah mata kuliah English for Specific Purposes.'],
        ],
        'indikator' => [14],
        'lkpt' => ['Tabel II-1.D.2'],
        'resmi' => [
            'Lampiran 3c Ind. 14B: survei kepuasan pengguna lulusan TS-1 s.d. TS-3 dengan data valid dan representatif, dievaluasi untuk perbaikan kurikulum; Unggul: terbukti ditindaklanjuti dengan bukti sahih.',
        ],
    ],

    'tracer-studys' => [
        'judul' => 'Tracer Study',
        'ringkas' => 'Penelusuran lulusan per tahun lulus: responden, bekerja, studi lanjut, berwirausaha, terserap ≤ 1 tahun, waktu tunggu, bekerja di tingkat global, dan bersertifikat kompetensi. Dasar metrik PLLK (Indikator 15A) dan Tabel II-1.D.3.',
        'langkah' => [
            'Jalankan tracer study tiap tahun untuk lulusan TS-1 s.d. TS-3 (mengikuti Tracer Study Kemdiktisaintek).',
            'Isi rekap per tahun lulus.',
            'Analisis hasil untuk Indikator 15B: akar masalah, faktor pendukung, faktor penghambat, dan tindak lanjut.',
        ],
        'isian' => [
            ['Jumlah lulusan / Responden', 'Lulusan tahun itu dan yang menjawab.', '1.020 lulusan; 612 responden (60%).'],
            ['Terserap ≤ 1 tahun', 'Bekerja, studi lanjut, atau berwirausaha dalam ≤ 1 tahun.', '355 → PLLK 58%.'],
            ['Rerata waktu tunggu (bulan)', 'Rerata waktu memperoleh pekerjaan pertama.', '4,2 bulan'],
        ],
        'indikator' => [14, 15],
        'lkpt' => ['Tabel II-1.D.3', 'Tabel II-1.D.4'],
        'resmi' => ['Lampiran 3c Ind. 15A: PLLK ≥ 20% (Terakreditasi), > 20% (Unggul).'],
    ],

    /* ------------------------------------------------------------------ Pengendalian */

    'tasks' => [
        'judul' => 'Tugas',
        'ringkas' => 'Semua pekerjaan tindak lanjut: dari kertas kerja, temuan AMI, keputusan pimpinan, risiko, dan rekomendasi asesor. Setiap tugas punya penanggung jawab, tenggat, prioritas, dan status.',
        'langkah' => ['Tulis judul yang berupa hasil akhir, bukan aktivitas.', 'Tetapkan satu penanggung jawab dan tenggat realistis.', 'Kaitkan dengan butir kerja agar kemajuan terlihat di dasbor.'],
        'isian' => [
            ['Judul tugas', 'Hasil akhir yang dapat diperiksa.', 'SK Rektor tentang pedoman MBKM ditetapkan (bukan "Rapat MBKM").'],
            ['Prioritas', 'Tinggi untuk butir syarat perlu atau tenggat dekat.', 'Tinggi'],
            ['Butir kerja terkait', 'Sub-indikator yang terdampak.', '10B'],
        ],
        'indikator' => [],
    ],

    'papan-tugas' => [
        'judul' => 'Papan Kanban Tugas',
        'ringkas' => 'Tampilan kanban tugas per status (belum mulai, berjalan, ditinjau, selesai). Seret kartu untuk memindahkan status.',
        'langkah' => ['Gunakan pada rapat koordinasi mingguan.', 'Fokus pada kartu yang lama tidak bergerak.'],
        'isian' => [],
        'indikator' => [],
    ],

    'kalender-tenggat' => [
        'judul' => 'Kalender Tenggat',
        'ringkas' => 'Semua tenggat dalam satu kalender: butir kerja, tugas, CAPA, tinjau ulang standar, masa berlaku dokumen, akreditasi prodi yang akan berakhir, dan jadwal asesmen lapangan.',
        'langkah' => ['Periksa tenggat 30 hari ke depan setiap awal minggu.', 'Akreditasi prodi yang akan kedaluwarsa memengaruhi syarat perlu Indikator 4; ajukan reakreditasi jauh hari.'],
        'isian' => [],
        'indikator' => [4],
    ],

    'decisions' => [
        'judul' => 'Keputusan Pimpinan',
        'ringkas' => 'Catatan keputusan pimpinan terkait akreditasi beserta tugas turunannya. Membuktikan peran pimpinan dalam siklus PPEPP (pengendalian dan peningkatan) dan tata pamong.',
        'langkah' => ['Catat keputusan segera setelah rapat.', 'Turunkan menjadi tugas dengan penanggung jawab.', 'Tutup keputusan bila seluruh tugas selesai.'],
        'isian' => [
            ['Keputusan', 'Rumusan singkat.', 'Rekrutmen 9 dosen tetap untuk 3 prodi dengan RM/DT > 40.'],
            ['Forum', 'Rapat tempat keputusan diambil.', 'Rapat Pimpinan 14 Oktober 2025'],
        ],
        'indikator' => [2, 25],
    ],

    'risks' => [
        'judul' => 'Risiko',
        'ringkas' => 'Register risiko akreditasi: peristiwa yang dapat menggagalkan indikator, dengan probabilitas, dampak, skor, pemilik, dan mitigasi. SPMI IAPT 4.1 menuntut pendekatan berbasis risiko.',
        'langkah' => ['Identifikasi risiko per indikator, terutama syarat perlu.', 'Nilai probabilitas dan dampak 1–5; skor = P × D.', 'Tetapkan mitigasi dan target tanggal.'],
        'isian' => [
            ['Risiko', 'Peristiwa yang mungkin terjadi.', 'Akreditasi prodi D3 Perhotelan berakhir Mei 2026 sebelum hasil reakreditasi terbit.'],
            ['Probabilitas / Dampak', 'Skala 1–5.', 'P 3, D 5 → skor 15 (tinggi).'],
            ['Mitigasi', 'Langkah mengurangi risiko.', 'Unggah dokumen reakreditasi ke LAM paling lambat Desember 2025.'],
        ],
        'indikator' => [25, 4],
        'resmi' => ['Lampiran 3c Ind. 25: PT memiliki sistem tata pamong untuk menjamin akuntabilitas, keberlanjutan, dan transparansi, serta mitigasi potensi risiko akademik maupun non-akademik.'],
    ],

    /* ------------------------------------------------------------------ SPMI */

    'quality-standards' => [
        'judul' => 'Standar Mutu',
        'ringkas' => 'Daftar standar SPMI (akademik dan non-akademik) beserta tahap PPEPP saat ini dan jadwal tinjau ulang. Membuktikan Indikator 1 (perangkat SPMI) dan Indikator 2 (implementasi siklus PPEPP).',
        'langkah' => ['Catat semua standar yang ditetapkan, minimal standar SN Dikti dan standar tambahan PT.', 'Perbarui tahap PPEPP setiap siklus.', 'Tinjau ulang standar sesuai jadwal; hasil tinjauan menjadi bukti peningkatan.'],
        'isian' => [
            ['Kode / Nama standar', 'Identitas standar.', 'STD-AK-03 Standar Proses Pembelajaran'],
            ['Bidang', 'Akademik atau non-akademik.', 'Akademik'],
            ['Tahap PPEPP', 'Penetapan, Pelaksanaan, Evaluasi, Pengendalian, Peningkatan.', 'Evaluasi (AMI 2025 selesai)'],
            ['Pernyataan standar', 'Rumusan standar yang terukur (ABCD: audience, behavior, competence, degree).', 'Dosen pengampu mengunggah RPS seluruh mata kuliah paling lambat 2 minggu sebelum perkuliahan (100%).'],
        ],
        'indikator' => [1, 2, 3],
    ],

    'internal-audits' => [
        'judul' => 'Audit Mutu Internal (AMI)',
        'ringkas' => 'Jadwal dan hasil AMI per unit per siklus. AMI adalah tahap Evaluasi dalam PPEPP dan bukti utama Indikator 2.',
        'langkah' => ['Rencanakan AMI tahunan untuk semua unit.', 'Catat hasil dan temuan di menu Temuan.', 'Pastikan temuan ditindaklanjuti dengan CAPA dan dibahas di RTM.'],
        'isian' => [
            ['Nama audit', 'Nama siklus dan unit.', 'AMI 2025 Fakultas Ekonomi dan Bisnis'],
            ['Ketua auditor', 'Auditor bersertifikat.', 'Dr. Andi Pratama, M.M.'],
            ['Ringkasan hasil', 'Ringkasan kesimpulan audit.', '2 mayor, 5 minor, 3 observasi; kesesuaian 86%.'],
        ],
        'indikator' => [2, 3],
    ],

    'findings' => [
        'judul' => 'Temuan',
        'ringkas' => 'Temuan AMI atau sumber lain (asesor, survei) dengan kategori mayor/minor/observasi/peluang, akar masalah, dan tenggat. Setiap temuan ditindaklanjuti dengan CAPA.',
        'langkah' => ['Uraikan temuan berdasarkan fakta dan standar yang dilanggar.', 'Analisis akar masalah (mis. 5 Why).', 'Buat CAPA dan pantau sampai efektif.'],
        'isian' => [
            ['Kategori', 'Mayor: ketidaksesuaian yang berdampak sistemik; Minor: ketidaksesuaian terbatas; Observasi: potensi masalah; Peluang: saran peningkatan.', 'Minor'],
            ['Uraian temuan', 'Fakta + kriteria.', '4 dari 32 RPS prodi Manajemen belum memuat CPMK (Standar STD-AK-03).'],
            ['Akar masalah', 'Penyebab mendasar.', 'Templat RPS lama belum diperbarui setelah revisi kurikulum OBE.'],
            ['Indikator IAPT terkait', 'Indikator yang terdampak.', '5 (kurikulum OBE)'],
        ],
        'indikator' => [2, 5],
    ],

    'capas' => [
        'judul' => 'CAPA (Tindakan Koreksi dan Pencegahan)',
        'ringkas' => 'Tindakan koreksi/pencegahan atas temuan, dengan penanggung jawab, tenggat, dan verifikasi efektivitas. Bukti tahap Pengendalian dan Peningkatan pada siklus PPEPP.',
        'langkah' => ['Pilih temuan dan jenis tindakan (koreksi/korektif/pencegahan).', 'Laksanakan dan catat tanggal selesai.', 'Verifikator memeriksa efektivitas: apakah masalah tidak terulang.'],
        'isian' => [
            ['Tindakan', 'Tindakan yang menghilangkan akar masalah.', 'Memperbarui templat RPS dan melatih 32 dosen; RPS direviu GKM.'],
            ['Efektif?', 'Diisi verifikator setelah tindakan berjalan.', 'Ya, AMI berikutnya 0 RPS tanpa CPMK.'],
        ],
        'indikator' => [2],
    ],

    'management-reviews' => [
        'judul' => 'Rapat Tinjauan Manajemen (RTM)',
        'ringkas' => 'Notulen dan keputusan RTM yang membahas hasil AMI, survei, kinerja, dan tindak lanjut. RTM adalah bukti pengendalian oleh pimpinan dan laporan implementasi SPMI (Indikator 2 dan 3).',
        'langkah' => ['Selenggarakan RTM minimal setahun sekali setelah AMI.', 'Catat masukan, keputusan, dan tindak lanjut.', 'Turunkan keputusan menjadi tugas.'],
        'isian' => [
            ['Judul rapat', 'Nama RTM.', 'RTM Tingkat Universitas 2025'],
            ['Keputusan dan tindak lanjut', 'Butir keputusan yang terukur.', '1) Revisi standar penelitian (target 2026); 2) Anggaran pelatihan auditor Rp150 juta.'],
        ],
        'indikator' => [2, 3],
    ],

    /* ------------------------------------------------------------------ Asesmen Lapangan */

    'site-visits' => [
        'judul' => 'Asesmen Lapangan',
        'ringkas' => 'Rencana dan catatan asesmen lapangan (AL) daring atau luring: jadwal, gladi, tim asesor, ruang dokumen, sesi, narasumber, dan berita acara.',
        'langkah' => ['Buat agenda setelah ada surat pemberitahuan AL dari BAN-PT.', 'Susun sesi dan narasumber per kelompok.', 'Lakukan gladi minimal satu minggu sebelumnya.', 'Catat rekomendasi asesor untuk ditindaklanjuti.'],
        'isian' => [
            ['Moda', 'Daring atau luring, sesuai keputusan BAN-PT.', 'Daring (Zoom)'],
            ['Ruang dokumen / tautan', 'Tempat asesor membuka bukti.', 'https://akreditasi.univ.ac.id/ruang-dokumen'],
        ],
        'indikator' => [],
        'resmi' => ['Lampiran 2: Asesmen Lapangan dilakukan bila diputuskan Dewan Eksekutif, secara daring atau luring, untuk memverifikasi dan mengklarifikasi hasil Asesmen Kecukupan.'],
    ],

    'visit-sessions' => [
        'judul' => 'Sesi Asesmen Lapangan',
        'ringkas' => 'Jadwal sesi AL per kelompok narasumber (pimpinan, dosen, tendik, mahasiswa, lulusan, pengguna, observasi).',
        'langkah' => ['Isi waktu, topik, ruang/tautan, dan koordinator setiap sesi.'],
        'isian' => [['Topik / acara', 'Isi sesi.', 'Wawancara pimpinan: tata pamong dan diferensiasi misi (Ind. 24–25, 36).']],
        'indikator' => [],
    ],

    'visit-interviewees' => [
        'judul' => 'Narasumber Asesmen Lapangan',
        'ringkas' => 'Daftar narasumber per kelompok beserta konfirmasi kehadiran.',
        'langkah' => ['Pilih narasumber yang memahami praktik nyata, bukan hanya pejabat.', 'Konfirmasi kehadiran H-3.'],
        'isian' => [['Kelompok', 'Pimpinan, dosen, tendik, mahasiswa, lulusan, pengguna.', 'Pengguna: HRD PT Bank ABC']],
        'indikator' => [],
    ],

    'visit-questions' => [
        'judul' => 'Bank Pertanyaan Asesor',
        'ringkas' => 'Prediksi pertanyaan asesor per indikator, pokok jawaban, bukti yang ditunjukkan, dan penjawab utama. Dipakai untuk gladi.',
        'langkah' => ['Turunkan pertanyaan dari deskripsi pemenuhan resmi (lihat rujukan resmi).', 'Tulis pokok jawaban singkat dengan bukti yang dapat ditunjukkan.', 'Tandai setelah digladikan.'],
        'isian' => [
            ['Pertanyaan', 'Pertanyaan yang mungkin muncul.', 'Bagaimana hasil AMI 2024 ditindaklanjuti dan apa buktinya?'],
            ['Pokok jawaban dan bukti', 'Inti jawaban + dokumen.', 'Temuan 7 minor → 7 CAPA selesai; notulen RTM 12 Feb 2025; laporan AMI 2025.'],
        ],
        'indikator' => [2],
    ],

    'visit-recommendations' => [
        'judul' => 'Rekomendasi Asesor',
        'ringkas' => 'Rekomendasi asesor setelah AL per indikator beserta tindak lanjutnya. Bahan perbaikan berkelanjutan dan bukti SPMI siklus berikutnya.',
        'langkah' => ['Catat rekomendasi apa adanya.', 'Turunkan menjadi tugas atau CAPA.'],
        'isian' => [['Rekomendasi', 'Teks rekomendasi.', 'Perkuat analisis capaian dampak Indikator 15 dengan akar masalah per prodi.']],
        'indikator' => [],
    ],

    /* ------------------------------------------------------------------ Institusi */

    'profil-institusi' => [
        'judul' => 'Profil Institusi',
        'ringkas' => 'Identitas perguruan tinggi untuk halaman identitas LKPT dan Bab I LED: nama, kode PT, jenis PT (menentukan varian instrumen), badan penyelenggara, SK pendirian, akreditasi terakhir, visi, dan fokus diferensiasi misi.',
        'langkah' => ['Lengkapi seluruh identitas sesuai SK pendirian.', 'Tulis fokus diferensiasi misi: pendidikan, penelitian, atau PkM. Pilihan ini dipakai di banyak deskriptor Unggul dan Kriteria 4.'],
        'isian' => [
            ['Jenis PT', 'Menentukan varian Lampiran 3.', 'PTS Akademik → Lampiran 3c'],
            ['Tahun pertama menerima mahasiswa', 'Menentukan perlakuan "PT baru < 2 tahun" pada beberapa indikator.', '1998'],
            ['Fokus diferensiasi misi', 'Bidang keunggulan yang dijanjikan.', 'Pendidikan vokasional berbasis industri kreatif.'],
        ],
        'indikator' => [36, 37, 38, 39],
        'lkpt' => ['Identitas Perguruan Tinggi'],
    ],

    'study-programs' => [
        'judul' => 'Program Studi',
        'ringkas' => 'Daftar prodi beserta jenjang, kode PD Dikti, fakultas, tahun dibuka, akreditasi berlaku, dan jumlah dosen tetap. Dasar Indikator 4 dan pengelompokan data per prodi.',
        'langkah' => ['Gunakan kode PD Dikti resmi.', 'Prodi baru yang beroperasi > 2 tahun dan belum terakreditasi dihitung tidak terakreditasi.'],
        'isian' => [
            ['Kode PD Dikti', 'Kode program studi.', '61201'],
            ['Jenjang', 'D3, D4, S1, S2, S3, profesi, spesialis.', 'S1'],
            ['Tahun dibuka', 'Tahun izin operasional.', '2019'],
        ],
        'indikator' => [4, 7],
        'lkpt' => ['Tabel 1'],
    ],

    'program-accreditations' => [
        'judul' => 'Akreditasi Program Studi',
        'ringkas' => 'Riwayat dan status akreditasi setiap prodi dari BAN-PT atau LAM, termasuk akreditasi internasional. Dasar Tabel 1 dan syarat perlu Indikator 4.',
        'langkah' => ['Masukkan SK akreditasi terbaru setiap prodi dan centang "berlaku saat ini".', 'Pantau masa berlaku; prodi kedaluwarsa menggagalkan syarat perlu Terakreditasi.'],
        'isian' => [
            ['Lembaga', 'BAN-PT atau LAM (LAMEMBA, LAMDIK, LAM Teknik, LAM-PTKes, dan lain-lain).', 'LAMEMBA'],
            ['Status / peringkat', 'Unggul, Baik Sekali, Baik, A, B, C, Terakreditasi, Minimum, Internasional, Tidak.', 'Baik Sekali'],
            ['Nomor SK / Berlaku sampai', 'Sesuai sertifikat.', '1234/DE/A.5/AR.10/X/2023, berlaku sampai 10 Oktober 2028'],
        ],
        'indikator' => [4],
        'lkpt' => ['Tabel 1', 'Tabel 3 (akreditasi internasional)'],
        'resmi' => [
            'Lampiran 3c Ind. 4 Terakreditasi: persentase PS terakreditasi 100% (syarat perlu).',
            'Lampiran 3c Ind. 4 Unggul: PT dengan ≥ 40 prodi atau ≤ 10 prodi: PS Unggul dan/atau A minimal 20%; PT dengan 10–40 prodi: minimal 15% (syarat perlu Unggul).',
            'Lampiran 4b: Prodi terakreditasi/bersertifikat internasional dihitung jika telah divalidasi kementerian.',
        ],
    ],

    'units' => [
        'judul' => 'Unit Kerja',
        'ringkas' => 'Struktur organisasi: fakultas, lembaga, biro, UPT, beserta kepala unit. Dipakai untuk pembagian tugas, akses bukti rahasia, dan sasaran AMI. Relevan dengan Indikator 24 (statuta dan struktur organisasi).',
        'langkah' => ['Masukkan sesuai statuta/OTK terbaru.', 'Tetapkan unit induk untuk hierarki.'],
        'isian' => [['Jenis', 'Fakultas, lembaga, biro, UPT, prodi, lainnya.', 'Lembaga (LPM)']],
        'indikator' => [24],
    ],

    'academic-periods' => [
        'judul' => 'Periode Akademik',
        'ringkas' => 'Tahun akademik yang dipakai untuk TS dan TS-n. Proyek akreditasi memilih salah satu periode sebagai TS.',
        'langkah' => ['Buat periode untuk minimal 7 tahun terakhir (TS-6 s.d. TS) agar tabel kohort lengkap.'],
        'isian' => [
            ['Nama periode', 'Format tahun akademik.', '2024/2025'],
            ['Tahun awal (TS)', 'Tahun awal periode.', '2024'],
        ],
        'indikator' => [],
        'resmi' => ['Lampiran 4b: TS = tahun akademik penuh terakhir; TS-n = tahun akademik n tahun sebelum TS.'],
    ],

    /* ------------------------------------------------------------------ Administrasi */

    'memberships' => [
        'judul' => 'Pengguna dan Peran',
        'ringkas' => 'Kelola anggota tim dan perannya: Admin, Pimpinan, Manajer akreditasi, PIC, Pemilik Data, Reviewer, Asesor (internal), dan Pengamat (hanya baca).',
        'langkah' => ['Undang pengguna dengan email institusi.', 'Pilih peran sesuai tanggung jawab; PIC dan Pemilik Data wajib punya unit.', 'Nonaktifkan akun yang tidak lagi terlibat.'],
        'isian' => [
            ['Peran', 'Pimpinan: memantau dan mengesahkan; Manajer: mengatur proyek dan penugasan; PIC: menulis butir; Pemilik Data: mengisi data kinerja; Reviewer: mereviu; Pengamat: hanya membaca.', 'Kepala Biro Akademik → Pemilik Data, unit Biro Akademik.'],
            ['Unit', 'Membatasi akses bukti rahasia ke unit pengguna.', 'Biro SDM'],
        ],
        'indikator' => [],
    ],

    'audit-logs' => [
        'judul' => 'Log Audit',
        'ringkas' => 'Jejak semua perubahan data, login, ekspor, dan unduhan bukti rahasia. Mendukung akuntabilitas dan keamanan data (Indikator 26).',
        'langkah' => ['Saring menurut pengguna atau rentang tanggal untuk menelusuri perubahan.'],
        'isian' => [],
        'indikator' => [26],
    ],

    /* ------------------------------------------------------------------ Panduan */

    'panduan.memahami-iapt' => [
        'judul' => 'Memahami IAPT 4.1',
        'ringkas' => 'Penjelasan struktur IAPT 4.1: 4 kriteria, 39 indikator, sasaran mutu, syarat perlu, dan cara penilaian. Gunakan pemilih indikator di bawah untuk membaca teks resmi setiap indikator.',
        'langkah' => ['Pahami alur: Masukan → Proses → Luaran/Capaian → Dampak pada setiap kriteria.', 'Baca indikator yang bertanda syarat perlu lebih dulu (mis. 1, 4, 7A, dan 11 untuk PTS Akademik).'],
        'isian' => [],
        'indikator' => [1, 2, 3, 4],
        'resmi' => [
            'Lampiran 2: Kriteria IAPT 4.1: (1) Budaya Mutu, (2) Relevansi Pendidikan, Penelitian, dan PkM, (3) Akuntabilitas, (4) Diferensiasi Misi.',
            'Lampiran 2: Syarat eligibilitas: 1) izin penyelenggaraan yang sah dan berlaku; 2) anggaran dasar/rumah tangga atau statuta; 3) rencana induk pengembangan atau renstra yang memuat diferensiasi visi-misi, nilai dasar, pengambilan keputusan, dan SPMI; 4) seluruh prodi aktif berstatus terakreditasi (termasuk terakreditasi pertama/sementara).',
        ],
    ],

    'panduan' => [
        'judul' => 'Panduan',
        'ringkas' => 'Halaman panduan berisi penjelasan konsep akreditasi dan cara memakai Akredia. Tombol Bantuan di setiap halaman kerja memberi penjelasan isian, contoh, dan rujukan resmi BAN-PT yang relevan dengan halaman tersebut.',
        'langkah' => ['Mulai dari "Memahami IAPT 4.1" lalu "Peta Jalan Akreditasi".', 'Gunakan pemilih indikator di bawah untuk membaca teks resmi indikator mana pun.'],
        'isian' => [],
        'indikator' => [],
    ],

    'profile' => [
        'judul' => 'Profil Saya',
        'ringkas' => 'Ubah nama, email, kata sandi, dan aktifkan verifikasi dua langkah (MFA). Pengguna dengan akses bukti rahasia sangat disarankan mengaktifkan MFA.',
        'langkah' => ['Gunakan kata sandi minimal 12 karakter.', 'Aktifkan MFA dengan aplikasi autentikator.'],
        'isian' => [],
        'indikator' => [],
    ],

    '_umum' => [
        'judul' => 'Bantuan',
        'ringkas' => 'Akredia membantu tim menyiapkan akreditasi institusi IAPT 4.1: membagi sub-indikator kepada PIC, mengumpulkan bukti, menghitung data kinerja dari PD Dikti, menulis LED, dan memantau kesiapan.',
        'langkah' => [
            'Manajer membuat proyek akreditasi dan menetapkan PIC per butir kerja.',
            'Pemilik data mengisi atau mengimpor Data Kinerja dan merekonsiliasinya dengan PD Dikti.',
            'PIC mengisi analisis kesenjangan, mengunggah bukti, dan menulis LED.',
            'Reviewer mereviu, pimpinan memantau dasbor dan mengesahkan.',
        ],
        'isian' => [],
        'indikator' => [],
    ],
];
