<?php

/*
 * Template baku kuesioner prakualifikasi CSMS untuk pembangunan dan pengelolaan gedung (mal, hotel, apartemen).
 * Mengacu ISO 45001:2018 kl. 8.1.4, SMK3 PP 50/2012, kerangka SMKK (Permen PUPR 10/2021), dan tahap prakualifikasi
 * IOGP Report 423. Butir, bobot, dan butir kritis adalah usulan awal Rekana; owner dapat menyalin dan menyesuaikannya.
 *
 * Elemen A–L dipakai bersama dengan template manufaktur; elemen M dan N khusus konstruksi dan gedung beroperasi.
 */
$base = require __DIR__.'/template-iso45001-smk3.php';
$elements = array_values(array_filter($base['elements'], fn ($e) => ! in_array($e[0], ['M', 'N'], true)));

$elements[] = ['M', 'Konstruksi dan pekerjaan di gedung beroperasi', [
    ['M1', 'Apakah perusahaan memiliki Rencana Keselamatan Konstruksi (RKK) dan Ahli K3 Konstruksi untuk proyek sejenis?', 'Contoh RKK dan sertifikat Ahli K3 Konstruksi', 3, false, 'menengah'],
    ['M2', 'Apakah perancah dirakit dan diperiksa oleh teknisi perancah bersertifikat, dengan tanda layak pakai?', 'Sertifikat teknisi perancah dan formulir inspeksi', 3, true, 'tinggi'],
    ['M3', 'Apakah operator tower crane, mobile crane, dan gondola memiliki SIO, dan alatnya memiliki sertifikat riksa uji yang berlaku?', 'SIO operator dan sertifikat riksa uji alat', 3, true, 'tinggi'],
    ['M4', 'Untuk pekerjaan di mal atau hotel yang beroperasi: bagaimana perusahaan melindungi pengunjung (pembatas, jam kerja, jalur material)?', 'Rencana pengamanan area publik', 2, false, 'menengah'],
    ['M5', 'Apakah ada prosedur bila pekerjaan harus menonaktifkan sementara sistem proteksi kebakaran (fire impairment)?', 'Prosedur fire impairment dan contoh formulir', 2, false, 'menengah'],
]];
$elements[] = ['N', 'Lingkungan dan kebersihan lokasi', [
    ['N1', 'Apakah limbah konstruksi dan limbah B3 dikelola, dan debu serta kebisingan dikendalikan?', 'Prosedur pengelolaan limbah, debu, dan kebisingan', 2, false, 'menengah'],
]];

return [
    'code' => 'CSMS-KONSTRUKSI-GEDUNG',
    'name' => 'CSMS Konstruksi dan Pengelolaan Gedung (SMKK dan ISO 45001)',
    'standard' => 'ISO 45001:2018 kl. 8.1.4 · PP 50/2012 SMK3 · Permen PUPR 10/2021 SMKK · IOGP 423',
    'description' => 'Kuesioner prakualifikasi K3 kontraktor pembangunan dan pemeliharaan mal, hotel, dan apartemen.',
    'elements' => $elements,
];
