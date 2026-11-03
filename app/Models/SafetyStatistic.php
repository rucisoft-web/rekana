<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Statistik keselamatan tahunan rekanan; LTIFR dan TRIR dihitung otomatis. */
class SafetyStatistic extends Model
{
    protected $guarded = ['id'];

    public function contractor(): BelongsTo
    {
        return $this->belongsTo(Contractor::class);
    }

    /** LTIFR = kasus hilang waktu x 1.000.000 / jam kerja. */
    public function ltifr(): ?float
    {
        return $this->manhours > 0 ? round($this->lti_cases * 1_000_000 / $this->manhours, 2) : null;
    }

    /** TRIR = kasus tercatat x 200.000 / jam kerja. */
    public function trir(): ?float
    {
        return $this->manhours > 0 ? round($this->recordable_cases * 200_000 / $this->manhours, 2) : null;
    }
}
