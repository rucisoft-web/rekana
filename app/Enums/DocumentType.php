<?php

namespace App\Enums;

use App\Enums\Concerns\EnumMeta;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

/** Jenis dokumen perusahaan rekanan. */
enum DocumentType: string implements HasColor, HasLabel
{
    use EnumMeta;

    case Nib = 'nib';
    case Npwp = 'npwp';
    case Sbu = 'sbu';
    case Iso45001 = 'iso45001';
    case Smk3 = 'smk3';
    case KebijakanK3 = 'kebijakan_k3';
    case OrganisasiK3 = 'organisasi_k3';
    case Prosedur = 'prosedur';
    case ProgramK3 = 'program_k3';
    case TanggapDarurat = 'tanggap_darurat';
    case Bpjs = 'bpjs';
    case Asuransi = 'asuransi';
    case Statistik = 'statistik';
    case Lainnya = 'lainnya';

    public const META = [
        'nib' => ['NIB / izin usaha', 'gray'],
        'npwp' => ['NPWP', 'gray'],
        'sbu' => ['SBU / sertifikat badan usaha', 'gray'],
        'iso45001' => ['Sertifikat ISO 45001', 'success'],
        'smk3' => ['Sertifikat SMK3', 'success'],
        'kebijakan_k3' => ['Kebijakan K3', 'info'],
        'organisasi_k3' => ['Struktur organisasi K3', 'info'],
        'prosedur' => ['Prosedur kerja aman', 'info'],
        'program_k3' => ['Program K3 tahunan', 'info'],
        'tanggap_darurat' => ['Rencana tanggap darurat', 'info'],
        'bpjs' => ['BPJS Ketenagakerjaan', 'gray'],
        'asuransi' => ['Asuransi', 'gray'],
        'statistik' => ['Laporan statistik K3', 'info'],
        'lainnya' => ['Lainnya', 'gray'],
    ];
}
