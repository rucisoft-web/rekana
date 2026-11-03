<?php

/*
 * Template baku kuesioner prakualifikasi CSMS untuk industri manufaktur pangan.
 * Disusun mengacu kerangka ISO 45001:2018 (termasuk klausul 8.1.4 pengendalian kontraktor), elemen SMK3 PP 50/2012,
 * dan tahap prakualifikasi IOGP Report 423. Butir, bobot, dan butir kritis adalah usulan awal Rekana; owner dapat
 * menyalin template ini lalu menyesuaikannya.
 *
 * Format butir: [kode, pertanyaan, bukti yang diharapkan, bobot 1-3, kritis?, berlaku mulai risiko]
 */
return [
    'code' => 'CSMS-ISO45001-SMK3-PANGAN',
    'name' => 'CSMS Manufaktur Pangan (ISO 45001 dan SMK3)',
    'standard' => 'ISO 45001:2018 kl. 8.1.4 · PP 50/2012 SMK3 · IOGP 423',
    'description' => 'Kuesioner prakualifikasi K3 rekanan untuk pabrik pangan, termasuk elemen keamanan pangan dan halal.',
    'elements' => [
        ['A', 'Kepemimpinan dan komitmen', [
            ['A1', 'Apakah perusahaan memiliki kebijakan K3 tertulis yang ditandatangani pimpinan tertinggi dan dikomunikasikan ke seluruh pekerja?', 'Dokumen kebijakan K3 bertanda tangan dan bukti sosialisasi', 3, true, 'rendah'],
            ['A2', 'Apakah pimpinan menetapkan sasaran dan program K3 tahunan yang terukur?', 'Program K3 tahunan dan laporan capaiannya', 2, false, 'menengah'],
            ['A3', 'Apakah pimpinan terlibat langsung dalam kegiatan K3 (inspeksi, rapat, safety talk)?', 'Notulen rapat atau jadwal inspeksi manajemen', 1, false, 'menengah'],
        ]],
        ['B', 'Organisasi dan tanggung jawab K3', [
            ['B1', 'Apakah ada personel yang ditunjuk bertanggung jawab atas K3 (misalnya Ahli K3 Umum bersertifikat)?', 'SK penunjukan dan sertifikat AK3 / sertifikat kompetensi K3', 3, false, 'rendah'],
            ['B2', 'Apakah struktur organisasi K3 dan P2K3 (bila wajib) sudah dibentuk?', 'Struktur organisasi K3, SK P2K3', 2, false, 'menengah'],
            ['B3', 'Untuk proyek di lokasi owner, berapa rasio pengawas K3 terhadap jumlah pekerja yang Anda tetapkan?', 'Rencana penugasan pengawas K3', 2, false, 'tinggi'],
        ]],
        ['C', 'Identifikasi bahaya dan penilaian risiko', [
            ['C1', 'Apakah perusahaan melakukan identifikasi bahaya dan penilaian risiko (HIRADC/JSA) sebelum pekerjaan dimulai?', 'Contoh HIRADC/JSA pekerjaan sejenis', 3, false, 'rendah'],
            ['C2', 'Apakah hasil penilaian risiko dikomunikasikan ke pekerja sebelum bekerja (toolbox talk)?', 'Catatan toolbox talk', 2, false, 'menengah'],
            ['C3', 'Apakah ada prosedur Management of Change saat metode kerja atau alat berubah?', 'Prosedur MoC', 1, false, 'tinggi'],
        ]],
        ['D', 'Prosedur kerja aman', [
            ['D1', 'Apakah perusahaan memiliki prosedur kerja aman untuk jenis pekerjaan yang ditawarkan?', 'Daftar dan contoh SOP', 2, false, 'rendah'],
            ['D2', 'Apakah ada prosedur untuk pekerjaan berisiko tinggi: kerja panas, ruang terbatas, ketinggian, dan LOTO?', 'SOP kerja panas, ruang terbatas, ketinggian, LOTO', 3, true, 'tinggi'],
            ['D3', 'Apakah perusahaan memahami dan bersedia mengikuti sistem izin kerja owner?', 'Pernyataan kesediaan dan contoh izin kerja yang pernah dipakai', 2, false, 'menengah'],
        ]],
        ['E', 'Kompetensi dan pelatihan', [
            ['E1', 'Apakah pekerja mendapat pelatihan K3 dasar dan pelatihan sesuai tugasnya?', 'Matriks pelatihan dan daftar hadir', 2, false, 'rendah'],
            ['E2', 'Apakah operator alat berat, juru las, juru ikat, dan teknisi listrik memiliki SIO / sertifikat kompetensi yang berlaku?', 'Daftar personel dan salinan SIO / lisensi', 3, true, 'menengah'],
            ['E3', 'Apakah ada catatan evaluasi kompetensi pekerja baru sebelum ditugaskan?', 'Formulir evaluasi kompetensi', 1, false, 'menengah'],
        ]],
        ['F', 'Alat pelindung diri', [
            ['F1', 'Apakah perusahaan menyediakan APD sesuai bahaya secara gratis dan mengganti yang rusak?', 'Daftar APD per jabatan dan catatan pembagian', 2, false, 'rendah'],
            ['F2', 'Apakah ada pemeriksaan berkala APD khusus (full body harness, respirator, SCBA)?', 'Catatan inspeksi APD khusus', 2, false, 'tinggi'],
        ]],
        ['G', 'Peralatan dan perkakas', [
            ['G1', 'Apakah alat angkat, bejana tekan, dan alat berat diperiksa dan memiliki izin/sertifikat dari pihak berwenang?', 'Sertifikat riksa uji alat (Disnaker / PJK3)', 3, false, 'menengah'],
            ['G2', 'Apakah ada program inspeksi dan pemeliharaan peralatan kerja dan perkakas listrik?', 'Jadwal dan catatan inspeksi alat', 2, false, 'rendah'],
        ]],
        ['H', 'Kesehatan kerja dan jaminan sosial', [
            ['H1', 'Apakah seluruh pekerja terdaftar BPJS Ketenagakerjaan?', 'Bukti kepesertaan BPJS Ketenagakerjaan', 3, true, 'rendah'],
            ['H2', 'Apakah pekerja menjalani pemeriksaan kesehatan sebelum bekerja dan berkala?', 'Rekap hasil medical check-up', 2, false, 'menengah'],
            ['H3', 'Apakah ada petugas P3K bersertifikat dan kotak P3K di lokasi kerja?', 'Sertifikat petugas P3K', 1, false, 'rendah'],
        ]],
        ['I', 'Kinerja K3 dan pelaporan insiden', [
            ['I1', 'Apakah perusahaan mencatat jam kerja, kecelakaan, dan hampir celaka (near miss) setiap bulan?', 'Statistik K3 3 tahun terakhir', 2, false, 'rendah'],
            ['I2', 'Apakah setiap kecelakaan diselidiki untuk menemukan akar penyebab dan tindakan perbaikan?', 'Contoh laporan investigasi insiden', 3, false, 'menengah'],
            ['I3', 'Apakah dalam 3 tahun terakhir ada kecelakaan fatal? Bila ya, apakah investigasi dan tindakannya terdokumentasi?', 'Laporan investigasi dan tindak lanjut', 3, true, 'menengah'],
        ]],
        ['J', 'Tanggap darurat', [
            ['J1', 'Apakah perusahaan memiliki rencana tanggap darurat dan melakukan latihan berkala?', 'Rencana tanggap darurat dan catatan simulasi', 2, false, 'rendah'],
            ['J2', 'Apakah pekerja dilatih menghadapi kebocoran bahan kimia / amonia dan kebakaran?', 'Materi dan daftar hadir pelatihan', 2, false, 'tinggi'],
        ]],
        ['K', 'Pengelolaan subkontraktor', [
            ['K1', 'Apakah subkontraktor diseleksi berdasarkan kemampuan K3 dan diwajibkan mengikuti aturan K3 yang sama?', 'Prosedur seleksi subkontraktor', 2, false, 'menengah'],
        ]],
        ['L', 'Inspeksi dan audit', [
            ['L1', 'Apakah perusahaan melakukan inspeksi K3 rutin di lokasi kerja?', 'Checklist dan laporan inspeksi', 2, false, 'rendah'],
            ['L2', 'Apakah ada audit internal SMK3 / ISO 45001 dan tindak lanjut temuannya?', 'Laporan audit internal dan status tindak lanjut', 2, false, 'menengah'],
            ['L3', 'Apakah perusahaan memiliki sertifikat SMK3 atau ISO 45001 yang masih berlaku?', 'Sertifikat SMK3 / ISO 45001', 2, false, 'tinggi'],
        ]],
        ['M', 'Keamanan pangan, higiene, dan halal', [
            ['M1', 'Apakah pekerja yang akan masuk area produksi dibekali aturan GMP dan kebersihan diri (pakaian kerja, larangan perhiasan, cuci tangan)?', 'Materi briefing GMP dan daftar hadir', 2, false, 'rendah'],
            ['M2', 'Apakah ada pengendalian benda asing dari pekerjaan Anda (serpihan logam, kaca, plastik keras, baut) di area produksi?', 'Prosedur pengendalian benda asing / housekeeping', 3, false, 'menengah'],
            ['M3', 'Apakah bahan yang dibawa ke area produksi (pelumas, cat, bahan kimia) food grade dan tidak mengandung bahan non-halal?', 'Daftar bahan dan sertifikat food grade / halal', 2, false, 'menengah'],
        ]],
        ['N', 'Lingkungan', [
            ['N1', 'Apakah limbah dari pekerjaan (oli bekas, majun, sisa bahan kimia) dikelola sesuai ketentuan limbah B3?', 'Prosedur dan manifes limbah B3', 2, false, 'menengah'],
        ]],
    ],
];
