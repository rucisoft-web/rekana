<?php

namespace Database\Seeders;

use App\Enums\AreaClass;
use App\Enums\ContractMode;
use App\Enums\DocumentType;
use App\Enums\PermitType;
use App\Enums\RiskLevel;

/**
 * Data contoh manufaktur pangan: PT Garudafood Putra Putri Jaya Tbk, Business Unit Gresik (pabrik biskuit).
 *
 * Fakta profil dari Laporan Tahunan dan Keberlanjutan 2025 Garudafood: kantor pusat Wisma Garudafood (Jakarta Selatan),
 * alamat Business Unit Gresik di Driyorejo, galeri kunjungan pabrik digital Gery X-Quest di Gresik, seluruh unit
 * tersertifikasi SMK3, P2K3 di tiap unit, ERT, PLTS atap, transisi boiler gas ke boiler biomassa dengan absorption
 * chiller, IPAL, verifikasi alat oleh PJK3, serta penerapan contractor safety management system di seluruh area.
 *
 * Jenis pekerjaan rekanan disusun mengikuti kegiatan yang lazim di pabrik biskuit tersebut. Nama rekanan, staf,
 * pekerja, skor, dan aktivitas adalah simulasi untuk demo, bukan data Garudafood.
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
            'industry' => 'Manufaktur makanan (biskuit), bersertifikat SMK3, FSSC 22000, dan SJPH',
            'city' => 'Gresik',
            'address' => 'Jl. Krikilan KM 28, Krikilan, Driyorejo, Kabupaten Gresik, Jawa Timur 61177. Kantor pusat: Wisma Garudafood, Jl. Bintaro Raya No. 10A, Jakarta Selatan 12240.',
            'hse_email' => 'hse@garudafood.rekana.test',
            'settings' => ['demo_note' => 'Profil Garudafood dan Business Unit Gresik (alamat, SMK3, PLTS atap, boiler biomassa, IPAL, Gery X-Quest) diambil dari Laporan Tahunan dan Keberlanjutan 2025. Jenis pekerjaan disusun mengikuti kegiatan lazim pabrik biskuit, sedangkan nama rekanan, staf, pekerja, skor, dan aktivitas adalah simulasi, bukan data Garudafood.'],
        ];
    }

    protected function inductionTopics(): array
    {
        return ['k3_umum', 'bahaya_area', 'tanggap_darurat', 'apd', 'izin_kerja', 'keamanan_pangan', 'kaca_plastik', 'halal', 'lingkungan'];
    }

    protected function areaData(): array
    {
        return [
            ['PRD', 'Lini produksi biskuit (mixing, forming, tunnel oven, cooling)', AreaClass::ProduksiPangan, 'Area GMP: cuci tangan, penutup rambut, larangan kaca, perhiasan, dan makanan pribadi. Alat kerja wajib dicatat masuk-keluar.'],
            ['PKG', 'Area pengemasan dan sortir', AreaClass::ProduksiPangan, 'Mesin pengemas otomatis, metal detector, dan checkweigher; pengendalian benda asing ketat.'],
            ['SIL', 'Silo tepung, gula, dan sistem pneumatic conveying', AreaClass::Bahaya, 'Ruang terbatas dan bahaya ledakan debu tepung/gula.'],
            ['UTL', 'Utilitas: boiler biomassa, absorption chiller, kompresor', AreaClass::Bahaya, 'Pesawat uap, bejana tekan, gudang bahan bakar biomassa, panel listrik utama.'],
            ['ATP', 'Atap pabrik dan PLTS atap', AreaClass::Terbatas, 'Atap metal dan instalasi panel surya; bahaya jatuh dan listrik DC.'],
            ['WH', 'Gudang bahan baku dan barang jadi', AreaClass::Umum, 'Forklift, rak tinggi, dan dok bongkar muat.'],
            ['IPL', 'Instalasi pengolahan air limbah (IPAL) dan TPS limbah B3', AreaClass::Terbatas, 'Bak aerasi, sumur pompa, dan tempat penyimpanan sementara limbah B3.'],
            ['XQ', 'Gery X-Quest, kantor, dan kantin', AreaClass::Umum, 'Galeri kunjungan pabrik digital: ada pengunjung umum dan pelajar.'],
        ];
    }

    protected function staffData(): array
    {
        return [
            'admin' => ['Novi Anggraini', 'Sekretaris P2K3 (Ahli K3 Umum)'],
            'pimpinan' => ['Hadi Prasetyo', 'Plant Manager BU Gresik'],
            'pemilik' => ['Rudy Hermawan', 'Engineering Manager'],
            'pengadaan' => ['Yuliana Putri', 'Purchasing Supervisor'],
            'evaluator' => ['Taufik Hidayat', 'SHE Superintendent'],
            'pengawas' => ['Maya Sari', 'SHE Officer'],
            'auditor' => ['Eko Wahyudi', 'QA dan Food Safety Auditor'],
        ];
    }

    protected function contractorData(): array
    {
        return [
            'mesin' => ['PT Gresik Teknik Mesin Pangan', 'Perawatan mesin produksi, tunnel oven, dan burner', 'Gresik', 'Ferry Kusuma', [2023 => [96000, 0, 0, 1], 2024 => [104000, 0, 0, 1], 2025 => [112000, 0, 0, 0]]],
            'silo' => ['PT Silo Bersih Indonesia', 'Pembersihan silo, tangki, dan ducting industri pangan', 'Sidoarjo', 'Dwi Santoso', [2023 => [38000, 0, 0, 1], 2024 => [41000, 0, 0, 0], 2025 => [45000, 0, 0, 0]]],
            'plts' => ['PT Surya Atap Nusantara', 'EPC dan pemeliharaan PLTS atap', 'Surabaya', 'Gilang Ramadhan', [2023 => [52000, 0, 0, 1], 2024 => [68000, 0, 0, 1], 2025 => [81000, 0, 0, 1]]],
            'sanitasi' => ['PT Higienis Prima Sanitasi', 'Sanitasi industri dan deep cleaning lini produksi', 'Sidoarjo', 'Lestari Wulandari', [2023 => [148000, 0, 1, 3], 2024 => [156000, 0, 0, 2], 2025 => [163000, 0, 0, 2]]],
            'forklift' => ['PT Jatim Forklift Rental', 'Sewa forklift berikut operator', 'Gresik', 'Hendra Kusnadi', [2023 => [88000, 0, 1, 2], 2024 => [92000, 0, 1, 3], 2025 => [97000, 0, 0, 2]]],
            'listrik' => ['PT Driyorejo Elektrik', 'Instalasi dan pemeliharaan listrik industri', 'Gresik', 'Lukman Hakim', [2023 => [52000, 0, 0, 1], 2024 => [55000, 0, 0, 1], 2025 => [58000, 0, 0, 0]]],
            'chiller' => ['PT Dingin Teknik Utama', 'Perawatan absorption chiller, HVAC, dan cold room', 'Surabaya', 'Wibowo Santoso', [2023 => [44000, 0, 0, 1], 2024 => [47000, 0, 0, 0], 2025 => [51000, 0, 0, 1]]],
            'proteksi' => ['PT Proteksi Kebakaran Mandiri', 'Perawatan hydrant, sprinkler, dan fire alarm', 'Surabaya', 'Rahmat Fauzi', [2024 => [31000, 0, 0, 0], 2025 => [33000, 0, 0, 1]]],
            'hama' => ['PT Jatim Pest Control', 'Pengendalian hama terpadu area pangan', 'Surabaya', 'Rina Marlina', [2024 => [21000, 0, 0, 0], 2025 => [23000, 0, 0, 0]]],
            'kantin' => ['CV Boga Sehat Driyorejo', 'Katering dan pengelolaan kantin karyawan', 'Gresik', 'Siti Khotijah', [2024 => [62000, 0, 0, 1], 2025 => [65000, 0, 0, 0]]],
            'boiler' => ['PT Ketel Uap Jaya', 'Perawatan boiler biomassa dan bejana tekan', 'Surabaya', 'Antonius Halim', [2023 => [33000, 0, 1, 3], 2024 => [35000, 0, 1, 2], 2025 => [37000, 0, 0, 2]]],
            'limbah' => ['PT Mitra Olah Limbah', 'Operasi dan perawatan IPAL', 'Mojokerto', 'Retno Wulandari', [2024 => [27000, 0, 0, 1], 2025 => [29000, 0, 0, 1]]],
            'sipil' => ['PT Krikilan Karya Sipil', 'Pekerjaan sipil dan renovasi bangunan', 'Gresik', 'Bambang Susilo', [2023 => [61000, 0, 2, 5], 2024 => [64000, 0, 1, 4], 2025 => [66000, 0, 1, 4]]],
            'perancah' => ['CV Perancah Jaya Gresik', 'Sewa dan pemasangan perancah', 'Gresik', 'Mulyono', [2024 => [24000, 0, 1, 2], 2025 => [26000, 0, 1, 3]]],
        ];
    }

    protected function extraDocuments(): array
    {
        return [
            'mesin' => [[DocumentType::Smk3, 'Sertifikat SMK3 tingkat lanjutan', 'SMK3/2024/317', today()->addYears(2)]],
            'silo' => [[DocumentType::Iso45001, 'Sertifikat ISO 45001:2018', 'ID-OHS-6012', today()->addDays(25)]],
            'plts' => [[DocumentType::Sbu, 'SBU instalasi pembangkit listrik tenaga surya', 'SBU-EP/2024/0571', today()->addYear()], [DocumentType::Lainnya, 'Izin Usaha Jasa Penunjang Tenaga Listrik (IUJPTL)', 'IUJPTL/35/2023/118', today()->addMonths(8)]],
            'sanitasi' => [[DocumentType::Lainnya, 'Daftar bahan kimia sanitasi dan SDS (food grade)', 'SDS/HPS/2026', null], [DocumentType::Lainnya, 'Sertifikat halal bahan pembersih kontak pangan', 'ID35110000123450526', today()->addDays(12)]],
            'forklift' => [[DocumentType::Lainnya, 'Sertifikat riksa uji forklift (12 unit) oleh PJK3', 'RU/FL/2026/044', today()->addDays(40)]],
            'hama' => [[DocumentType::Lainnya, 'Izin operasional pengendalian hama (Dinas Kesehatan)', 'PH/SBY/2022/019', today()->subDays(5)]],
            'kantin' => [[DocumentType::Lainnya, 'Sertifikat Laik Higiene Sanitasi Jasa Boga', 'SLHS/GRS/2025/077', today()->addMonths(5)]],
        ];
    }

    protected function workerData(): array
    {
        return [
            'mesin' => [
                ['Agus Salim', 'Supervisor mekanik', ['Ahli K3 Umum', 'Higiene Sanitasi Pangan']],
                ['Heru Susanto', 'Teknisi oven dan burner', ['Juru Las SMAW 3G', 'Higiene Sanitasi Pangan']],
                ['Nanang Kosim', 'Teknisi mekanik', ['Higiene Sanitasi Pangan']],
                ['Sugianto', 'Juru las', ['Juru Las SMAW 3G', 'Higiene Sanitasi Pangan']],
                ['Mochamad Ilham', 'Teknisi kontrol burner', ['Teknisi K3 Listrik', 'Higiene Sanitasi Pangan']],
                ['Rohman Hakim', 'Helper', ['Higiene Sanitasi Pangan']],
            ],
            'silo' => [
                ['Wahyu Utomo', 'Pengawas ruang terbatas', ['Petugas K3 Ruang Terbatas', 'Pekerja Ketinggian Tingkat 2']],
                ['Puji Rahayu', 'Teknisi pembersih silo', ['Petugas K3 Ruang Terbatas']],
                ['Fajar Ramadhan', 'Standby person', ['Petugas K3 Ruang Terbatas', 'P3K']],
                ['Saiful Anwar', 'Teknisi pembersih silo', ['Petugas K3 Ruang Terbatas']],
            ],
            'plts' => [
                ['Aditya Nugraha', 'Site manager PLTS', ['Ahli K3 Listrik', 'Pekerja Ketinggian Tingkat 2']],
                ['Bagas Saputra', 'Teknisi PV', ['Teknisi K3 Listrik', 'Pekerja Ketinggian Tingkat 1']],
                ['Rizal Fahmi', 'Teknisi PV', ['Pekerja Ketinggian Tingkat 1']],
                ['Ari Wibisono', 'Teknisi inverter', ['Teknisi K3 Listrik']],
                ['Dani Kurniawan', 'Rigger', ['Juru Ikat (Rigger) Tingkat II', 'Pekerja Ketinggian Tingkat 1']],
            ],
            'sanitasi' => [
                ['Endang Susilowati', 'Supervisor sanitasi', ['Higiene Sanitasi Pangan', 'Penanganan Bahan Kimia']],
                ['Suwarni', 'Operator sanitasi', ['Higiene Sanitasi Pangan']],
                ['Mujiono', 'Operator sanitasi', ['Higiene Sanitasi Pangan']],
                ['Lilik Indrawati', 'Operator sanitasi', ['Higiene Sanitasi Pangan']],
                ['Yusron Hadi', 'Operator foam cleaning', ['Higiene Sanitasi Pangan', 'Penanganan Bahan Kimia']],
            ],
            'forklift' => [
                ['Kasmudi', 'Operator forklift', ['SIO Operator Forklift Kelas II']],
                ['Ahmad Zainuri', 'Operator forklift', ['SIO Operator Forklift Kelas II']],
                ['Slamet Widodo', 'Operator forklift', ['SIO Operator Forklift Kelas II']],
                ['Imron Rosyadi', 'Mekanik forklift', ['Mekanik Alat Angkat']],
            ],
            'listrik' => [
                ['Irwan Setiawan', 'Teknisi listrik', ['Teknisi K3 Listrik']],
                ['Dodik Prasetyo', 'Teknisi listrik', ['Teknisi K3 Listrik']],
            ],
            'chiller' => [
                ['Bayu Aji', 'Teknisi chiller', ['Teknisi Refrigerasi', 'Petugas K3 Ruang Terbatas']],
                ['Hendro Wicaksono', 'Teknisi HVAC', ['Teknisi Refrigerasi']],
            ],
            'proteksi' => [
                ['Rudi Setiawan', 'Teknisi hydrant', ['Petugas Peran Kebakaran Kelas C']],
                ['Gunawan Saleh', 'Teknisi fire alarm', ['Teknisi K3 Listrik']],
                ['Febri Ardiansyah', 'Helper', ['Petugas Peran Kebakaran Kelas D']],
            ],
            'hama' => [
                ['Suryani', 'Teknisi pest control', ['Sertifikat Pengendalian Hama', 'Higiene Sanitasi Pangan']],
                ['Andik Purwanto', 'Teknisi pest control', ['Sertifikat Pengendalian Hama']],
            ],
            'kantin' => [
                ['Siti Muawanah', 'Juru masak', ['Penjamah Makanan']],
                ['Ninik Rahayu', 'Juru masak', ['Penjamah Makanan']],
                ['Abdul Rohim', 'Pramusaji', ['Penjamah Makanan']],
            ],
            'boiler' => [['Gatot Subroto', 'Operator boiler', ['SIO Operator Pesawat Uap Kelas I']]],
        ];
    }

    protected function pqData(): array
    {
        return [
            'mesin' => [RiskLevel::Tinggi, 4, null, 'disetujui'],
            'silo' => [RiskLevel::Tinggi, 4, null, 'disetujui', 4],
            'plts' => [RiskLevel::Tinggi, 4, null, 'disetujui', 6],
            'sanitasi' => [RiskLevel::Menengah, 4, null, 'disetujui', 3],
            'forklift' => [RiskLevel::Menengah, 3, null, 'disetujui'],
            'listrik' => [RiskLevel::Tinggi, 4, null, 'disetujui', 5],
            'chiller' => [RiskLevel::Tinggi, 4, null, 'disetujui', 3],
            'proteksi' => [RiskLevel::Menengah, 4, null, 'disetujui', 2],
            'kantin' => [RiskLevel::Rendah, 3, null, 'disetujui', 4],
            'hama' => [RiskLevel::Menengah, 3, null, 'disetujui'],
            'boiler' => [RiskLevel::Tinggi, 3, null, 'diajukan'],
            'limbah' => [RiskLevel::Menengah, 3, null, 'klarifikasi'],
            'sipil' => [RiskLevel::Menengah, 2, 'H1', 'ditolak'],
            'perancah' => [RiskLevel::Tinggi, 3, 'D2', 'ditolak'],
        ];
    }

    protected function clarificationText(string $key): string
    {
        return 'Mohon lampirkan izin pengelolaan limbah B3 yang masih berlaku dan prosedur kerja di sumur pompa IPAL sebagai ruang terbatas (butir D2 dan J1).';
    }

    protected function criticalNote(string $key): string
    {
        return $key === 'perancah'
            ? 'Tidak memiliki prosedur kerja ketinggian dan inspeksi perancah; teknisi perancah tidak bersertifikat K3 Perancah.'
            : 'Sebagian besar pekerja harian lepas tidak terdaftar BPJS Ketenagakerjaan; bukti kepesertaan tidak dapat ditunjukkan.';
    }

    protected function planData(): array
    {
        $gmpBridging = "\nAturan pangan: alat dicatat masuk-keluar, larangan kaca dan perhiasan, area disekat, lini dibersihkan dan di-release QA sebelum produksi.";

        return [
            'oven' => [
                'title' => 'Overhaul bertahap tunnel oven dan penggantian burner lini 1 sampai 3', 'area' => 'PRD', 'mode' => ContractMode::Mode2,
                'days' => 75, 'workers' => 6, 'value' => 1_450_000_000, 'flow' => 'contract', 'contractor' => 'mesin',
                'number' => 'GOOD/BU-GRS/2026/0231', 'start_offset' => -45, 'hse' => [4, 4, 3, 4, 4, 4], 'technical' => 85,
                'lines' => [
                    ['Pengelasan rangka oven di area produksi pangan', 'kerja_panas', 3, 4, 'Izin kerja panas, sekat area, fire watch, bersihkan remah dan debu tepung sebelum kerja'],
                    ['Isolasi jalur gas burner dan panel oven', 'energi_tersimpan', 3, 4, 'LOTO jalur gas dan listrik, uji kebocoran gas setelah pemasangan'],
                    ['Bekerja di atas oven dan conveyor', 'ketinggian', 2, 4, 'Platform kerja berpagar, full body harness bila di atas 1,8 m'],
                    ['Serpihan logam dan kaca mencemari lini', 'kontaminasi_pangan', 3, 3, 'Daftar alat masuk-keluar, magnet trap, pembersihan dan release QA'],
                ],
                'hse_summary' => 'HSE Plan overhaul oven: JSA kerja panas dan LOTO gas, pengendalian benda asing, release lini oleh QA sebelum produksi.',
                'kickoff' => 'Kick-off di BU Gresik: dibahas jadwal shutdown per lini, LOTO gas burner, dan aturan GMP serta halal untuk teknisi.',
                'bridging' => "Owner (Garudafood): shutdown dan isolasi lini, izin kerja, release QA.\nKontraktor: JSA, pengawas K3, pengendalian alat dan benda asing.".$gmpBridging,
                'induction' => 'Induksi K3, GMP, dan halal · Overhaul oven', 'fail_index' => 5,
            ],
            'silo' => [
                'title' => 'Pembersihan berkala silo tepung dan gula 2026', 'area' => 'SIL', 'mode' => ContractMode::Mode2,
                'days' => 300, 'workers' => 4, 'value' => 420_000_000, 'flow' => 'contract', 'contractor' => 'silo',
                'number' => 'GOOD/BU-GRS/2026/0244', 'start_offset' => -60, 'hse' => [4, 4, 4, 4, 4, 3], 'technical' => 80,
                'lines' => [
                    ['Masuk ke dalam silo tepung', 'ruang_terbatas', 3, 5, 'Izin ruang terbatas, isolasi rotary valve dan screw conveyor, uji gas, standby person, tripod rescue'],
                    ['Debu tepung dan gula mudah meledak', 'lainnya', 2, 5, 'Alat antiledak, grounding, larangan sumber api'],
                ],
                'kickoff' => 'Kick-off di area silo: dibahas isolasi rotary valve dan screw conveyor, uji gas, dan rencana penyelamatan bersama ERT.',
                'bridging' => "Owner: pengosongan dan isolasi silo, uji gas awal, izin kerja, kesiagaan ERT.\nKontraktor: peralatan antiledak, tim ruang terbatas, rescue.".$gmpBridging,
                'induction' => 'Induksi K3 ruang terbatas dan GMP · Silo',
            ],
            'plts' => [
                'title' => 'Pemasangan PLTS atap tahap 2 di atap gudang barang jadi', 'area' => 'ATP', 'mode' => ContractMode::Mode2,
                'days' => 90, 'workers' => 5, 'value' => 3_250_000_000, 'flow' => 'contract', 'contractor' => 'plts',
                'number' => 'GOOD/BU-GRS/2026/0198', 'start_offset' => -50, 'hse' => [4, 4, 4, 4, 3, 3], 'technical' => 87,
                'lines' => [
                    ['Bekerja di atap metal setinggi 12 m', 'ketinggian', 4, 5, 'Lifeline permanen, full body harness, jalur jalan atap, larangan kerja saat angin kencang dan hujan'],
                    ['Mengangkat panel surya ke atap', 'angkat_berat', 3, 4, 'Lift plan, rigger bersertifikat, zona larangan di bawah'],
                    ['Penyambungan string DC dan inverter', 'listrik', 3, 4, 'LOTO sisi AC dan DC, teknisi K3 listrik, APD isolasi'],
                    ['Atap rapuh di area skylight', 'lainnya', 2, 5, 'Penutup skylight dan jaring pengaman'],
                ],
                'hse_summary' => 'HSE Plan PLTS atap: rencana perlindungan jatuh, lift plan panel, prosedur LOTO DC, rencana penyelamatan dari atap.',
                'kickoff' => 'Kick-off: disepakati jalur akses atap, titik angkur lifeline, jadwal pengangkatan panel di luar jam bongkar muat.',
                'bridging' => "Owner: akses atap, izin kerja ketinggian dan LOTO, koordinasi dengan gudang.\nKontraktor: perlindungan jatuh, lift plan, teknisi listrik bersertifikat.",
                'induction' => 'Induksi K3 kerja ketinggian dan listrik · PLTS atap',
            ],
            'sanitasi' => [
                'title' => 'Deep cleaning dan sanitasi malam lini produksi dan pengemasan', 'area' => 'PKG', 'mode' => ContractMode::Mode2,
                'days' => 365, 'workers' => 12, 'value' => 1_960_000_000, 'flow' => 'contract', 'contractor' => 'sanitasi',
                'number' => 'GOOD/BU-GRS/2026/0102', 'start_offset' => -75, 'hse' => [4, 3, 3, 4, 3, 4], 'technical' => 79,
                'lines' => [
                    ['Membersihkan mesin pengemas dengan bagian bergerak', 'mesin', 2, 4, 'LOTO mesin oleh teknisi owner sebelum dibersihkan'],
                    ['Penggunaan bahan kimia pembersih kontak pangan', 'bahan_kimia', 2, 3, 'SDS, APD kacamata dan sarung tangan, bahan food grade dan halal'],
                    ['Lantai licin saat foam cleaning', 'lainnya', 3, 2, 'Rambu lantai basah, sepatu antiselip'],
                ],
                'kickoff' => 'Kick-off: disepakati jadwal sanitasi pukul 22.00 sampai 04.00, daftar bahan kimia yang boleh dipakai, dan verifikasi ATP swab oleh QA.',
                'bridging' => "Owner: LOTO mesin, verifikasi kebersihan oleh QA.\nKontraktor: bahan kimia terdaftar, pelatihan sanitasi, pengawas per shift.".$gmpBridging,
                'induction' => 'Induksi K3, GMP, dan bahan kimia · Sanitasi malam',
            ],
            'forklift' => [
                'title' => 'Operasional forklift gudang bahan baku dan barang jadi', 'area' => 'WH', 'mode' => ContractMode::Mode2,
                'days' => 365, 'workers' => 8, 'value' => 1_380_000_000, 'flow' => 'contract', 'contractor' => 'forklift',
                'number' => 'GOOD/BU-GRS/2026/0087', 'start_offset' => -80, 'hse' => [3, 3, 4, 3, 3, 3], 'technical' => 76,
                'lines' => [
                    ['Forklift bersinggungan dengan pejalan kaki di gudang', 'kendaraan', 3, 4, 'Jalur pejalan kaki terpisah, lampu biru, batas kecepatan 8 km/jam'],
                    ['Pengisian baterai forklift', 'bahan_kimia', 2, 3, 'Ruang charging berventilasi, eyewash'],
                ],
                'kickoff' => 'Kick-off: dibahas tata letak jalur forklift, pemeriksaan harian unit, dan larangan forklift masuk area produksi.',
                'bridging' => "Owner: tata letak gudang dan jalur pejalan kaki.\nKontraktor: unit laik riksa uji, operator SIO, inspeksi harian.",
                'induction' => 'Induksi K3 gudang dan forklift',
            ],
            'proteksi' => [
                'title' => 'Perawatan berkala hydrant, sprinkler, dan fire alarm 2026', 'area' => 'UTL', 'mode' => ContractMode::Mode2,
                'days' => 365, 'workers' => 3, 'value' => 310_000_000, 'flow' => 'contract', 'contractor' => 'proteksi',
                'number' => 'GOOD/BU-GRS/2026/0091', 'start_offset' => -70, 'hse' => [4, 3, 4, 4, 4, 3], 'technical' => 81,
                'lines' => [
                    ['Uji pompa hydrant dan panel pompa', 'energi_tersimpan', 2, 4, 'LOTO panel pompa, koordinasi dengan ERT karena sistem proteksi berkurang'],
                    ['Penggantian kepala sprinkler', 'mesin', 2, 3, 'Tangga berpagar, area dibarikade'],
                ],
                'kickoff' => 'Kick-off: disepakati prosedur impairment (sistem proteksi tidak aktif) dan patroli api pengganti selama pekerjaan.',
                'bridging' => "Owner: izin impairment, pemberitahuan ke ERT dan asuransi, patroli api pengganti.\nKontraktor: teknisi bersertifikat, pemulihan sistem dalam hari yang sama.",
                'induction' => 'Induksi K3 umum · Proteksi kebakaran',
            ],
            'hama' => [
                'title' => 'Pengendalian hama terpadu 2026', 'area' => 'WH', 'mode' => ContractMode::Mode3,
                'days' => 365, 'workers' => 2, 'value' => 240_000_000, 'flow' => 'contract', 'contractor' => 'hama',
                'number' => 'GOOD/BU-GRS/2026/0063', 'start_offset' => -85, 'hse' => [3, 3, 3, 3, 3, 4], 'technical' => 78,
                'lines' => [['Penggunaan pestisida di sekitar area pangan', 'bahan_kimia', 2, 3, 'Pestisida terdaftar, peta umpan, larangan di area produksi terbuka']],
                'kickoff' => 'Kick-off: dibahas peta stasiun umpan, larangan rodentisida di area produksi, dan laporan tren temuan hama ke QA.',
                'induction' => 'Induksi K3 dan GMP · Pengendalian hama',
            ],
            'kantin' => [
                'title' => 'Katering dan pengelolaan kantin karyawan 2026', 'area' => 'XQ', 'mode' => ContractMode::Mode3,
                'days' => 365, 'workers' => 3, 'value' => 2_700_000_000, 'flow' => 'contract', 'contractor' => 'kantin',
                'number' => 'GOOD/BU-GRS/2026/0055', 'start_offset' => -90, 'hse' => [3, 3, 3, 3, 3, 4], 'technical' => 75,
                'lines' => [['Memasak dengan kompor LPG di dapur kantin', 'lainnya', 1, 4, 'Detektor gas, APAR, regulator dan selang standar']],
                'kickoff' => 'Kick-off: dibahas sertifikat halal bahan, pemeriksaan kesehatan penjamah makanan, dan inspeksi dapur bulanan.',
                'induction' => 'Induksi K3, higiene, dan halal · Kantin',
            ],
            'chiller' => [
                'title' => 'Overhaul absorption chiller dan pembersihan tube condenser', 'area' => 'UTL', 'mode' => ContractMode::Mode2,
                'days' => 30, 'workers' => 4, 'value' => 890_000_000, 'flow' => 'tender', 'invite' => ['chiller', 'mesin'],
                'lines' => [
                    ['Membuka water box dan masuk ke shell condenser', 'ruang_terbatas', 2, 5, 'Izin ruang terbatas, uji gas, standby person'],
                    ['Penanganan larutan lithium bromide', 'bahan_kimia', 2, 4, 'SDS, APD kimia, penampungan tumpahan'],
                    ['Pengelasan tube plate', 'kerja_panas', 2, 4, 'Izin kerja panas, uji LEL di area utilitas'],
                ],
            ],
            'panel' => [
                'title' => 'Penggantian panel distribusi listrik area pengemasan', 'area' => 'PKG', 'mode' => ContractMode::Mode2,
                'days' => 10, 'workers' => 4, 'value' => 720_000_000, 'flow' => 'tender', 'invite' => ['listrik'],
                'lines' => [
                    ['Pekerjaan pada panel tegangan rendah', 'listrik', 3, 5, 'LOTO, teknisi K3 listrik, APD arc flash'],
                    ['Debu dan serpihan di area pengemasan', 'kontaminasi_pangan', 2, 3, 'Sekat plastik, pembersihan, release QA'],
                ],
            ],
            'boiler' => [
                'title' => 'Inspeksi tahunan dan perawatan boiler biomassa (riksa uji PJK3)', 'area' => 'UTL', 'mode' => ContractMode::Mode2,
                'days' => 21, 'workers' => 5, 'value' => 560_000_000, 'flow' => 'assessed',
                'lines' => [
                    ['Masuk ruang bakar dan drum boiler', 'ruang_terbatas', 3, 5, 'Pendinginan, isolasi uap dan bahan bakar, uji gas'],
                    ['Uji hidrostatik pesawat uap', 'uap_bertekanan', 3, 5, 'Prosedur uji tekan, zona aman'],
                ],
            ],
            'ipal' => [
                'title' => 'Operasi dan perawatan IPAL 2027', 'area' => 'IPL', 'mode' => ContractMode::Mode2,
                'days' => 365, 'workers' => 3, 'value' => 380_000_000, 'flow' => 'assessed',
                'lines' => [
                    ['Perawatan pompa di sumur pompa IPAL', 'ruang_terbatas', 2, 5, 'Izin ruang terbatas, uji gas H2S dan O2'],
                    ['Penanganan lumpur dan bahan kimia koagulan', 'bahan_kimia', 2, 3, 'APD, SDS'],
                ],
            ],
        ];
    }

    protected function permitData(): array
    {
        $h = fn (int $daysAgo, int $hour) => now()->subDays($daysAgo)->setTime($hour, 0);

        return [
            ['oven', PermitType::KerjaPanas, 'Pengelasan rangka tunnel oven lini 2', ['Heru Susanto', 'Sugianto'], now()->startOfHour(), 8, null, null, 'aktif'],
            ['plts', PermitType::Ketinggian, 'Pemasangan rail dan panel surya blok C atap gudang', ['Bagas Saputra', 'Rizal Fahmi', 'Aditya Nugraha'], now()->startOfHour(), 8, null, null, 'aktif'],
            ['sanitasi', PermitType::Loto, 'Isolasi mesin pengemas P-04 sampai P-06 untuk deep cleaning', ['Endang Susilowati', 'Yusron Hadi'], now()->setTime(22, 0)->subDay(), 6, null, 'Mesin P-04, P-05, P-06 di-off pada panel MCC-PKG2, digembok dan diberi tag; udara tekan diisolasi.', 'ditutup'],
            ['silo', PermitType::RuangTerbatas, 'Pembersihan dinding dalam silo tepung S-02', ['Wahyu Utomo', 'Puji Rahayu', 'Fajar Ramadhan'], now()->addDay()->setTime(8, 0), 6, ['o2' => 20.8, 'lel' => 0, 'h2s' => 0, 'co' => 0], 'Rotary valve dan screw conveyor silo S-02 di-LOTO; slide gate inlet ditutup dan digembok.', 'diajukan'],
            ['plts', PermitType::Loto, 'Penyambungan string DC ke inverter INV-03', ['Ari Wibisono', 'Aditya Nugraha'], now()->addDay()->setTime(9, 0), 4, null, 'Isolator DC string 1 sampai 8 dibuka dan digembok; breaker AC inverter di panel LVMDP-2 di-off dan diberi tag.', 'disetujui'],
            ['oven', PermitType::Loto, 'Isolasi jalur gas burner dan panel listrik oven lini 3', ['Agus Salim', 'Mochamad Ilham'], now()->addDays(2)->setTime(7, 0), 6, null, 'Katup gas utama burner lini 3 ditutup dan digembok; panel MCC oven lini 3 di-off dan diberi tag.', 'diajukan'],
            ['proteksi', PermitType::Umum, 'Uji aliran pompa hydrant bulanan', ['Rudi Setiawan', 'Febri Ardiansyah'], $h(3, 9), 4, null, null, 'ditutup'],
            ['forklift', PermitType::Umum, 'Penggantian ban forklift FL-07 di area charging', ['Imron Rosyadi'], now()->addDays(2)->setTime(13, 0), 3, null, null, 'draf'],
            ['silo', PermitType::RuangTerbatas, 'Pembersihan silo gula G-01', ['Saiful Anwar', 'Puji Rahayu', 'Fajar Ramadhan'], $h(8, 8), 6, ['o2' => 20.9, 'lel' => 0, 'h2s' => 0, 'co' => 0], 'Rotary valve silo G-01 di-LOTO; slide gate ditutup dan digembok.', 'ditutup'],
        ];
    }
}
