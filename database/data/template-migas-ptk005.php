<?php

/*
 * Template baku kuesioner prakualifikasi CSMS untuk kegiatan usaha hulu migas.
 * Mengikuti tahap prakualifikasi IOGP Report 423 dan kerangka umum pengelolaan K3LL mitra kerja di PTK-005
 * SKK Migas. Butir, bobot, dan butir kritis adalah usulan awal Rekana dan perlu dicocokkan dengan dokumen
 * PTK-005 resmi serta pedoman CSMS KKKS sebelum dipakai untuk penilaian sungguhan.
 *
 * Elemen A–L dipakai bersama dengan template manufaktur; elemen M dan N khusus migas.
 */
$base = require __DIR__.'/template-iso45001-smk3.php';
$elements = array_values(array_filter($base['elements'], fn ($e) => ! in_array($e[0], ['M', 'N'], true)));

$elements[] = ['M', 'Operasi khusus hulu migas', [
    ['M1', 'Apakah seluruh pekerja yang bekerja di area berpotensi H2S telah mengikuti pelatihan H2S dan penggunaan SCBA yang masih berlaku?', 'Daftar personel dan sertifikat pelatihan H2S', 3, true, 'menengah'],
    ['M2', 'Untuk pekerjaan sumur: apakah supervisor dan driller memiliki sertifikat well control (IWCF/IADC) yang berlaku?', 'Sertifikat well control personel kunci', 3, true, 'tinggi'],
    ['M3', 'Apakah perusahaan memiliki prosedur SIMOPS dan pengalaman bekerja di fasilitas produksi yang sedang beroperasi?', 'Prosedur SIMOPS dan daftar pengalaman', 2, false, 'menengah'],
    ['M4', 'Apakah ada program manajemen perjalanan (journey management) dan pelatihan defensive driving untuk pengemudi?', 'Prosedur journey management dan sertifikat defensive driving', 2, false, 'rendah'],
    ['M5', 'Apakah alat angkat dan peralatan bertekanan (BOP, wellhead, peralatan uji) telah diinspeksi pihak ketiga dan bersertifikat?', 'Sertifikat inspeksi pihak ketiga', 3, false, 'tinggi'],
]];
$elements[] = ['N', 'Lingkungan dan tumpahan', [
    ['N1', 'Apakah perusahaan memiliki prosedur penanganan tumpahan minyak / bahan kimia dan peralatannya tersedia di lokasi?', 'Prosedur dan daftar spill kit', 2, false, 'menengah'],
    ['N2', 'Apakah limbah B3 dari pekerjaan (oli bekas, lumpur, majun) dikelola sesuai ketentuan?', 'Prosedur dan manifes limbah B3', 2, false, 'menengah'],
]];

return [
    'code' => 'CSMS-MIGAS-PTK005',
    'name' => 'CSMS Hulu Migas (kerangka PTK-005 dan IOGP 423)',
    'standard' => 'PTK-005/SKKMA0000/2018/S0 · IOGP 423 · ISO 45001:2018',
    'description' => 'Kuesioner prakualifikasi K3LL mitra kerja KKKS, termasuk H2S, well control, SIMOPS, dan tumpahan.',
    'elements' => $elements,
];
