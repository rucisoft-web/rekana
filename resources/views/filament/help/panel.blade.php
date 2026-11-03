@php
    $help = \App\Support\Help::forCurrentRequest();
    $page = $help['page'];
    $official = \App\Support\Help::official();
    $related = array_values(array_unique(array_filter(array_merge([$help['indicator']], $page['indikator'] ?? []))));
@endphp
<script>
    function akHelp() {
        return {
            open: false,
            show() { this.open = true; this.$nextTick(() => this.$refs.panel.focus()); },
            async load(n) {
                if (! n) { this.$refs.loaded.innerHTML = ''; return; }
                this.$refs.loaded.innerHTML = '<p class="ak-help-note">Memuat…</p>';
                try {
                    const res = await fetch(@js(url('/bantuan/indikator')) + '/' + n, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                    this.$refs.loaded.innerHTML = res.ok ? await res.text() : '<p class="ak-help-note">Gagal memuat.</p>';
                } catch (e) { this.$refs.loaded.innerHTML = '<p class="ak-help-note">Gagal memuat.</p>'; }
            },
        };
    }
</script>
<div id="ak-help" class="ak-help" x-data="akHelp()" x-on:ak-help-open.window="show()" x-on:keydown.escape.window="open = false"
     x-show="open" x-cloak role="dialog" aria-modal="true" aria-labelledby="ak-help-title" data-help-key="{{ $help['key'] }}">
    <div class="ak-help-backdrop" x-on:click="open = false" x-show="open" x-transition.opacity></div>
    <aside class="ak-help-panel" x-show="open" x-transition:enter="ak-help-in" x-transition:leave="ak-help-out" x-ref="panel" tabindex="-1">
        <header class="ak-help-head">
            <div>
                <div class="ak-help-eyebrow">Bantuan halaman</div>
                <h2 id="ak-help-title">{{ $page['judul'] }}</h2>
            </div>
            <button type="button" class="ak-help-close" x-on:click="open = false" aria-label="Tutup bantuan">✕</button>
        </header>

        <div class="ak-help-body">
            <p class="ak-help-lead">{{ $page['ringkas'] }}</p>

            @if ($help['context'])
                <div class="ak-help-context">{{ $help['context'] }}</div>
            @endif

            @if (! empty($page['langkah']))
                <section>
                    <h3>Cara mengerjakan</h3>
                    <ol class="ak-help-steps">
                        @foreach ($page['langkah'] as $step)<li>{{ $step }}</li>@endforeach
                    </ol>
                </section>
            @endif

            @if (! empty($page['isian']))
                <section>
                    <h3>Penjelasan isian dan contoh</h3>
                    <dl class="ak-help-fields">
                        @foreach ($page['isian'] as [$label, $desc, $example])
                            <div>
                                <dt>{{ $label }}</dt>
                                <dd>{{ $desc }}</dd>
                                @if ($example)<dd class="ak-help-example"><span>Contoh</span>{{ $example }}</dd>@endif
                            </div>
                        @endforeach
                    </dl>
                </section>
            @endif

            @if (! empty($page['resmi']) || ! empty($page['lkpt']))
                <section>
                    <h3>Ketentuan resmi BAN-PT</h3>
                    @if (! empty($page['resmi']))
                        <ul class="ak-help-official">
                            @foreach ($page['resmi'] as $line)<li>{{ $line }}</li>@endforeach
                        </ul>
                    @endif
                    @if (! empty($page['lkpt']))
                        <p class="ak-help-note"><b>Tabel LKPT terkait:</b> {{ implode(' · ', $page['lkpt']) }}</p>
                    @endif
                </section>
            @endif

            @if ($related)
                <section>
                    <h3>Rujukan resmi per indikator</h3>
                    <p class="ak-help-note">Teks asli Lampiran 3c (aspek penilaian, sumber data, deskripsi pemenuhan Terakreditasi dan Unggul) dan petunjuk Panduan LED. Klik untuk membuka.</p>
                    @foreach ($related as $n)
                        @if ($official[$n] ?? null)
                            <details class="ak-help-ind" @if ($n === $help['indicator']) open @endif>
                                <summary><b>Indikator {{ $n }}</b> {{ \Illuminate\Support\Str::limit(preg_replace('/\s+/', ' ', $official[$n]['indikator']), 110) }}</summary>
                                @include('help.indicator', ['ind' => $official[$n]])
                            </details>
                        @endif
                    @endforeach
                </section>
            @endif

            @if (! empty($page['tips']))
                <section>
                    <h3>Tips dan kesalahan umum</h3>
                    <ul class="ak-help-tips">
                        @foreach ($page['tips'] as $tip)<li>{{ $tip }}</li>@endforeach
                    </ul>
                </section>
            @endif

            <section>
                <h3>Telusuri indikator lain</h3>
                <select class="ak-input" x-on:change="load($event.target.value)" aria-label="Pilih indikator IAPT 4.1">
                    <option value="">Pilih indikator 1–39…</option>
                    @foreach ($official as $n => $ind)
                        <option value="{{ $n }}">{{ $n }}. {{ $ind['kriteria'] }}: {{ \Illuminate\Support\Str::limit(preg_replace('/\s+/', ' ', $ind['indikator']), 70) }}</option>
                    @endforeach
                </select>
                <div class="ak-help-loaded" x-ref="loaded" aria-live="polite"></div>
            </section>

            <footer class="ak-help-source">
                Sumber: Peraturan BAN-PT No. 35 Tahun 2025 tentang IAPT 4.1, Lampiran 2 (Kriteria dan Prosedur), Lampiran 3c (Sistem dan Acuan Penilaian PTS Akademik), Lampiran 4a (Panduan LED), dan Lampiran 4b (Panduan LKPT).
                Ambang varian lain (PTN, vokasi, PJJ) dapat berbeda; selalu cocokkan dengan Lampiran 3 varian institusi Anda di
                <a href="https://www.banpt.or.id" target="_blank" rel="noopener">banpt.or.id</a>.
            </footer>
        </div>
    </aside>
</div>
