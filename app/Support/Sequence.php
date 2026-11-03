<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

/** Nomor berurutan per owner per tahun, misalnya PQ/2026/001. */
class Sequence
{
    public const PREFIXES = [
        'work_plan' => 'RP',
        'pq' => 'PQ',
        'certificate' => 'CSMS',
        'tender' => 'SLK',
        'permit' => 'IK',
    ];

    public static function next(string $code, int $ownerId, ?int $year = null): string
    {
        $year ??= (int) now()->format('Y');

        $number = DB::transaction(function () use ($code, $ownerId, $year) {
            DB::table('sequences')->insertOrIgnore(['owner_id' => $ownerId, 'code' => $code, 'year' => $year, 'last_number' => 0]);
            $row = DB::table('sequences')->where(['owner_id' => $ownerId, 'code' => $code, 'year' => $year])->lockForUpdate()->first();
            DB::table('sequences')->where('id', $row->id)->update(['last_number' => $row->last_number + 1]);

            return $row->last_number + 1;
        });

        return sprintf('%s/%d/%03d', self::PREFIXES[$code] ?? strtoupper($code), $year, $number);
    }
}
