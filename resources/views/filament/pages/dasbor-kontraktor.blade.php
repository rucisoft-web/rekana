@php
    $k = $d['kpi'];
    $cfg = $d['cfg'];
    $num = fn ($n, $dec = 0) => number_format((float) $n, $dec, ',', '.');
    $c = ['orange' => '#d45a06', 'amber' => '#f59e0b', 'green' => '#16a34a', 'red' => '#e11d48', 'gray' => '#a8a29e', 'sky' => '#0ea5e9', 'violet' => '#7c3aed', 'char' => '#44403c'];
    $chart = fn (array $cfg) => json_encode($cfg);
    $readyPct = $k['workers'] ? round($k['ready'] * 100 / $k['workers']) : 0;
@endphp
<x-filament-panels::page>
<div class="rk-dash">
    <div class="rk-hero">
        <div>
            <div class="rk-eyebrow">Portal kontraktor · {{ $d['contractor']->business_field }}</div>
            <h1>{{ $d['contractor']->name }}</h1>
            <p>Terdaftar di {{ $k['owners'] }} perusahaan · {{ $k['contracts_live'] }} kontrak berjalan · {{ $k['workers'] }} pekerja aktif</p>
            <p class="rk-muted">LTIFR 3 tahun {{ $k['ltifr'] !== null ? $num($k['ltifr'], 2) : '-' }} · TRIR {{ $k['trir'] !== null ? $num($k['trir'], 2) : '-' }} · {{ $num($k['manhours']) }} jam kerja · per {{ $d['today']->translatedFormat('l, d F Y') }}</p>
        </div>
        <div class="rk-hero-stats">
            <div class="rk-ring {{ $readyPct < 90 ? 'warn' : '' }}" style="--pct: {{ $readyPct }}"><strong>{{ $k['ready'] }}/{{ $k['workers'] }}</strong><span>pekerja siap masuk</span></div>
            <div class="rk-ring" style="--pct: {{ $k['owners'] ? round($k['certs'] * 100 / $k['owners']) : 0 }}"><strong>{{ $k['certs'] }}/{{ $k['owners'] }}</strong><span>sertifikat CSMS berlaku</span></div>
        </div>
    </div>

    <div class="rk-kpis">
        <div class="rk-kpi {{ $k['blocked'] ? 'danger' : 'good' }}"><span>Pekerja tertahan</span><strong>{{ $k['blocked'] }}</strong><small>tidak boleh masuk lokasi</small></div>
        <div class="rk-kpi good"><span>Siap masuk</span><strong>{{ $k['ready'] }}</strong><small>kartu akses berlaku</small></div>
        <div class="rk-kpi {{ $k['permits_active'] ? 'warning' : '' }}"><span>Izin kerja aktif</span><strong>{{ $k['permits_active'] }}</strong><small>{{ $k['permits_waiting'] }} menunggu persetujuan / aktivasi</small></div>
        <div class="rk-kpi"><span>Kontrak berjalan</span><strong>{{ $k['contracts_live'] }}</strong><small>di {{ $k['owners'] }} owner</small></div>
        <div class="rk-kpi {{ $k['docs_due'] ? 'warning' : 'good' }}"><span>Dokumen jatuh tempo</span><strong>{{ $k['docs_due'] }}</strong><small>dalam 30 hari</small></div>
        <div class="rk-kpi {{ ($k['ltifr'] ?? 0) > $cfg['ltifr_alert'] ? 'danger' : 'good' }}"><span>LTIFR 3 tahun</span><strong>{{ $k['ltifr'] !== null ? $num($k['ltifr'], 2) : '-' }}</strong><small>ambang owner {{ $cfg['ltifr_alert'] }}</small></div>
        <div class="rk-kpi {{ $k['fatalities'] ? 'danger' : 'good' }}"><span>Kecelakaan fatal</span><strong>{{ $k['fatalities'] }}</strong><small>3 tahun terakhir</small></div>
        <div class="rk-kpi"><span>Sertifikat CSMS</span><strong>{{ $k['certs'] }}</strong><small>dari {{ $k['owners'] }} owner</small></div>
    </div>

    <div class="rk-grid">
        <section class="rk-panel">
            <h3>Yang harus diurus <small>{{ count($d['todo']) }} hal</small></h3>
            @forelse ($d['todo'] as [$level, $title, $detail])
                <div class="rk-decision {{ $level }}"><b>{{ $title }}</b><p>{{ $detail }}</p></div>
            @empty
                <p class="rk-muted">Semua beres. Tidak ada yang perlu diurus hari ini.</p>
            @endforelse
        </section>

        <section class="rk-panel span-2">
            <h3>Kontrak berjalan <small>kemajuan waktu kontrak · pekerja berkartu · izin kerja</small></h3>
            @forelse ($d['contracts'] as $ct)
                <div class="rk-contract">
                    <div class="rk-contract-head">
                        <div><b>{{ $ct['title'] }}</b><br><small class="rk-muted">{{ $ct['owner'] }} · {{ $ct['number'] }} · {{ $ct['area'] }}</small></div>
                        <div class="rk-contract-meta">@if ($ct['risk'])<span class="rk-pill {{ $ct['risk']->getColor() }}">Risiko {{ $ct['risk']->getLabel() }}</span>@endif</div>
                    </div>
                    <div class="rk-bar"><span style="width: {{ $ct['progress'] }}%"></span></div>
                    <div class="rk-contract-foot">
                        <span>{{ $ct['start']->translatedFormat('d M Y') }} – {{ $ct['end']->translatedFormat('d M Y') }} · sisa {{ $ct['days_left'] }} hari</span>
                        <span><b>{{ $ct['workers'] }}</b> pekerja berkartu · <b>{{ $ct['permits'] }}</b> izin · <b>{{ $ct['active'] }}</b> aktif</span>
                    </div>
                </div>
            @empty
                <p class="rk-muted">Belum ada kontrak berjalan.</p>
            @endforelse
        </section>

        <section class="rk-panel span-2">
            <h3>Izin kerja per minggu <small>{{ $cfg['weeks'] }} minggu terakhir</small></h3>
            <div class="rk-chart h-280"><canvas data-rk-chart="{{ $chart([
                'type' => 'bar',
                'data' => ['labels' => $d['permit_trend']['labels'], 'datasets' => [
                    ['label' => 'Ditutup normal', 'data' => $d['permit_trend']['closed'], 'backgroundColor' => $c['green'], 'stack' => 'p'],
                    ['label' => 'Dibatalkan', 'data' => $d['permit_trend']['cancelled'], 'backgroundColor' => $c['red'], 'stack' => 'p'],
                    ['label' => 'Berjalan / diajukan', 'data' => $d['permit_trend']['open'], 'backgroundColor' => $c['amber'], 'stack' => 'p'],
                ]],
                'options' => ['scales' => ['x' => ['stacked' => true], 'y' => ['stacked' => true, 'beginAtZero' => true, 'ticks' => ['precision' => 0]]]],
            ]) }}"></canvas></div>
        </section>

        <section class="rk-panel">
            <h3>Status pekerja <small>gerbang masuk lokasi</small></h3>
            <div class="rk-chart h-280"><canvas data-rk-chart="{{ $chart([
                'type' => 'doughnut',
                'data' => ['labels' => array_column($d['worker_status'], 'label'), 'datasets' => [['data' => array_column($d['worker_status'], 'value'), 'backgroundColor' => array_column($d['worker_status'], 'color')]]],
                'options' => ['cutout' => '62%', 'plugins' => ['legend' => ['position' => 'bottom']]],
            ]) }}"></canvas></div>
        </section>

        <section class="rk-panel span-2">
            <h3>Jatuh tempo 6 bulan ke depan <small>perpanjang sebelum kartu pekerja terblokir</small></h3>
            <div class="rk-chart h-260"><canvas data-rk-chart="{{ $chart([
                'type' => 'bar',
                'data' => ['labels' => $d['expiry']['labels'], 'datasets' => array_map(fn ($s) => ['label' => $s['label'], 'data' => $s['data'], 'backgroundColor' => $s['color'], 'stack' => 'e'], $d['expiry']['series'])],
                'options' => ['scales' => ['x' => ['stacked' => true], 'y' => ['stacked' => true, 'beginAtZero' => true, 'ticks' => ['precision' => 0]]]],
            ]) }}"></canvas></div>
        </section>

        <section class="rk-panel">
            <h3>Jenis izin kerja <small>seluruh periode</small></h3>
            <div class="rk-chart h-260"><canvas data-rk-chart="{{ $chart([
                'type' => 'polarArea',
                'data' => ['labels' => array_column($d['permit_types'], 'label'), 'datasets' => [['data' => array_column($d['permit_types'], 'value'), 'backgroundColor' => [$c['red'].'bb', $c['violet'].'bb', $c['amber'].'bb', $c['sky'].'bb', $c['gray'].'bb']]]],
                'options' => ['plugins' => ['legend' => ['position' => 'bottom']]],
            ]) }}"></canvas></div>
        </section>

        <section class="rk-panel">
            <h3>Kinerja K3 tahunan <small>dilaporkan ke semua owner</small></h3>
            <div class="rk-chart h-260"><canvas data-rk-chart="{{ $chart([
                'type' => 'bar',
                'data' => ['labels' => $d['safety']['labels'], 'datasets' => [
                    ['type' => 'line', 'label' => 'LTIFR', 'data' => $d['safety']['ltifr'], 'borderColor' => $c['red'], 'backgroundColor' => $c['red'], 'tension' => .3, 'borderWidth' => 2.5, 'yAxisID' => 'y'],
                    ['type' => 'line', 'label' => 'TRIR', 'data' => $d['safety']['trir'], 'borderColor' => $c['violet'], 'backgroundColor' => $c['violet'], 'tension' => .3, 'borderWidth' => 2.5, 'yAxisID' => 'y'],
                    ['type' => 'line', 'label' => 'Ambang LTIFR', 'data' => array_fill(0, count($d['safety']['labels']), $cfg['ltifr_alert']), 'borderColor' => $c['char'], 'borderDash' => [5, 4], 'pointRadius' => 0, 'borderWidth' => 1, 'yAxisID' => 'y'],
                    ['type' => 'bar', 'label' => 'Jam kerja (ribu)', 'data' => $d['safety']['manhours'], 'backgroundColor' => $c['orange'].'55', 'yAxisID' => 'y1'],
                ]],
                'options' => ['scales' => ['y' => ['beginAtZero' => true], 'y1' => ['position' => 'right', 'beginAtZero' => true, 'grid' => ['drawOnChartArea' => false]]]],
            ]) }}"></canvas></div>
        </section>

        <section class="rk-panel span-2">
            <h3>Status di setiap owner <small>prakualifikasi dan sertifikat CSMS</small></h3>
            <div class="rk-table-wrap"><table class="rk-table">
                <thead><tr><th>Owner</th><th>Registrasi</th><th>PQ</th><th class="num">Skor</th><th>Sertifikat CSMS</th></tr></thead>
                <tbody>
                @foreach ($d['owners'] as $o)
                    <tr>
                        <td><b>{{ $o['owner'] }}</b><br><small class="rk-muted">Kode vendor {{ $o['vendor'] }}</small></td>
                        <td><span class="rk-pill {{ $o['status']->getColor() }}">{{ $o['status']->getLabel() }}</span></td>
                        <td>@if ($o['pq'])<span class="rk-pill {{ $o['pq']->status->getColor() }}">{{ $o['pq']->status->getLabel() }}</span>@else - @endif</td>
                        <td class="num">{{ $o['pq']?->score !== null ? $num($o['pq']->score, 1).'%' : '-' }}</td>
                        <td>@if ($o['cert']) Sampai risiko <b>{{ $o['cert']->max_risk->getLabel() }}</b> · s.d. {{ $o['cert']->valid_until->translatedFormat('d M Y') }} @else <span class="rk-muted">belum ada</span> @endif</td>
                    </tr>
                @endforeach
                </tbody>
            </table></div>
        </section>

        <section class="rk-panel span-3">
            <h3>Daftar pekerja dan status gerbang <small>kartu akses berlaku bila surat sehat, induksi, dan kontrak masih valid</small></h3>
            <div class="rk-table-wrap"><table class="rk-table">
                <thead><tr><th>Pekerja</th><th>Status</th><th>Kartu akses</th><th>Surat sehat</th><th>Induksi</th><th>Kompetensi</th><th>Catatan</th></tr></thead>
                <tbody>
                @foreach ($d['roster'] as $r)
                    <tr>
                        <td><b>{{ $r['name'] }}</b><br><small class="rk-muted">{{ $r['position'] }}</small></td>
                        <td><span class="rk-pill {{ ['siap' => 'success', 'tertahan' => 'danger', 'cadangan' => ''][$r['status']] }}">{{ ['siap' => 'Siap masuk', 'tertahan' => 'Tertahan', 'cadangan' => 'Cadangan'][$r['status']] }}</span></td>
                        <td>{{ $r['card'] ? 's.d. '.$r['card']->valid_until->translatedFormat('d M Y') : '-' }}@if ($r['card'])<br><small class="rk-muted">{{ $r['card']->contract->owner->name }}</small>@endif</td>
                        <td class="{{ $r['medical_ok'] ? '' : 'bad' }}">{{ $r['medical']?->translatedFormat('d M Y') ?? 'belum ada' }}</td>
                        <td class="{{ $r['induction'] && ! $r['induction']->passed ? 'bad' : '' }}">@if ($r['induction']){{ $r['induction']->passed ? 'Lulus' : 'Tidak lulus' }} ({{ $r['induction']->post_score }})@if ($r['induction']->valid_until)<br><small class="rk-muted">s.d. {{ $r['induction']->valid_until->translatedFormat('d M Y') }}</small>@endif @else - @endif</td>
                        <td><small>{{ implode(', ', $r['competencies']) }}</small>@if ($r['comp_soon'])<br><span class="rk-pill warning">{{ $r['comp_soon'] }} segera habis</span>@endif</td>
                        <td><small>{{ implode(' ', $r['reasons']) }}</small></td>
                    </tr>
                @endforeach
                </tbody>
            </table></div>
        </section>

        <section class="rk-panel span-3">
            <h3>Izin kerja terbaru <small>8 izin terakhir dari semua owner</small></h3>
            <div class="rk-table-wrap"><table class="rk-table">
                <thead><tr><th>Nomor</th><th>Pekerjaan</th><th>Jenis</th><th>Owner</th><th>Waktu</th><th>Pekerja</th><th>Status</th></tr></thead>
                <tbody>
                @foreach ($d['recent_permits'] as $p)
                    <tr>
                        <td>{{ $p->number }}</td><td>{{ $p->title }}</td>
                        <td><span class="rk-pill {{ $p->type->getColor() }}">{{ $p->type->getLabel() }}</span></td>
                        <td>{{ $p->contract->owner->name }}</td>
                        <td>{{ $p->starts_at->translatedFormat('d M H:i') }} – {{ $p->ends_at->format('H:i') }}</td>
                        <td><small>{{ $p->workers->pluck('name')->implode(', ') }}</small></td>
                        <td><span class="rk-pill {{ $p->status->getColor() }}">{{ $p->status->getLabel() }}</span></td>
                    </tr>
                @endforeach
                </tbody>
            </table></div>
        </section>
    </div>
</div>
</x-filament-panels::page>
