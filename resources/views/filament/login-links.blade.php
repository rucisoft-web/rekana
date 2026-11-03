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
            <div class="rk-login-guests">
                <button type="submit" name="sebagai" value="owner" class="rk-login-guest">
                    <b>Tamu sebagai Owner</b><small>Perusahaan pemberi kerja: dasbor, PQ, seleksi, izin kerja</small>
                </button>
                <button type="submit" name="sebagai" value="kontraktor" class="rk-login-guest alt">
                    <b>Tamu sebagai Kontraktor</b><small>Portal rekanan: dokumen, pekerja, PQ, HSE Plan</small>
                </button>
            </div>
            <p class="rk-login-hint">Tamu dapat membuka semua menu, formulir, dan tombol tindakan. Perubahan tidak disimpan.</p>
        </form>
        <p class="rk-login-note"><b>Data simulasi.</b> Seluruh data contoh dibuat untuk keperluan demo software Rekana. Profil perusahaan diambil dari laporan tahunan publik; rekanan, pekerja, skor, dan aktivitas adalah rekaan.</p>
    @endif
    <a class="rk-login-home" href="{{ url('/') }}">Kenali Rekana</a>
</div>
