<?php

namespace Database\Seeders;

use App\Enums\AreaClass;
use App\Enums\ContractMode;
use App\Enums\DocumentType;
use App\Enums\PermitType;
use App\Enums\RiskLevel;

/**
 * Data contoh manufaktur pangan: PT Garudafood Putra Putri Jaya Tbk, Business Unit Gresik (pabrik biskuit).
 * Profil owner (nama, kantor pusat Wisma Garudafood, alamat Business Unit Gresik, sertifikasi SMK3 dan FSSC 22000,
 * penerapan CSMS di seluruh area operasional) diambil dari Laporan Tahunan dan Keberlanjutan 2025 Garudafood.
 * Nama rekanan, staf, pekerja, skor, dan seluruh aktivitas adalah ilustrasi.
 */
class GarudafoodDemoSeeder extends DemoScenario
{
    public static function slug(): string
    {
        return 'garudafood-gresik';
    }

    protected function domain(): string
    {
        return 'garudafood.rekana.test';
    }

    protected function templateCode(): string
    {
        return 'CSMS-ISO45001-SMK3-PANGAN';
    }

    protected function ownerData(): array
    {
        return [
            'name' => 'Garudafood · Business Unit Gresik',
            'code' => 'GOOD-GRS',
            'industry' => 'Manufaktur makanan dan minuman (biskuit)',
            'city' => 'Gresik',
            'address' => 'Jl. Krikilan KM 28, Krikilan, Driyorejo, Kabupaten Gresik, Jawa Timur 61177. Kantor pusat: Wisma Garudafood, Jl. Bintaro Raya No. 10A, Jakarta Selatan 12240.',
            'hse_email' => 'hse@garudafood.rekana.test',
            'settings' => ['demo_note' => 'Profil Garudafood dan Business Unit Gresik diambil dari Laporan Tahunan dan Keberlanjutan 2025. Nama rekanan, staf, pekerja, skor, dan aktivitas adalah ilustrasi untuk menjelaskan fitur Rekana, bukan data resmi Garudafood.'],
        ];
    }

    protected function inductionTopics(): array
    {
        return ['k3_umum', 'bahaya_area', 'tanggap_darurat', 'apd', 'izin_kerja', 'keamanan_pangan', 'kaca_plastik', 'halal', 'lingkungan'];
    }

    protected function areaData(): array
    {
        return [
            ['PRD', 'Lini produksi biskuit (mixing, oven, cooling)', AreaClass::ProduksiPangan, 'Area GMP: wajib cuci tangan, penutup rambut, larangan kaca dan perhiasan.'],
            ['PKG', 'Area pengemasan', AreaClass::ProduksiPangan, 'Mesin pengemas otomatis; pengendalian benda asing ketat.'],
            ['SIL', 'Silo tepung dan gula', AreaClass::Bahaya, 'Ruang terbatas dan bahaya ledakan debu.'],
            ['UTL', 'Utilitas (boiler, kompresor, LPG/gas burner oven)', AreaClass::Bahaya, 'Bejana tekan, jalur gas burner, panel listrik utama.'],
            ['WH', 'Gudang bahan baku dan barang jadi', AreaClass::Umum, 'Forklift dan rak tinggi.'],
            ['IPL', 'Instalasi pengolahan air limbah', AreaClass::Terbatas, 'Bak aerasi dan sumur pompa.'],
        ];
    }

    protected function staffData(): array
    {
        return [
            'admin' => ['Novi Anggraini', 'HSE Administrator'],
            'pimpinan' => ['Hadi Prasetyo', 'Plant Manager BU Gresik'],
            'pemilik' => ['Rudy Hermawan', 'Engineering Manager'],
            'pengadaan' => ['Yuliana Putri', 'Purchasing Supervisor'],
            'evaluator' => ['Taufik Hidayat', 'HSE Superintendent'],
            'pengawas' => ['Maya Sari', 'HSE Officer'],
            'auditor' => ['Eko Wahyudi', 'QA dan Food Safety Auditor'],
        ];
    }

    protected function contractorData(): array
    {
        return [
            'mesin' => ['PT Gresik Teknik Mesin Pangan', 'Perawatan mesin produksi dan oven', 'Gresik', 'Ferry Kusuma', [2023 => [96000, 0, 0, 1], 2024 => [104000, 0, 0, 1], 2025 => [112000, 0, 0, 0]]],
            'silo' => ['PT Silo Bersih Indonesia', 'Pembersihan silo dan tangki industri', 'Sidoarjo', 'Dwi Santoso', [2023 => [38000, 0, 0, 1], 2024 => [41000, 0, 0, 0], 2025 => [45000, 0, 0, 0]]],
            'listrik' => ['PT Driyorejo Elektrik', 'Instalasi dan pemeliharaan listrik industri', 'Gresik', 'Lukman Hakim', [2023 => [52000, 0, 0, 1], 2024 => [55000, 0, 0, 1], 2025 => [58000, 0, 0, 0]]],
            'hama' => ['PT Jatim Pest Control', 'Pengendalian hama area pangan', 'Surabaya', 'Rina Marlina', [2024 => [21000, 0, 0, 0], 2025 => [23000, 0, 0, 0]]],
            'boiler' => ['PT Ketel Uap Jaya', 'Perawatan boiler dan bejana tekan', 'Surabaya', 'Antonius Halim', [2023 => [33000, 0, 1, 3], 2024 => [35000, 0, 1, 2], 2025 => [37000, 0, 0, 2]]],
            'sipil' => ['PT Krikilan Karya Sipil', 'Pekerjaan sipil dan renovasi bangunan', 'Gresik', 'Bambang Susilo', [2023 => [61000, 0, 3, 7], 2024 => [64000, 0, 2, 6], 2025 => [66000, 0, 2, 5]]],
            'limbah' => ['PT Mitra Olah Limbah', 'Operasi dan perawatan IPAL', 'Mojokerto', 'Retno Wulandari', [2024 => [27000, 0, 0, 1], 2025 => [29000, 0, 0, 1]]],
        ];
    }

    protected function extraDocuments(): array
    {
        return [
            'mesin' => [[DocumentType::Smk3, 'Sertifikat SMK3 tingkat lanjutan', 'SMK3/2024/317', today()->addYears(2)]],
            'silo' => [[DocumentType::Iso45001, 'Sertifikat ISO 45001:2018', 'ID-OHS-6012', today()->addDays(25)]],
        ];
    }

    protected function workerData(): array
    {
        return [
            'mesin' => [
                ['Agus Salim', 'Supervisor mekanik', ['Ahli K3 Umum']],
                ['Heru Susanto', 'Teknisi oven', ['Juru Las SMAW 2G', 'Higiene Sanitasi Pangan']],
                ['Nanang Kosim', 'Teknisi mekanik', ['Higiene Sanitasi Pangan']],
                ['Rohman Hakim', 'Helper', ['Higiene Sanitasi Pangan']],
            ],
            'silo' => [
                ['Wahyu Utomo', 'Pengawas ruang terbatas', ['Petugas K3 Ruang Terbatas', 'Pekerja Ketinggian Tingkat 2']],
                ['Puji Rahayu', 'Teknisi pembersih silo', ['Petugas K3 Ruang Terbatas']],
                ['Fajar Ramadhan', 'Standby person', ['Petugas K3 Ruang Terbatas', 'P3K']],
            ],
            'listrik' => [
                ['Irwan Setiawan', 'Teknisi listrik', ['Teknisi K3 Listrik']],
                ['Dodik Prasetyo', 'Teknisi listrik', ['Teknisi K3 Listrik']],
            ],
            'hama' => [['Suryani', 'Teknisi pest control', ['Sertifikat Pengendalian Hama', 'Higiene Sanitasi Pangan']]],
            'boiler' => [['Gatot Subroto', 'Operator boiler', ['SIO Operator Pesawat Uap Kelas I']]],
        ];
    }

    protected function pqData(): array
    {
        return [
            'mesin' => [RiskLevel::Tinggi, 4, null, 'disetujui'],
            'silo' => [RiskLevel::Tinggi, 4, null, 'disetujui'],
            'listrik' => [RiskLevel::Tinggi, 4, null, 'disetujui'],
            'hama' => [RiskLevel::Menengah, 3, null, 'disetujui'],
            'sipil' => [RiskLevel::Menengah, 2, 'H1', 'ditolak'],
            'boiler' => [RiskLevel::Tinggi, 3, null, 'diajukan'],
            'limbah' => [RiskLevel::Menengah, 3, null, 'klarifikasi'],
        ];
    }

    protected function clarificationText(string $key): string
    {
        return 'Mohon lampirkan izin pengelolaan limbah B3 yang masih berlaku dan prosedur kerja di sumur pompa IPAL sebagai ruang terbatas (butir D2 dan J).';
    }

    protected function criticalNote(string $key): string
    {
        return 'Sebagian besar pekerja harian lepas tidak terdaftar BPJS Ketenagakerjaan; bukti kepesertaan tidak dapat ditunjukkan.';
    }

    protected function planData(): array
    {
        return [
            'oven' => [
                'title' => 'Overhaul tunnel oven dan penggantian burner lini 2', 'area' => 'PRD', 'mode' => ContractMode::Mode2,
                'days' => 14, 'workers' => 6, 'value' => 1_450_000_000, 'flow' => 'contract', 'contractor' => 'mesin',
                'number' => 'GOOD/BU-GRS/2026/0231', 'start_offset' => -3, 'hse' => [4, 4, 3, 4, 4, 4], 'technical' => 85,
                'lines' => [
                    ['Pengelasan rangka oven di area produksi pangan', 'kerja_panas', 3, 4, 'Izin kerja panas, sekat area, fire watch, bersih remah sebelum kerja'],
                    ['Isolasi jalur gas burner dan panel oven', 'energi_tersimpan', 3, 4, 'LOTO jalur gas dan listrik, uji kebocoran gas'],
                    ['Serpihan logam dan kaca mencemari lini', 'kontaminasi_pangan', 3, 3, 'Daftar alat masuk-keluar, magnet trap, pembersihan dan release QA'],
                ],
                'hse_summary' => 'HSE Plan overhaul oven: JSA kerja panas dan LOTO gas, pengendalian benda asing, release lini oleh QA sebelum produksi.',
                'kickoff' => 'Kick-off di BU Gresik: dibahas jadwal shutdown lini 2, LOTO gas burner, dan aturan GMP serta halal untuk teknisi.',
                'bridging' => "Owner (Garudafood): shutdown dan isolasi lini, izin kerja, release QA.\nKontraktor: JSA, pengawas K3, pengendalian alat dan benda asing.",
                'induction' => 'Induksi K3, GMP, dan halal · Overhaul oven lini 2', 'fail_index' => 3,
            ],
            'silo' => [
                'title' => 'Pembersihan internal silo tepung S-02', 'area' => 'SIL', 'mode' => ContractMode::Mode2,
                'days' => 5, 'workers' => 3, 'value' => 185_000_000, 'flow' => 'contract', 'contractor' => 'silo',
                'number' => 'GOOD/BU-GRS/2026/0244', 'start_offset' => -1, 'hse' => [4, 4, 4, 4, 4, 3], 'technical' => 80,
                'lines' => [
                    ['Masuk ke dalam silo tepung', 'ruang_terbatas', 3, 5, 'Izin ruang terbatas, isolasi feeder, uji gas, standby person, tripod rescue'],
                    ['Debu tepung mudah meledak', 'lainnya', 2, 5, 'Alat antiledak, grounding, larangan sumber api'],
                ],
                'kickoff' => 'Kick-off di area silo: dibahas isolasi rotary valve dan screw conveyor, uji gas, dan rencana penyelamatan.',
                'bridging' => "Owner: pengosongan dan isolasi silo, uji gas awal, izin kerja.\nKontraktor: peralatan antiledak, tim ruang terbatas, rescue.",
                'induction' => 'Induksi K3 ruang terbatas dan GMP · Silo tepung',
            ],
            'panel' => [
                'title' => 'Penggantian panel distribusi listrik area pengemasan', 'area' => 'PKG', 'mode' => ContractMode::Mode2,
                'days' => 10, 'workers' => 4, 'value' => 720_000_000, 'flow' => 'tender', 'invite' => ['listrik'],
                'lines' => [
                    ['Pekerjaan pada panel tegangan rendah bertegangan', 'listrik', 3, 5, 'LOTO, teknisi K3 listrik, APD arc flash'],
                    ['Debu dan serpihan di area pengemasan', 'kontaminasi_pangan', 2, 3, 'Sekat plastik, pembersihan, release QA'],
                ],
            ],
            'hama' => [
                'title' => 'Jasa pengendalian hama terpadu 2027', 'area' => 'WH', 'mode' => ContractMode::Mode3,
                'days' => 365, 'workers' => 3, 'value' => 240_000_000, 'flow' => 'assessed',
                'lines' => [['Penggunaan pestisida di sekitar area pangan', 'bahan_kimia', 2, 3, 'Pestisida terdaftar, peta umpan, larangan di area produksi terbuka']],
            ],
        ];
    }

    protected function permitData(): array
    {
        return [
            ['oven', PermitType::KerjaPanas, 'Pengelasan rangka tunnel oven lini 2', ['Heru Susanto', 'Agus Salim'], now()->startOfHour(), 8, null, null, 'aktif'],
            ['oven', PermitType::Loto, 'Isolasi jalur gas burner dan panel listrik oven lini 2', ['Agus Salim', 'Nanang Kosim'], now()->subDays(2)->setTime(7, 0), 6, null, 'Katup gas utama burner ditutup dan digembok; panel MCC oven lini 2 di-off, digembok, dan diberi tag.', 'ditutup'],
            ['silo', PermitType::RuangTerbatas, 'Pembersihan dinding dalam silo tepung S-02', ['Wahyu Utomo', 'Puji Rahayu', 'Fajar Ramadhan'], now()->addDay()->setTime(8, 0), 6, ['o2' => 20.8, 'lel' => 0, 'h2s' => 0, 'co' => 0], 'Rotary valve dan screw conveyor silo S-02 di-LOTO; slide gate inlet ditutup dan digembok.', 'diajukan'],
            ['oven', PermitType::Umum, 'Pemasangan kembali cover oven dan pembersihan area', ['Nanang Kosim', 'Heru Susanto'], now()->addDays(2)->setTime(8, 0), 6, null, null, 'draf'],
        ];
    }
}
