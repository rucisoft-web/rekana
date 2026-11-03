@extends('cetak.layout')
@section('title', 'Kartu akses')
@section('css')
    .cards { display: grid; grid-template-columns: repeat(2, 85.6mm); gap: 6mm; justify-content: center; }
    .card { width: 85.6mm; height: 54mm; background: #fff; border-radius: 3mm; overflow: hidden; position: relative; box-shadow: 0 2px 6px rgba(0,0,0,.2); display: flex; flex-direction: column; font-size: 7.5pt; }
    .card .head { background: #1c1917; color: #fbbf24; padding: 1.6mm 3mm; font-weight: 900; font-size: 8pt; display: flex; justify-content: space-between; }
    .card .head span { color: #e7e5e4; font-weight: 600; }
    .card .body { display: flex; gap: 3mm; padding: 2.5mm 3mm; flex: 1; }
    .card .body img { width: 24mm; height: 24mm; }
    .card h3 { margin: 0; font-size: 10.5pt; } .card p { margin: .4mm 0; } .muted { color: #78716c; }
    .card .stripe { height: 3mm; } .card .demo { font-size: 6pt; padding: .6mm; }
    @media print { .card { box-shadow: none; border: .2mm solid #a8a29e; } }
@endsection
@section('body')
<div class="cards">
@forelse ($cards as $card)
    <div class="card">
        @if ($card->contract->owner->is_demo)<div class="demo">DATA SIMULASI · CONTOH DEMO</div>@endif
        <div class="head">KARTU AKSES REKANAN <span>{{ \Illuminate\Support\Str::limit($card->contract->owner->name, 34) }}</span></div>
        <div class="body">
            <img src="{{ \App\Http\Controllers\PrintController::qr(route('verifikasi.kartu', $card->code)) }}" alt="QR">
            <div>
                <h3>{{ $card->worker->name }}</h3>
                <p>{{ $card->worker->position }}</p>
                <p class="muted">{{ $card->contract->contractor->name }}</p>
                <p><b>Area:</b> {{ $card->contract->workPlan?->area?->name }}</p>
                <p><b>Berlaku s.d.</b> {{ $card->valid_until->translatedFormat('d M Y') }}</p>
                <p class="muted">{{ \Illuminate\Support\Str::limit($card->worker->competencies->pluck('name')->implode(', '), 70) }}</p>
            </div>
        </div>
        <div class="stripe"></div>
    </div>
@empty
    <p>Tidak ada kartu aktif.</p>
@endforelse
</div>
@endsection
