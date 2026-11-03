<x-filament-panels::page>
    <div class="rk-guide">
        <div class="rk-guide-intro">
            <p>{{ $intro }}</p>
            <p class="rk-muted">Klik judul untuk membuka. Pada setiap halaman, tombol <b>Bantuan</b> di bilah atas membuka penjelasan halaman tersebut. Gambar diambil dari data demo simulasi.</p>
        </div>
        <nav class="rk-guide-toc" aria-label="Daftar isi panduan">
            @foreach ($entries as $key => $entry)
                <a href="#{{ str_replace('.', '-', $key) }}">{{ $entry['judul'] }}</a>
            @endforeach
        </nav>
        @foreach ($entries as $key => $entry)
            <details class="rk-guide-item" id="{{ str_replace('.', '-', $key) }}" @if ($loop->first) open @endif>
                <summary>
                    <span class="rk-guide-no">{{ $loop->iteration }}</span>
                    <span><b>{{ $entry['judul'] }}</b>@if (! empty($entry['fase']))<small>{{ $entry['fase'] }}</small>@endif</span>
                </summary>
                <div class="rk-guide-body">
                    @include('filament.help.content', ['page' => $entry, 'image' => $entry['image']])
                </div>
            </details>
        @endforeach
    </div>
</x-filament-panels::page>
