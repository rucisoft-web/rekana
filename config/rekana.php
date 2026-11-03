<?php

/*
 * Nilai awal aturan bisnis Rekana. Angka-angka ini adalah usulan rancangan, bukan kutipan regulasi;
 * sesuaikan dengan pedoman CSMS yang dipakai owner (PTK 005, Pedoman Pertamina, prosedur internal).
 */
return [
    'mfa_required' => env('REKANA_MFA_REQUIRED', false),
    'upload_max_kb' => (int) env('REKANA_UPLOAD_MAX_KB', 20480),

    // Matriks risiko 5x5 (kemungkinan x keparahan) dan bahaya yang langsung menjadikan pekerjaan Tinggi.
    'risk' => [
        'medium_from' => 6,
        'high_from' => 15,
        'force_high' => ['ruang_terbatas', 'amonia', 'kerja_panas', 'ketinggian', 'h2s', 'kerja_sumur'],
    ],

    // Skor prakualifikasi (persen) minimum untuk tiap kategori risiko yang boleh dikerjakan.
    'pq' => [
        'max_item_score' => 4,
        'thresholds' => ['rendah' => 35, 'menengah' => 55, 'tinggi' => 75],
        'certificate_years' => 2,
    ],

    // Ambang dasbor monitoring: rekanan di atas ambang ditandai untuk ditinjau.
    'dashboard' => [
        'ltifr_alert' => 1.5,   // per 1.000.000 jam kerja; di atas 2x ambang = tinjau ulang
        'trir_alert' => 3.0,    // per 200.000 jam kerja
        'expiry_days' => 60,    // jendela peringatan sertifikat dan dokumen
        'weeks' => 12,          // rentang tren izin kerja
    ],

    // Rubrik penilaian HSE Plan saat seleksi; tiap kriteria dinilai 0-4.
    'hse_plan_rubric' => [
        'organisasi' => 'Organisasi dan tanggung jawab K3 proyek',
        'risiko' => 'Identifikasi bahaya dan pengendalian (JSA/HIRADC) sesuai lingkup',
        'kompetensi' => 'Kompetensi dan sertifikasi personel kunci',
        'peralatan' => 'Peralatan, APD, dan inspeksi alat',
        'darurat' => 'Tanggap darurat dan pelaporan insiden',
        'pangan' => 'Pengendalian khusus lokasi (keamanan pangan dan halal, atau H2S dan SIMOPS di fasilitas migas)',
    ],

    // Batas uji gas ruang terbatas (nilai awal umum; sesuaikan prosedur owner).
    'gas_limits' => [
        'o2_min' => 19.5,
        'o2_max' => 23.5,
        'lel_max' => 10,
        'h2s_max' => 10,
        'co_max' => 25,
    ],

    'permit_max_hours' => 12,
    'expiry_warning_days' => 30,

    'induction_topics' => [
        'k3_umum' => 'K3 umum dan peraturan lokasi',
        'bahaya_area' => 'Bahaya khusus area (amonia, boiler, bahan kimia)',
        'tanggap_darurat' => 'Tanggap darurat, jalur evakuasi, titik kumpul',
        'apd' => 'APD wajib',
        'izin_kerja' => 'Sistem izin kerja dan LOTO',
        'keamanan_pangan' => 'Keamanan pangan dan GMP (benda asing, kebersihan diri)',
        'kaca_plastik' => 'Pengendalian kaca dan plastik keras',
        'halal' => 'Area dan aturan halal',
        'lingkungan' => 'Lingkungan dan pengelolaan limbah',
        'h2s' => 'Kesadaran H2S dan penggunaan SCBA',
        'simops' => 'SIMOPS dan bekerja di fasilitas hidup',
        'journey' => 'Manajemen perjalanan (journey management) dan defensive driving',
    ],

    'admin' => [
        'email' => env('REKANA_ADMIN_EMAIL'),
        'password' => env('REKANA_ADMIN_PASSWORD'),
    ],

    'demo' => [
        'enabled' => env('REKANA_DEMO', false),
        'password' => env('REKANA_DEMO_PASSWORD', 'Demo#2026rekana'),
        'guest' => env('REKANA_DEMO_GUEST', true),
    ],
];
