@php
    $help = \App\Support\Help::forCurrentRequest();
    $panelId = filament()->getCurrentPanel()?->getId();
    $guideUrl = $panelId && filament()->getTenant() ? \App\Filament\Shared\Panduan::urlFor($panelId) : null;
@endphp
<div id="rk-help" class="rk-help" x-data="{ open: false }" x-on:rk-help-open.window="open = true; $nextTick(() => $refs.panel.focus())"
     x-on:keydown.escape.window="open = false" x-show="open" x-cloak role="dialog" aria-modal="true" aria-labelledby="rk-help-title" data-help-key="{{ $help['key'] }}">
    <div class="rk-help-backdrop" x-on:click="open = false" x-show="open" x-transition.opacity></div>
    <aside class="rk-help-panel" x-show="open" x-transition:enter="rk-help-in" x-transition:leave="rk-help-out" x-ref="panel" tabindex="-1">
        <header class="rk-help-head">
            <div>
                <div class="rk-help-eyebrow">Bantuan halaman</div>
                <h2 id="rk-help-title">{{ $help['page']['judul'] }}</h2>
            </div>
            <button type="button" class="rk-help-close" x-on:click="open = false" aria-label="Tutup bantuan">✕</button>
        </header>
        <div class="rk-help-body">
            @include('filament.help.content', ['page' => $help['page'], 'image' => $help['image']])
            @if ($guideUrl)
                <footer class="rk-help-foot"><a href="{{ $guideUrl }}">Buka panduan lengkap semua halaman →</a></footer>
            @endif
        </div>
    </aside>
</div>
