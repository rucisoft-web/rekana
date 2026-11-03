@extends('cetak.layout')
@section('title', 'Verifikasi '.$title)
@section('css')
    .box { background: #fff; border-radius: 12px; overflow: hidden; max-width: 560px; margin: 0 auto; }
    .status { padding: 1.2rem 1.4rem; color: #fff; font-weight: 900; font-size: 1.3rem; } .ok { background: #16a34a; } .no { background: #e11d48; }
    table { width: 100%; border-collapse: collapse; } td { padding: .6rem 1.4rem; border-bottom: 1px solid #e7e5e4; font-size: .92rem; } td:first-child { color: #78716c; width: 40%; }
@endsection
@section('body')
<div class="box">
    @if ($demo)<div class="demo">DATA SIMULASI · CONTOH UNTUK DEMO SOFTWARE REKANA</div>@endif
    <div class="stripe"></div>
    <div class="status {{ $valid ? 'ok' : 'no' }}">{{ $title }}: {{ $valid ? 'BERLAKU' : 'TIDAK BERLAKU' }}</div>
    <table>@foreach ($rows as $k => $v)<tr><td>{{ $k }}</td><td>{{ $v }}</td></tr>@endforeach</table>
</div>
@endsection
