<?php

namespace App\Enums;

use App\Enums\Concerns\EnumMeta;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

/** Daftar bahaya baku untuk penilaian risiko pekerjaan (pabrik dan fasilitas migas). */
enum Hazard: string implements HasColor, HasLabel
{
    use EnumMeta;

    case Ketinggian = 'ketinggian';
    case RuangTerbatas = 'ruang_terbatas';
    case KerjaPanas = 'kerja_panas';
    case Listrik = 'listrik';
    case EnergiTersimpan = 'energi_tersimpan';
    case AngkatBerat = 'angkat_berat';
    case BahanKimia = 'bahan_kimia';
    case Amonia = 'amonia';
    case UapBertekanan = 'uap_bertekanan';
    case Penggalian = 'penggalian';
    case KendaraanAlatBerat = 'kendaraan';
    case MesinBergerak = 'mesin';
    case Kebisingan = 'kebisingan';
    case KontaminasiPangan = 'kontaminasi_pangan';
    case H2s = 'h2s';
    case Hidrokarbon = 'hidrokarbon';
    case KerjaSumur = 'kerja_sumur';
    case Perairan = 'perairan';
    case Lainnya = 'lainnya';

    public const META = [
        'ketinggian' => ['Kerja di ketinggian', 'danger'],
        'ruang_terbatas' => ['Ruang terbatas (tangki, fermentor, IPAL)', 'danger'],
        'kerja_panas' => ['Kerja panas (las, gerinda, potong)', 'danger'],
        'listrik' => ['Listrik', 'warning'],
        'energi_tersimpan' => ['Energi tersimpan (perlu LOTO)', 'warning'],
        'angkat_berat' => ['Pengangkatan dengan crane / alat angkat', 'warning'],
        'bahan_kimia' => ['Bahan kimia (asam, basa, pelarut)', 'warning'],
        'amonia' => ['Amonia (sistem pendingin, bahan proses)', 'danger'],
        'uap_bertekanan' => ['Boiler dan uap bertekanan', 'warning'],
        'penggalian' => ['Penggalian', 'warning'],
        'kendaraan' => ['Kendaraan dan alat berat', 'warning'],
        'mesin' => ['Mesin bergerak / berputar', 'warning'],
        'kebisingan' => ['Kebisingan dan getaran', 'gray'],
        'kontaminasi_pangan' => ['Kontaminasi produk pangan (benda asing, non-halal)', 'info'],
        'h2s' => ['Gas H2S (sour gas)', 'danger'],
        'hidrokarbon' => ['Hidrokarbon mudah terbakar / SIMOPS di fasilitas hidup', 'danger'],
        'kerja_sumur' => ['Intervensi sumur / tekanan tinggi (well control)', 'danger'],
        'perairan' => ['Kerja di atas air / lepas pantai', 'warning'],
        'lainnya' => ['Lainnya', 'gray'],
    ];

    /** Bahaya yang langsung menaikkan pekerjaan ke kategori Tinggi, berapa pun skor matriksnya. */
    public function forcesHigh(): bool
    {
        return in_array($this->value, (array) config('rekana.risk.force_high'), true);
    }
}
