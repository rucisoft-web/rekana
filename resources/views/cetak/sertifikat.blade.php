@extends('cetak.layout')
@section('title', 'Sertifikat CSMS '.$c->number)
@section('css')
    .cert { background: #fff; border: 10px solid #1c1917; padding: 2.4rem 2.6rem; position: relative; }
    .cert h1 { font-size: 2rem; margin: .4rem 0; letter-spacing: -.02em; } .cert h2 { font-size: 1.6rem; margin: .6rem 0 .2rem; }
    .eyebrow { color: #d45a06; font-weight: 800; letter-spacing: .12em; text-transform: uppercase; font-size: .8rem; }
    .grid { display: grid; grid-template-columns: 1fr 1fr; gap: .8rem 2rem; margin: 1.4rem 0; font-size: .95rem; } .grid b { display: block; font-size: .72rem; text-transform: uppercase; color: #78716c; letter-spacing: .05em; }
    .risk { display: inline-block; background: #fbbf24; padding: .3rem .9rem; border-radius: 6px; font-weight: 900; font-size: 1.1rem; }
    .foot { display: flex; justify-content: space-between; align-items: flex-end; margin-top: 1.6rem; } .foot img { width: 120px; } .sign { font-size: .85rem; }
@endsection
@section('body')
<div class="cert">
    @if ($c->owner->is_demo)<div class="demo">DATA SIMULASI · CONTOH UNTUK DEMO SOFTWARE REKANA · BUKAN SERTIFIKAT RESMI</div>@endif
    <div class="stripe"></div>
    <p class="eyebrow" style="margin-top:1.2rem">{{ $c->owner->name }}</p>
    <h1>Sertifikat CSMS Rekanan</h1>
    <p>Diberikan kepada</p>
    <h2>{{ $c->contractor->name }}</h2>
    <p>yang telah lulus prakualifikasi keselamatan kontraktor (Contractor Safety Management System) dan dinyatakan layak mengerjakan pekerjaan sampai kategori risiko</p>
    <p><span class="risk">{{ strtoupper($c->max_risk->getLabel()) }}</span></p>
    <div class="grid">
        <div><b>Nomor sertifikat</b>{{ $c->number }}</div>
        <div><b>Skor prakualifikasi</b>{{ $c->score }}%</div>
        <div><b>Tanggal terbit</b>{{ $c->issued_at->translatedFormat('d F Y') }}</div>
        <div><b>Berlaku sampai</b>{{ $c->valid_until->translatedFormat('d F Y') }}</div>
        <div><b>Nomor PQ</b>{{ $c->prequalification?->number ?? '-' }}</div>
        <div><b>Status</b>{{ $c->revoked_at ? 'DICABUT: '.$c->revoke_reason : ($c->isValid() ? 'Berlaku' : 'Kedaluwarsa') }}</div>
    </div>
    <div class="foot">
        <div class="sign">Keabsahan sertifikat dapat diperiksa dengan memindai kode QR.<br>Diterbitkan melalui Rekana · IOGP 423</div>
        <img src="{{ $qr }}" alt="QR verifikasi">
    </div>
</div>
@endsection
