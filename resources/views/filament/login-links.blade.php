@php($samples = \App\Http\Controllers\DemoController::samples())
<div class="rk-login-demo">
    @if ($samples->isNotEmpty())
        <div class="rk-login-divider"><span>atau coba tanpa akun</span></div>
        <form method="get" action="{{ route('demo.tamu') }}">
            <p><b>Pilih perusahaan contoh</b></p>
            <div class="rk-login-cards">
                @foreach ($samples as $i => $o)
                    @php([$short, $industry] = \App\Http\Controllers\DemoController::story($o))
                    <label class="rk-login-card"><input type="radio" name="owner" value="{{ $o->slug }}" @checked($i === 0)><b>{{ $short }}</b><small>{{ $industry }}</small></label>
                @endforeach
            </div>
            <button type="submit" class="rk-login-guest">Masuk sebagai Tamu</button>
        </form>
        <p class="rk-login-note"><b>Data simulasi.</b> Seluruh data contoh dibuat untuk keperluan demo software Rekana. Profil perusahaan diambil dari laporan tahunan publik; rekanan, pekerja, skor, dan aktivitas adalah rekaan.</p>
    @endif
    <a class="rk-login-home" href="{{ url('/') }}">Kenali Rekana</a>
</div>
