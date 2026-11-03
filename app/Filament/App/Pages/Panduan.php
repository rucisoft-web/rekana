<?php

namespace App\Filament\App\Pages;

use App\Filament\Shared\Panduan as BasePanduan;

class Panduan extends BasePanduan
{
    protected static function panel(): string
    {
        return 'app';
    }

    protected function intro(): string
    {
        return 'Panduan owner mengikuti urutan kerja CSMS IOGP 423: siapkan area dan template, nilai risiko rencana pekerjaan, prakualifikasi rekanan, seleksi dengan HSE Plan, mobilisasi (kick-off, induksi, kartu akses), lalu kendalikan pekerjaan harian dengan izin kerja dan pantau semuanya di dasbor.';
    }
}
