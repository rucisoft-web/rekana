<?php

namespace App\Enums;

use App\Enums\Concerns\EnumMeta;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

/** Peran pengguna di sisi owner (bagian 3 rancangan). */
enum Role: string implements HasColor, HasLabel
{
    use EnumMeta;

    case Admin = 'admin';
    case Pimpinan = 'pimpinan';
    case PemilikPekerjaan = 'pemilik_pekerjaan';
    case Pengadaan = 'pengadaan';
    case Evaluator = 'evaluator';
    case Pengawas = 'pengawas';
    case Pengamat = 'pengamat';

    public const META = [
        'admin' => ['Administrator Sistem', 'danger'],
        'pimpinan' => ['Pimpinan', 'primary'],
        'pemilik_pekerjaan' => ['User / Pemilik Pekerjaan', 'info'],
        'pengadaan' => ['Pengadaan', 'info'],
        'evaluator' => ['Tim Evaluator HSE', 'warning'],
        'pengawas' => ['Pengawas Lapangan (HSE Officer)', 'warning'],
        'pengamat' => ['Pengamat (baca saja)', 'gray'],
    ];

    /** Pengguna, area, template, dan penghapusan. */
    public function canManage(): bool
    {
        return $this === self::Admin;
    }

    /** Mencatat data kerja (rencana pekerjaan, induksi, izin kerja). */
    public function canWrite(): bool
    {
        return ! in_array($this, [self::Pengamat, self::Pimpinan], true);
    }

    /** Menilai prakualifikasi dan HSE Plan. */
    public function canEvaluate(): bool
    {
        return in_array($this, [self::Admin, self::Evaluator], true);
    }

    /** Menyetujui hasil PQ, daftar hitam, dan izin mulai kerja. */
    public function canApprove(): bool
    {
        return in_array($this, [self::Admin, self::Pimpinan], true);
    }

    /** Membuka seleksi, mengundang rekanan, menetapkan pemenang. */
    public function canProcure(): bool
    {
        return in_array($this, [self::Admin, self::Pengadaan], true);
    }

    /** Menyetujui dan menutup izin kerja di lapangan. */
    public function canAuthorizePermit(): bool
    {
        return in_array($this, [self::Admin, self::Pengawas, self::Evaluator], true);
    }

    public function description(): string
    {
        return match ($this) {
            self::Admin => 'Pengguna, area, template kuesioner, ambang skor, dan seluruh fitur.',
            self::Pimpinan => 'Melihat semua; menyetujui hasil prakualifikasi, daftar hitam, dan izin mulai kerja.',
            self::PemilikPekerjaan => 'Membuat rencana pekerjaan, mengisi penilaian risiko, mengajukan izin kerja.',
            self::Pengadaan => 'Membuka seleksi, mengundang rekanan bersertifikat, menetapkan pemenang.',
            self::Evaluator => 'Menilai kuesioner prakualifikasi, kunjungan lokasi, dan HSE Plan.',
            self::Pengawas => 'Induksi, kartu akses, persetujuan dan penutupan izin kerja di lapangan.',
            self::Pengamat => 'Baca saja.',
        };
    }
}
