<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/** Seluruh data contoh: MedcoEnergi (hulu migas) dan Pakuwon Jati (properti dan konstruksi), Garudafood (manufaktur pangan). */
class DemoSeeder extends Seeder
{
    public const SCENARIOS = [MedcoDemoSeeder::class, PakuwonDemoSeeder::class, GarudafoodDemoSeeder::class];

    public function run(): void
    {
        $this->call(self::SCENARIOS);
    }
}
