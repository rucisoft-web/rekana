<?php

namespace App\Filament\Kontraktor\Pages;

use App\Filament\Shared\Panduan as BasePanduan;

class Panduan extends BasePanduan
{
    protected static function panel(): string
    {
        return 'kontraktor';
    }

    protected function intro(): string
    {
        return 'Panduan portal kontraktor: lengkapi dokumen perusahaan, statistik K3, dan data pekerja sekali saja; jawab prakualifikasi dari setiap owner; unggah HSE Plan saat diundang seleksi; lalu pantau kontrak, kesiapan pekerja, dan izin kerja di dasbor.';
    }
}
