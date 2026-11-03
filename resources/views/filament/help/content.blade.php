{{-- Isi satu entri bantuan. Variabel: $page (array), $image (?string). --}}
<p class="rk-help-lead">{{ $page['ringkas'] }}</p>
@if (! empty($page['fase']))
    <p class="rk-help-phase"><span>Fase IOGP 423</span>{{ $page['fase'] }}</p>
@endif

@if ($image)
    <figure class="rk-help-shot">
        <a href="{{ $image }}" target="_blank" rel="noopener" title="Buka gambar ukuran penuh">
            <img src="{{ $image }}" alt="Tangkapan layar: {{ $page['gambar'] ?? $page['judul'] }}" loading="lazy">
        </a>
        <figcaption>{{ $page['gambar'] ?? $page['judul'] }} <span>Data simulasi demo.</span></figcaption>
    </figure>
@endif

@if (! empty($page['konsep']))
    <section>
        <h3>Konsep yang perlu dipahami</h3>
        <dl class="rk-help-concepts">
            @foreach ($page['konsep'] as [$term, $desc])
                <div><dt>{{ $term }}</dt><dd>{{ $desc }}</dd></div>
            @endforeach
        </dl>
    </section>
@endif

@if (! empty($page['langkah']))
    <section>
        <h3>Cara menggunakan dan mengisi data</h3>
        <ol class="rk-help-steps">
            @foreach ($page['langkah'] as $step)<li>{{ $step }}</li>@endforeach
        </ol>
    </section>
@endif

@if (! empty($page['isian']))
    <section>
        <h3>Penjelasan isian dan contoh</h3>
        <dl class="rk-help-fields">
            @foreach ($page['isian'] as [$label, $desc, $example])
                <div>
                    <dt>{{ $label }}</dt>
                    <dd>{{ $desc }}</dd>
                    @if ($example)<dd class="rk-help-example"><span>Contoh</span>{{ $example }}</dd>@endif
                </div>
            @endforeach
        </dl>
    </section>
@endif

@if (! empty($page['tips']))
    <section>
        <h3>Tips dan kesalahan umum</h3>
        <ul class="rk-help-tips">
            @foreach ($page['tips'] as $tip)<li>{{ $tip }}</li>@endforeach
        </ul>
    </section>
@endif
