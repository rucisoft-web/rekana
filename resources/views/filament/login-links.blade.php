@php($samples = \App\Http\Controllers\DemoController::samples())
<div class="ak-login-demo">
    @if ($samples->isNotEmpty())
        <div class="ak-login-divider"><span>atau coba tanpa akun</span></div>
        <form method="get" action="{{ route('demo.tamu') }}">
            <p class="ak-login-label">Pilih perguruan tinggi contoh</p>
            <div class="ak-login-cards">
                @foreach ($samples as $i => $pt)
                    <label class="ak-login-card">
                        <input type="radio" name="pt" value="{{ $pt->slug }}" @checked($i === 0)>
                        <b>{{ $pt->name }}</b>
                        <small>{{ $pt->type?->getLabel() }} · {{ $pt->city }}</small>
                    </label>
                @endforeach
            </div>
            <button type="submit" class="ak-login-guest">Masuk sebagai Tamu</button>
        </form>
        <p class="ak-login-note">Mode hanya lihat dengan data contoh. Perguruan tinggi dapat diganti kapan saja dari pita di atas layar.</p>
    @endif
    <a class="ak-login-pimpinan" href="{{ route('pimpinan') }}">Akredia untuk Pimpinan Perguruan Tinggi &rarr;</a>
    <a class="ak-login-home" href="{{ route('home') }}">Kenali fitur Akredia</a>
    <p class="ak-login-trust">Terenkripsi · Akses berbasis peran · Jejak audit</p>
</div>
