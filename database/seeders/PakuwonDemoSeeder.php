<?php

namespace Database\Seeders;

use App\Enums\AreaClass;
use App\Enums\ContractMode;
use App\Enums\DocumentType;
use App\Enums\PermitType;
use App\Enums\RiskLevel;

/**
 * Data contoh properti dan konstruksi: PT Pakuwon Jati Tbk, Surabaya.
 * Profil owner (nama, kantor pusat, properti yang dikelola, proyek Pakuwon Mall Semarang) diambil dari Laporan
 * Tahunan 2025 Pakuwon Jati. Nama rekanan, staf, pekerja, skor, dan seluruh aktivitas adalah ilustrasi.
 */
class PakuwonDemoSeeder extends DemoScenario
{
    public static function slug(): string
    {
        return 'pakuwon-surabaya';
    }

    protected function domain(): string
    {
        return 'pakuwon.rekana.test';
    }

    protected function templateCode(): string
    {
        return 'CSMS-KONSTRUKSI-GEDUNG';
    }

    protected function ownerData(): array
    {
        return [
            'name' => 'Pakuwon Jati · Pengembangan dan Pengelolaan Properti',
            'code' => 'PWON-SBY',
            'industry' => 'Pengembang dan pengelola properti (mal, perkantoran, hotel, hunian)',
            'city' => 'Surabaya',
            'address' => 'Pakuwon City Mall Lt. 5, Pakuwon City, Surabaya 60112',
            'hse_email' => 'hse@pakuwon.rekana.test',
            'settings' => ['demo_note' => 'Profil Pakuwon Jati dan properti yang dikelolanya diambil dari Laporan Tahunan 2025. Nama rekanan, staf, pekerja, skor, dan aktivitas adalah ilustrasi untuk menjelaskan fitur Rekana, bukan data resmi Pakuwon Jati.'],
        ];
    }

    protected function inductionTopics(): array
    {
        return ['k3_umum', 'bahaya_area', 'tanggap_darurat', 'apd', 'izin_kerja', 'lingkungan'];
    }

    protected function areaData(): array
    {
        return [
            ['TP', 'Tunjungan Plaza', AreaClass::Umum, 'Mal dan perkantoran yang beroperasi; pekerjaan di area pengunjung wajib dibarikade.'],
            ['PMS', 'Pakuwon Mall Surabaya Barat', AreaClass::Umum, 'Mal beroperasi; pekerjaan malam di luar jam buka.'],
            ['PCM', 'Pakuwon City Mall', AreaClass::Umum, 'Mal dan kantor pusat.'],
            ['SMG', 'Proyek Pakuwon Mall Semarang', AreaClass::Bahaya, 'Proyek konstruksi baru (groundbreaking Agustus 2025): galian basement, tower crane, perancah.'],
            ['ME', 'Plant room M&E', AreaClass::Terbatas, 'Chiller, genset, panel tegangan menengah, ground tank.'],
            ['ATP', 'Atap dan fasad gedung', AreaClass::Terbatas, 'Akses gondola, atap, dan fasad kaca.'],
        ];
    }

    protected function staffData(): array
    {
        return [
            'admin' => ['Lina Wijayanti', 'HSE System Administrator'],
            'pimpinan' => ['Robert Tanuwijaya', 'Project Director'],
            'pemilik' => ['Hendro Gunawan', 'Building Management Manager'],
            'pengadaan' => ['Maria Susanti', 'Procurement Manager'],
            'evaluator' => ['Andre Kurniawan', 'HSE Manager'],
            'pengawas' => ['Yudi Prakoso', 'Site HSE Officer'],
            'auditor' => ['Ratna Sari', 'Internal Audit'],
        ];
    }

    protected function contractorData(): array
    {
        return [
            'bangun' => ['PT Bangun Cipta Jatim', 'Kontraktor utama gedung bertingkat', 'Surabaya', 'Hartono Wibisono', [2023 => [1250000, 0, 1, 5], 2024 => [1380000, 0, 0, 4], 2025 => [1460000, 0, 0, 3]]],
            'gondola' => ['PT Fasad Gondola Nusantara', 'Pembersihan dan perawatan fasad dengan gondola', 'Sidoarjo', 'Ignatius Lukito', [2023 => [72000, 0, 0, 1], 2024 => [78000, 0, 0, 0], 2025 => [84000, 0, 0, 0]]],
            'mekanikal' => ['PT Surya Mekanikal Elektrikal', 'Instalasi dan pemeliharaan M&E', 'Surabaya', 'Benny Halim', [2023 => [156000, 0, 0, 2], 2024 => [162000, 0, 0, 1], 2025 => [171000, 0, 0, 1]]],
            'bersih' => ['PT Prima Layanan Kebersihan', 'Jasa kebersihan dan pengelolaan sampah gedung', 'Surabaya', 'Wulan Dari', [2024 => [640000, 0, 0, 3], 2025 => [668000, 0, 0, 2]]],
            'baja' => ['PT Baja Rangka Semarang', 'Struktur baja dan atap', 'Semarang', 'Tommy Setiadi', [2023 => [94000, 0, 2, 5], 2024 => [101000, 0, 1, 4], 2025 => [110000, 0, 1, 4]]],
            'perancah' => ['PT Perancah Andal Indonesia', 'Sewa dan pemasangan perancah', 'Semarang', 'Agung Prasetya', [2023 => [58000, 1, 2, 4], 2024 => [61000, 0, 1, 3], 2025 => [63000, 0, 1, 3]]],
            'lift' => ['PT Lift dan Eskalator Mandiri', 'Pemeliharaan lift dan eskalator', 'Surabaya', 'Kevin Santoso', [2024 => [41000, 0, 0, 0], 2025 => [44000, 0, 0, 1]]],
        ];
    }

    protected function extraDocuments(): array
    {
        return [
            'bangun' => [[DocumentType::Sbu, 'SBU Konstruksi BG004 kualifikasi besar', 'SBU-BG004/2024/1187', today()->addYears(2)], [DocumentType::Iso45001, 'Sertifikat ISO 45001:2018', 'ID-OHS-5520', today()->addYear()]],
            'gondola' => [[DocumentType::Lainnya, 'Sertifikat riksa uji gondola', 'RU/GND/2026/031', today()->addDays(18)]],
        ];
    }

    protected function workerData(): array
    {
        return [
            'bangun' => [
                ['Sugeng Riyanto', 'Site manager', ['Ahli K3 Konstruksi', 'SKK Manajer Proyek']],
                ['Wawan Setiadi', 'Pelaksana struktur', ['SKK Pelaksana Gedung']],
                ['Mulyadi', 'Operator tower crane', ['SIO Operator Tower Crane Kelas I']],
                ['Rizky Ramadhan', 'Rigger', ['Juru Ikat (Rigger) Tingkat II']],
                ['Hari Purnomo', 'Tukang besi', ['Pekerja Ketinggian Tingkat 1']],
                ['Joni Iskandar', 'Juru las', ['Juru Las SMAW 3G']],
                ['Edi Kurniawan', 'Safety officer', ['Ahli K3 Konstruksi Muda']],
                ['Samsul Arifin', 'Tukang', ['Pekerja Ketinggian Tingkat 1']],
            ],
            'gondola' => [
                ['Rudi Hartanto', 'Operator gondola', ['SIO Operator Gondola', 'Pekerja Ketinggian Tingkat 2']],
                ['Anton Wijaya', 'Teknisi fasad', ['Pekerja Ketinggian Tingkat 2']],
                ['Toni Saputra', 'Teknisi fasad', ['Pekerja Ketinggian Tingkat 2']],
            ],
            'mekanikal' => [
                ['Bagus Prayitno', 'Supervisor M&E', ['Ahli K3 Listrik']],
                ['Dedi Firmansyah', 'Teknisi listrik', ['Teknisi K3 Listrik']],
                ['Yanto Saputro', 'Teknisi mekanik', ['Petugas K3 Ruang Terbatas']],
                ['Fikri Maulana', 'Helper', ['K3 Umum']],
            ],
            'bersih' => [['Siti Aminah', 'Supervisor kebersihan', ['K3 Umum']], ['Nanik Lestari', 'Petugas kebersihan', ['K3 Umum']]],
            'lift' => [['Kurniawan Hadi', 'Teknisi lift', ['Teknisi Lift dan Eskalator']]],
        ];
    }

    protected function pqData(): array
    {
        return [
            'bangun' => [RiskLevel::Tinggi, 4, null, 'disetujui'],
            'gondola' => [RiskLevel::Tinggi, 4, null, 'disetujui'],
            'mekanikal' => [RiskLevel::Tinggi, 4, null, 'disetujui'],
            'bersih' => [RiskLevel::Rendah, 3, null, 'disetujui'],
            'perancah' => [RiskLevel::Tinggi, 2, 'M2', 'ditolak'],
            'baja' => [RiskLevel::Tinggi, 3, null, 'diajukan'],
            'lift' => [RiskLevel::Menengah, 3, null, 'klarifikasi'],
        ];
    }

    protected function clarificationText(string $key): string
    {
        return 'Mohon lampirkan sertifikat riksa uji lift dan eskalator terbaru serta prosedur LOTO di ruang mesin lift (butir G1 dan D2).';
    }

    protected function criticalNote(string $key): string
    {
        return 'Perancah tidak diperiksa teknisi bersertifikat dan tanpa tanda layak pakai; kecelakaan fatal 2023 akibat perancah runtuh.';
    }

    protected function planData(): array
    {
        return [
            'struktur' => [
                'title' => 'Pekerjaan struktur atas Pakuwon Mall Semarang', 'area' => 'SMG', 'mode' => ContractMode::Mode2,
                'days' => 180, 'workers' => 120, 'value' => 86_500_000_000, 'flow' => 'contract', 'contractor' => 'bangun',
                'number' => 'PWON/PRJ-SMG/2026/017', 'start_offset' => -20, 'hse' => [4, 4, 4, 4, 3, 3], 'technical' => 88,
                'lines' => [
                    ['Pengecoran dan bekisting pelat lantai tinggi', 'ketinggian', 4, 5, 'Jaring pengaman, guard rail tepi, full body harness'],
                    ['Pengangkatan material dengan tower crane', 'angkat_berat', 3, 5, 'Lift plan, rigger bersertifikat, zona larangan di bawah beban'],
                    ['Pengelasan sambungan besi', 'kerja_panas', 3, 3, 'Izin kerja panas, APAR, fire watch'],
                ],
                'hse_summary' => 'RKK proyek: organisasi K3 dengan rasio 1 petugas per 30 pekerja, rencana pengangkatan tower crane, rencana tanggap darurat proyek.',
                'kickoff' => 'Kick-off di proyek Semarang: RKK disahkan; dibahas zona tower crane, jalur pejalan kaki, dan pengamanan pagar proyek.',
                'bridging' => "Owner (Pakuwon): izin kerja, pengawasan K3 owner, koordinasi dengan warga sekitar.\nKontraktor: RKK, SMKK, petugas K3 per zona, laporan harian.",
                'induction' => 'Induksi K3 konstruksi · Proyek Pakuwon Mall Semarang', 'fail_index' => 7,
            ],
            'fasad' => [
                'title' => 'Pembersihan berkala fasad kaca Tunjungan Plaza dengan gondola', 'area' => 'ATP', 'mode' => ContractMode::Mode2,
                'days' => 120, 'workers' => 3, 'value' => 640_000_000, 'flow' => 'contract', 'contractor' => 'gondola',
                'number' => 'PWON/BM-TP/2026/044', 'start_offset' => -40, 'hse' => [4, 4, 4, 4, 4, 3], 'technical' => 82,
                'lines' => [
                    ['Bekerja di gondola pada fasad gedung tinggi', 'ketinggian', 3, 5, 'Gondola riksa uji, lifeline independen, cek angin'],
                    ['Benda jatuh ke area pengunjung', 'lainnya', 2, 4, 'Barikade di bawah, kerja di luar jam buka'],
                ],
                'kickoff' => 'Kick-off di Tunjungan Plaza: dibahas jadwal kerja malam, batas kecepatan angin, dan barikade area drop-off.',
                'bridging' => "Owner: akses atap, penutupan area bawah, izin kerja ketinggian.\nKontraktor: gondola laik, operator SIO, rescue plan.",
                'induction' => 'Induksi K3 kerja ketinggian · Tunjungan Plaza',
            ],
            'chiller' => [
                'title' => 'Overhaul chiller dan pembersihan ground tank Pakuwon City Mall', 'area' => 'ME', 'mode' => ContractMode::Mode2,
                'days' => 25, 'workers' => 8, 'value' => 1_850_000_000, 'flow' => 'tender', 'invite' => ['mekanikal'],
                'lines' => [
                    ['Masuk ground tank untuk pembersihan', 'ruang_terbatas', 3, 5, 'Izin ruang terbatas, uji gas, standby person'],
                    ['Isolasi panel listrik chiller', 'listrik', 3, 4, 'LOTO, teknisi K3 listrik'],
                ],
            ],
            'kebersihan' => [
                'title' => 'Jasa kebersihan gedung Pakuwon Mall 2027', 'area' => 'PMS', 'mode' => ContractMode::Mode3,
                'days' => 365, 'workers' => 60, 'value' => 5_400_000_000, 'flow' => 'assessed',
                'lines' => [['Penggunaan bahan kimia pembersih di area pengunjung', 'bahan_kimia', 2, 2, 'SDS, APD sarung tangan, rambu lantai basah']],
            ],
        ];
    }

    protected function permitData(): array
    {
        return [
            ['fasad', PermitType::Ketinggian, 'Pembersihan fasad kaca sisi timur Tunjungan Plaza 6', ['Rudi Hartanto', 'Anton Wijaya'], now()->startOfHour(), 8, null, null, 'aktif'],
            ['struktur', PermitType::KerjaPanas, 'Pengelasan sambungan besi kolom lantai 3', ['Joni Iskandar', 'Edi Kurniawan'], now()->subDays(5)->setTime(8, 0), 8, null, null, 'ditutup'],
            ['struktur', PermitType::Ketinggian, 'Pemasangan bekisting tepi pelat lantai 4', ['Hari Purnomo', 'Wawan Setiadi', 'Edi Kurniawan'], now()->addDay()->setTime(7, 0), 8, null, null, 'diajukan'],
            ['struktur', PermitType::Umum, 'Pengangkatan bundel besi dengan tower crane', ['Mulyadi', 'Rizky Ramadhan'], now()->addDays(2)->setTime(7, 0), 6, null, null, 'draf'],
        ];
    }
}
