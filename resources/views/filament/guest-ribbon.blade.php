@php($tenant = filament()->getTenant())
@if (auth()->user()?->isDemoGuest())
    @php($samples = \App\Http\Controllers\DemoController::samples())
    <div class="rk-guest-ribbon" role="navigation" aria-label="Data contoh simulasi">
        <span><b>Mode tamu · coba tanpa menyimpan · data simulasi</b></span>
        <span>Perusahaan contoh:</span>
        @foreach ($samples as $o)
            <a href="{{ url('/app/'.$o->slug) }}" @class(['is-active' => $tenant?->is($o)])>{{ \App\Http\Controllers\DemoController::story($o)[0] }}</a>
        @endforeach
        @if ($tenant instanceof \App\Models\Owner && ($c = \App\Http\Controllers\DemoController::featuredContractor($tenant)))
            <a href="{{ url('/kontraktor/'.$c->slug) }}">Lihat sebagai rekanan: {{ $c->name }} →</a>
        @elseif ($tenant instanceof \App\Models\Contractor)
            <a href="{{ url('/app') }}" class="is-active">Rekanan: {{ $tenant->name }}</a>
        @endif
        <a href="{{ url('/') }}">Beranda Rekana</a>
    </div>
@endif
