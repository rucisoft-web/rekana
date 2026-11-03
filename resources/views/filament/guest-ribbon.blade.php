@php($tenant = filament()->getTenant())
@if (auth()->user()?->isDemoGuest())
    @php($samples = \App\Http\Controllers\DemoController::samples())
    <div class="ak-guest-ribbon" role="navigation" aria-label="Perguruan tinggi contoh">
        <span class="ak-guest-mode">Mode tamu · hanya lihat</span>
        <span class="ak-guest-switch">
            <span>Data contoh:</span>
            @foreach ($samples as $pt)
                <a href="{{ url('/app/'.$pt->slug) }}" @class(['is-active' => $tenant?->is($pt)]) title="{{ $pt->name }}">{{ \App\Http\Controllers\DemoController::shortName($pt) }}</a>
            @endforeach
        </span>
        <a class="ak-guest-more" href="{{ route('pimpinan') }}">Akredia untuk Pimpinan PT</a>
    </div>
@endif
