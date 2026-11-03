<?php

namespace App\Enums;

use App\Enums\Concerns\EnumMeta;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

/** Jenis izin kerja dan daftar periksa wajibnya. */
enum PermitType: string implements HasColor, HasLabel
{
    use EnumMeta;

    case KerjaPanas = 'kerja_panas';
    case RuangTerbatas = 'ruang_terbatas';
    case Ketinggian = 'ketinggian';
    case Loto = 'loto';
    case Umum = 'umum';

    public const META = [
        'kerja_panas' => ['Kerja panas', 'danger'],
        'ruang_terbatas' => ['Masuk ruang terbatas', 'danger'],
        'ketinggian' => ['Kerja di ketinggian', 'warning'],
        'loto' => ['Isolasi energi (LOTO)', 'warning'],
        'umum' => ['Kerja umum / dingin', 'gray'],
    ];

    /** @return array<string, string> kunci => butir periksa yang wajib dicentang sebelum izin diajukan. */
    public function checklist(): array
    {
        $common = [
            'jsa' => 'JSA / analisis bahaya pekerjaan sudah dibahas dengan semua pekerja',
            'apd' => 'APD sesuai bahaya tersedia dan dipakai',
            'area' => 'Area kerja dibatasi dan diberi rambu',
        ];

        return $common + match ($this) {
            self::KerjaPanas => [
                'material_mudah_terbakar' => 'Bahan mudah terbakar dalam radius 11 m dipindahkan atau ditutup',
                'apar' => 'APAR siap di lokasi',
                'fire_watch' => 'Petugas pengawas api (fire watch) ditunjuk dan tetap 30 menit setelah selesai',
                'uji_gas' => 'Uji gas mudah terbakar dilakukan bila di area bahaya',
            ],
            self::RuangTerbatas => [
                'isolasi' => 'Semua jalur masuk (pipa, listrik, mekanik) diisolasi dan dikunci',
                'ventilasi' => 'Ventilasi paksa berjalan',
                'uji_gas' => 'Uji gas O2, LEL, H2S, CO dilakukan dan dicatat',
                'standby' => 'Petugas siaga (standby person) di luar pintu masuk',
                'rescue' => 'Rencana dan peralatan penyelamatan siap',
            ],
            self::Ketinggian => [
                'harness' => 'Full body harness dan lanyard diperiksa',
                'angkur' => 'Titik angkur memadai',
                'perancah' => 'Perancah / tangga diperiksa dan bertanda layak',
                'cuaca' => 'Kondisi cuaca aman (tidak hujan, angin kencang)',
            ],
            self::Loto => [
                'identifikasi' => 'Semua sumber energi diidentifikasi',
                'gembok' => 'Gembok dan label personal terpasang',
                'verifikasi_nol' => 'Verifikasi energi nol (try-out) dilakukan',
            ],
            self::Umum => [],
        };
    }

    public function needsGasTest(): bool
    {
        return $this === self::RuangTerbatas;
    }

    public function needsIsolationPoints(): bool
    {
        return in_array($this, [self::Loto, self::RuangTerbatas], true);
    }
}
