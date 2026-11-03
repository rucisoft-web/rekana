<?php

namespace Database\Seeders;

use App\Enums\AreaClass;
use App\Enums\ContractMode;
use App\Enums\DocumentType;
use App\Enums\PermitType;
use App\Enums\RiskLevel;

/**
 * Data contoh hulu migas: MedcoEnergi, aset PSC Corridor (Sumatra Selatan).
 * Profil owner (nama, wilayah operasi, kantor pusat) diambil dari Laporan Tahunan 2025 MedcoEnergi. Nama rekanan,
 * staf, pekerja, skor, dan seluruh aktivitas adalah ilustrasi, bukan data resmi MedcoEnergi.
 */
class MedcoDemoSeeder extends DemoScenario
{
    public static function slug(): string
    {
        return 'medco-corridor';
    }

    protected function domain(): string
    {
        return 'medco.rekana.test';
    }

    protected function templateCode(): string
    {
        return 'CSMS-MIGAS-PTK005';
    }

    protected function ownerData(): array
    {
        return [
            'name' => 'MedcoEnergi · Aset Corridor, Sumatra Selatan',
            'code' => 'MEDCO-CRD',
            'industry' => 'Hulu minyak dan gas bumi (KKKS)',
            'city' => 'Sumatra Selatan',
            'address' => 'Kantor pusat: Gedung The Energy, Jakarta. Operasi: PSC Corridor (Suban, Grissik, Sumpal, Rebonjaro), Sumatra Selatan.',
            'hse_email' => 'hse@medco.rekana.test',
            'settings' => ['demo_note' => 'Profil MedcoEnergi dan wilayah operasinya diambil dari Laporan Tahunan 2025. Nama rekanan, staf, pekerja, skor, dan aktivitas adalah ilustrasi untuk menjelaskan fitur Rekana, bukan data resmi MedcoEnergi.'],
        ];
    }

    protected function inductionTopics(): array
    {
        return ['k3_umum', 'bahaya_area', 'h2s', 'simops', 'tanggap_darurat', 'apd', 'izin_kerja', 'journey', 'lingkungan'];
    }

    protected function areaData(): array
    {
        return [
            ['GRS', 'Grissik Gas Plant', AreaClass::Bahaya, 'Fasilitas pemrosesan gas; area klasifikasi bahaya dan potensi H2S.'],
            ['SBN', 'Suban Gas Plant dan klaster sumur', AreaClass::Bahaya, 'Sumur produksi gas Suban dan fasilitas pemrosesannya.'],
            ['SMP', 'Lapangan Sumpal', AreaClass::Bahaya, 'Sumur, flowline, dan stasiun pengumpul.'],
            ['RBJ', 'Lapangan Rebonjaro', AreaClass::Bahaya, 'Sumur dan fasilitas produksi.'],
            ['PPA', 'Koridor jalur pipa', AreaClass::Terbatas, 'Right of way jalur pipa; pekerjaan galian dan las sambung.'],
            ['LOG', 'Base camp dan gudang logistik', AreaClass::Umum, 'Akomodasi, gudang material, dan pool kendaraan.'],
            ['YRD', 'Workshop dan yard', AreaClass::Umum, 'Bengkel mekanik, listrik, dan area fabrikasi.'],
        ];
    }

    protected function staffData(): array
    {
        return [
            'admin' => ['Rina Kusumawati', 'HSE System Coordinator'],
            'pimpinan' => ['Bambang Hartono', 'Field Manager Corridor'],
            'pemilik' => ['Agus Setiawan', 'Facility Engineering Superintendent'],
            'pengadaan' => ['Dewi Lestari', 'Contract and Procurement Specialist'],
            'evaluator' => ['Fajar Nugroho', 'HSE Contractor Management Specialist'],
            'pengawas' => ['Sri Wahyuni', 'HSE Field Supervisor'],
            'auditor' => ['Hendra Wijaya', 'Internal Auditor'],
        ];
    }

    protected function contractorData(): array
    {
        return [
            'swell' => ['PT Sriwijaya Well Services', 'Workover dan intervensi sumur', 'Palembang', 'Yusuf Prasetyo', [2023 => [412000, 0, 0, 2], 2024 => [438000, 0, 0, 1], 2025 => [455000, 0, 0, 1]]],
            'musipipa' => ['PT Musi Pipa Konstruksi', 'Konstruksi dan pemeliharaan jalur pipa', 'Palembang', 'Wahyu Hidayat', [2023 => [210000, 0, 1, 3], 2024 => [236000, 0, 1, 4], 2025 => [248000, 0, 1, 3]]],
            'andalas' => ['PT Andalas Inspeksi Teknik', 'Inspeksi NDT dan sertifikasi peralatan', 'Prabumulih', 'Nur Aini', [2023 => [88000, 0, 0, 1], 2024 => [93000, 0, 0, 0], 2025 => [101000, 0, 0, 1]]],
            'transport' => ['PT Palembang Transportasi Energi', 'Angkutan personel dan logistik lapangan', 'Palembang', 'Siti Rahayu', [2023 => [520000, 0, 0, 3], 2024 => [544000, 0, 0, 2], 2025 => [561000, 0, 0, 2]]],
            'lematang' => ['PT Lematang Elektrik Instrumentasi', 'Kelistrikan dan instrumentasi (E&I)', 'Muara Enim', 'Galih Permana', [2023 => [64000, 0, 0, 1], 2024 => [71000, 0, 0, 1], 2025 => [80000, 0, 0, 0]]],
            'katering' => ['PT Sumsel Katering Remote', 'Katering dan layanan base camp', 'Musi Banyuasin', 'Andi Gunawan', [2024 => [190000, 0, 0, 2], 2025 => [204000, 0, 0, 1]]],
            'banyuasin' => ['PT Banyuasin Konstruksi Sipil', 'Pekerjaan sipil dan jalan akses', 'Sekayu', 'Rudi Santoso', [2023 => [98000, 1, 3, 6], 2024 => [102000, 0, 2, 5], 2025 => [107000, 0, 2, 4]]],
        ];
    }

    protected function extraDocuments(): array
    {
        return [
            'swell' => [[DocumentType::Iso45001, 'Sertifikat ISO 45001:2018', 'ID-OHS-4471', today()->addYears(2)], [DocumentType::Smk3, 'Sertifikat SMK3 tingkat lanjutan', 'SMK3/2024/221', today()->addDays(21)]],
            'musipipa' => [[DocumentType::Smk3, 'Sertifikat SMK3 tingkat transisi', 'SMK3/2023/088', today()->addYear()]],
        ];
    }

    protected function workerData(): array
    {
        return [
            'swell' => [
                ['Joko Susilo', 'Rig superintendent', ['Well Control IWCF Level 4', 'H2S Awareness']],
                ['Slamet Riyadi', 'Driller', ['Well Control IWCF Level 3', 'H2S Awareness']],
                ['Ahmad Fauzi', 'Derrickman', ['Pekerja Ketinggian Tingkat 2', 'H2S Awareness']],
                ['Eko Purnomo', 'Floorman', ['H2S Awareness']],
                ['Budi Santoso', 'Crane operator', ['SIO Operator Crane Kelas II', 'H2S Awareness']],
                ['Imam Syafii', 'HSE officer rig', ['Ahli K3 Migas', 'H2S Awareness']],
                ['Teguh Wibowo', 'Mekanik rig', ['H2S Awareness']],
                ['Dimas Saputra', 'Floorman', ['H2S Awareness']],
            ],
            'andalas' => [
                ['Fitri Handayani', 'Inspektor NDT', ['NDT UT Level II', 'H2S Awareness']],
                ['Hadi Susanto', 'Inspektor NDT', ['NDT RT Level II', 'Petugas Proteksi Radiasi']],
                ['Yoga Pratama', 'Teknisi ruang terbatas', ['Petugas K3 Ruang Terbatas', 'H2S Awareness']],
                ['Doni Setiawan', 'Helper', ['H2S Awareness']],
            ],
            'musipipa' => [
                ['Rahmat Hidayat', 'Mandor las pipa', ['Juru Las Pipa 6G']],
                ['Arif Rahman', 'Juru las pipa', ['Juru Las Pipa 6G']],
                ['Bayu Kurniawan', 'Operator side boom', ['SIO Alat Berat']],
            ],
            'transport' => [['Suparno', 'Pengemudi', ['Defensive Driving']], ['Darmaji', 'Pengemudi', ['Defensive Driving']]],
            'lematang' => [['Galih Saputra', 'Teknisi instrumen', ['Teknisi K3 Listrik']]],
        ];
    }

    protected function pqData(): array
    {
        return [
            'swell' => [RiskLevel::Tinggi, 4, null, 'disetujui'],
            'andalas' => [RiskLevel::Tinggi, 4, null, 'disetujui'],
            'musipipa' => [RiskLevel::Tinggi, 3, null, 'disetujui'], // skor cukup hanya untuk Menengah
            'transport' => [RiskLevel::Rendah, 3, null, 'disetujui'],
            'banyuasin' => [RiskLevel::Menengah, 2, 'I3', 'ditolak'],
            'lematang' => [RiskLevel::Menengah, 3, null, 'diajukan'],
            'katering' => [RiskLevel::Menengah, 3, null, 'klarifikasi'],
        ];
    }

    protected function clarificationText(string $key): string
    {
        return 'Mohon lampirkan sertifikat higiene sanitasi penjamah makanan, hasil uji air bersih base camp, dan prosedur journey management untuk pengiriman bahan makanan (butir H2 dan M4).';
    }

    protected function criticalNote(string $key): string
    {
        return 'Kecelakaan fatal 2023 di proyek jalan akses tidak dilaporkan; dokumen investigasi dan tindak lanjut tidak tersedia.';
    }

    protected function planData(): array
    {
        return [
            'workover' => [
                'title' => 'Program workover dan penggantian tubing sumur Suban', 'area' => 'SBN', 'mode' => ContractMode::Mode2,
                'days' => 120, 'workers' => 18, 'value' => 9_850_000_000, 'flow' => 'contract', 'contractor' => 'swell',
                'number' => 'MEPI/CRD/2026/0412', 'start_offset' => -70, 'hse' => [4, 4, 4, 3, 4, 4], 'technical' => 86,
                'lines' => [
                    ['Rig-up unit workover dan pemasangan BOP', 'kerja_sumur', 3, 5, 'BOP diuji tekan, driller bersertifikat IWCF'],
                    ['Membuka sumur dengan potensi gas H2S', 'h2s', 3, 5, 'Detektor H2S personal, SCBA, windsock, briefing arah angin'],
                    ['Kerja derrickman di monkey board', 'ketinggian', 3, 4, 'Full body harness, fall arrester, rescue plan'],
                    ['Pengangkatan tubing dengan crane', 'angkat_berat', 2, 4, 'Lift plan, juru ikat bersertifikat'],
                ],
                'hse_summary' => 'HSE Plan workover: organisasi K3 rig, bridging document dengan sistem K3 Medco, rencana kontingensi H2S dan well control.',
                'kickoff' => 'Kick-off di Suban: bridging document ditandatangani; dibahas rencana H2S, titik kumpul, dan SIMOPS dengan operasi produksi.',
                'bridging' => "Owner (Medco): isolasi flowline dan fasilitas produksi, izin kerja, uji gas, koordinasi SIMOPS.\nKontraktor: sistem K3 rig, well control, pengawas K3 per shift, laporan harian.",
                'induction' => 'Induksi K3, H2S, dan SIMOPS · Workover Suban', 'fail_index' => 6,
            ],
            'ndt' => [
                'title' => 'Inspeksi NDT dan internal separator Grissik Gas Plant', 'area' => 'GRS', 'mode' => ContractMode::Mode2,
                'days' => 60, 'workers' => 4, 'value' => 1_240_000_000, 'flow' => 'contract', 'contractor' => 'andalas',
                'number' => 'MEPI/CRD/2026/0398', 'start_offset' => -35, 'hse' => [4, 4, 4, 4, 3, 3], 'technical' => 84,
                'lines' => [
                    ['Masuk ke dalam separator V-101 untuk inspeksi visual', 'ruang_terbatas', 3, 5, 'Izin ruang terbatas, isolasi, uji gas, standby person'],
                    ['Radiografi sambungan las', 'bahan_kimia', 2, 4, 'Petugas proteksi radiasi, barikade radius aman'],
                    ['Bekerja di fasilitas yang beroperasi', 'hidrokarbon', 3, 4, 'SIMOPS, detektor gas portabel'],
                ],
                'kickoff' => 'Kick-off di Grissik Gas Plant: dibahas isolasi separator V-101 dan jadwal shutdown parsial.',
                'bridging' => "Owner: pengosongan dan isolasi separator, uji gas awal, izin kerja.\nKontraktor: prosedur ruang terbatas, peralatan NDT, petugas proteksi radiasi.",
                'induction' => 'Induksi K3, H2S, dan ruang terbatas · Grissik Gas Plant',
            ],
            'flowline' => [
                'title' => 'Penggantian segmen flowline Lapangan Sumpal', 'area' => 'SMP', 'mode' => ContractMode::Mode2,
                'days' => 35, 'workers' => 12, 'value' => 4_300_000_000, 'flow' => 'tender', 'invite' => ['swell'],
                'lines' => [
                    ['Pengelasan sambungan pipa di jalur hidup', 'kerja_panas', 3, 5, 'Hot tapping procedure, uji gas, fire watch'],
                    ['Galian di atas pipa bertekanan', 'penggalian', 3, 4, 'Deteksi pipa, galian manual dekat pipa'],
                ],
            ],
            'transport' => [
                'title' => 'Jasa angkutan personel dan logistik lapangan 2027', 'area' => 'LOG', 'mode' => ContractMode::Mode3,
                'days' => 365, 'workers' => 30, 'value' => 7_200_000_000, 'flow' => 'assessed',
                'lines' => [['Mengemudi di jalan lapangan dan jalan umum', 'kendaraan', 3, 3, 'Journey management, defensive driving, pembatas kecepatan']],
            ],
        ];
    }

    protected function permitData(): array
    {
        return [
            ['workover', PermitType::Ketinggian, 'Kerja derrickman di monkey board unit workover', ['Ahmad Fauzi', 'Imam Syafii'], now()->startOfHour(), 8, null, null, 'aktif'],
            ['workover', PermitType::Loto, 'Isolasi flowline dan wellhead sumur sebelum rig-up', ['Joko Susilo', 'Slamet Riyadi'], now()->subDays(6)->setTime(7, 0), 8, null, 'Katup wing dan master wellhead ditutup dan digembok; flowline diisolasi di manifold dengan blind.', 'ditutup'],
            ['ndt', PermitType::RuangTerbatas, 'Inspeksi visual bagian dalam separator V-101', ['Yoga Pratama', 'Fitri Handayani', 'Doni Setiawan'], now()->addDay()->setTime(8, 0), 6, ['o2' => 20.9, 'lel' => 0, 'h2s' => 0, 'co' => 2], 'Katup inlet, outlet, dan drain V-101 ditutup, digembok, dan dipasang blind; instrumen diisolasi di panel.', 'diajukan'],
            ['workover', PermitType::KerjaPanas, 'Pengelasan dudukan peralatan di lokasi rig', ['Teguh Wibowo', 'Imam Syafii'], now()->addDays(2)->setTime(8, 0), 6, null, null, 'draf'],
        ];
    }
}
