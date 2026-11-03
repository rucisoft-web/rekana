@php
    $k = $d['kpi'];
    $cfg = $d['cfg'];
    $num = fn ($n, $dec = 0) => number_format((float) $n, $dec, ',', '.');
    $c = ['teal' => '#d45a06', 'teal2' => '#fb9a24', 'blue' => '#1d4ed8', 'sky' => '#0ea5e9', 'green' => '#10b981', 'amber' => '#f59e0b', 'red' => '#e11d48', 'gray' => '#94a3b8', 'violet' => '#7c3aed'];
    $tone = ['success' => $c['green'], 'warning' => $c['amber'], 'danger' => $c['red'], 'info' => $c['sky'], 'gray' => $c['gray']];
    $resultColor = ['tinggi' => $c['teal'], 'menengah' => $c['sky'], 'rendah' => $c['amber'], 'tidak_lulus' => $c['gray'], 'gagal_kritis' => $c['red']];
    $quadColor = ['andalan' => $c['green'], 'pantau' => $c['amber'], 'bina' => $c['sky'], 'hindari' => $c['red']];
    $quadLabel = ['andalan' => 'Andalan', 'pantau' => 'Pantau (skor baik, kinerja K3 buruk)', 'bina' => 'Bina (K3 baik, sistem lemah)', 'hindari' => 'Hindari'];
    $sc = collect($d['scorecard']);
    $reliable = $sc->where('tone', 'success')->count();
    $ready = $k['workers_carded'] + $k['workers_blocked'] > 0 ? round($k['workers_carded'] * 100 / ($k['workers_carded'] + $k['workers_blocked'])) : 100;
    $chart = fn (array $cfg) => json_encode($cfg);
@endphp
<x-filament-panels::page>
<div class="rk-dash">
    <div class="rk-hero">
        <div>
            <div class="rk-eyebrow">{{ $d['owner']->name }}</div>
            <h1>Monitoring Rekanan</h1>
            <p>{{ $k['registrations'] }} rekanan terdaftar · {{ $k['contracts_live'] }} kontrak berjalan senilai Rp{{ $num($k['contract_value'] / 1_000_000) }} juta · {{ $k['workers_carded'] }} pekerja berkartu aktif</p>
            <p class="rk-muted">Enam tahap IOGP 423 dalam satu layar · per {{ $d['today']->translatedFormat('l, d F Y') }}</p>
        </div>
        <div class="rk-hero-stats">
            <div class="rk-ring" style="--pct: {{ $sc->count() ? round($reliable * 100 / $sc->count()) : 0 }}"><strong>{{ $reliable }}/{{ $sc->count() }}</strong><span>rekanan andalan</span></div>
            <div class="rk-ring {{ $ready < 90 ? 'warn' : '' }}" style="--pct: {{ $ready }}"><strong>{{ $ready }}%</strong><span>pekerja siap masuk</span></div>
        </div>
    </div>

    <div class="rk-kpis">
        <div class="rk-kpi {{ $k['permits_waiting'] ? 'danger' : 'good' }}"><span>Izin kerja menunggu</span><strong>{{ $k['permits_waiting'] }}</strong><small>{{ $k['permits_active'] }} izin sedang aktif</small></div>
        <div class="rk-kpi {{ $k['workers_blocked'] ? 'danger' : 'good' }}"><span>Pekerja tertahan</span><strong>{{ $k['workers_blocked'] }}</strong><small>tanpa kartu berlaku</small></div>
        <div class="rk-kpi {{ $k['pq_waiting'] ? 'warning' : 'good' }}"><span>PQ menunggu keputusan</span><strong>{{ $k['pq_waiting'] }}</strong><small>{{ $k['pq_oldest_days'] !== null ? 'tertua '.$k['pq_oldest_days'].' hari' : 'tidak ada antrean' }} · {{ $k['pq_clarification'] }} klarifikasi</small></div>
        <div class="rk-kpi {{ $k['certs_expiring'] ? 'warning' : 'good' }}"><span>Sertifikat CSMS berlaku</span><strong>{{ $k['certs_valid'] }}</strong><small>{{ $k['certs_expiring'] }} habis ≤ {{ $cfg['expiry_days'] }} hari</small></div>
        <div class="rk-kpi"><span>Rekanan aktif</span><strong>{{ $k['registered'] }}</strong><small>{{ $k['pending_registration'] }} menunggu verifikasi</small></div>
        <div class="rk-kpi {{ $k['blacklisted'] ? 'danger' : '' }}"><span>Daftar hitam</span><strong>{{ $k['blacklisted'] }}</strong><small>tidak boleh diundang</small></div>
        <div class="rk-kpi {{ $k['docs_due'] ? 'warning' : 'good' }}"><span>Dokumen jatuh tempo</span><strong>{{ $k['docs_due'] }}</strong><small>{{ $k['docs_expired'] }} sudah kedaluwarsa</small></div>
        <div class="rk-kpi"><span>Kontrak berjalan</span><strong>{{ $k['contracts_live'] }}</strong><small>Rp{{ $num($k['contract_value'] / 1_000_000_000, 2) }} miliar</small></div>
    </div>

    <div class="rk-grid">
        <section class="rk-panel span-3">
            <h3>Perlu keputusan sekarang <small>diurutkan dari yang paling mendesak</small></h3>
            @if ($d['decisions'])
                <div class="rk-decisions">
                    @foreach ($d['decisions'] as [$level, $title, $detail])
                        <div class="rk-decision {{ $level }}"><b>{{ $title }}</b><p>{{ $detail }}</p></div>
                    @endforeach
                </div>
            @else
                <p class="rk-muted">Tidak ada yang perlu diputuskan hari ini.</p>
            @endif
        </section>

        <section class="rk-panel span-2">
            <h3>Corong rekanan IOGP 423 <small>dari pendaftaran sampai izin mulai kerja</small></h3>
            <div class="rk-chart h-300"><canvas data-rk-chart="{{ $chart([
                'type' => 'bar',
                'data' => ['labels' => array_column($d['funnel'], 'label'), 'datasets' => [[
                    'label' => 'Rekanan', 'data' => array_column($d['funnel'], 'value'),
                    'backgroundColor' => [$c['gray'], $c['sky'], $c['teal2'], $c['blue'], $c['teal'], $c['green']],
                ]]],
                'options' => ['indexAxis' => 'y', 'plugins' => ['legend' => ['display' => false]], 'scales' => ['x' => ['beginAtZero' => true, 'ticks' => ['precision' => 0]]]],
            ]) }}"></canvas></div>
        </section>

        <section class="rk-panel">
            <h3>Status prakualifikasi <small>{{ array_sum(array_column($d['pq_status'], 'value')) }} PQ</small></h3>
            <div class="rk-chart h-300"><canvas data-rk-chart="{{ $chart([
                'type' => 'doughnut',
                'data' => ['labels' => array_column($d['pq_status'], 'label'), 'datasets' => [[
                    'data' => array_column($d['pq_status'], 'value'), 'backgroundColor' => array_map(fn ($r) => $tone[$r['color']] ?? $c['gray'], $d['pq_status']),
                ]]],
                'options' => ['cutout' => '62%', 'plugins' => ['legend' => ['position' => 'bottom']]],
            ]) }}"></canvas></div>
        </section>
        <section class="rk-panel span-3">
            <h3>Matriks keputusan rekanan <small>skor PQ vs LTIFR 3 tahun · ukuran = jam kerja · LTIFR di atas {{ $cfg['ltifr_alert'] * 6 }} digambar di tepi kanan</small></h3>
            <div class="rk-chart h-340"><canvas data-rk-chart="{{ $chart([
                'type' => 'bubble',
                'data' => ['datasets' => collect($d['matrix'])->groupBy('quadrant')->map(fn ($rows, $q) => [
                    'label' => $quadLabel[$q], 'data' => $rows->values()->all(), 'backgroundColor' => $quadColor[$q].'99', 'borderColor' => $quadColor[$q],
                ])->values()->all()],
                'options' => [
                    'rkQuadrant' => ['x' => $cfg['ltifr_alert'], 'y' => config('rekana.pq.thresholds.menengah'), 'labels' => ['tl' => 'ANDALAN', 'tr' => 'PANTAU', 'bl' => 'BINA', 'br' => 'HINDARI']],
                    'scales' => [
                        'x' => ['title' => ['display' => true, 'text' => 'LTIFR (per 1 juta jam kerja)'], 'min' => 0, 'max' => $cfg['ltifr_alert'] * 6 + 0.5],
                        'y' => ['title' => ['display' => true, 'text' => 'Skor PQ (%)'], 'min' => 0, 'max' => 105],
                    ],
                ],
            ]) }}"></canvas></div>
        </section>


        <section class="rk-panel span-2">
            <h3>Skor PQ per rekanan <small>garis putus: ambang Rendah {{ $d['thresholds']['rendah'] }} · Menengah {{ $d['thresholds']['menengah'] }} · Tinggi {{ $d['thresholds']['tinggi'] }}</small></h3>
            <div class="rk-chart h-280"><canvas data-rk-chart="{{ $chart([
                'type' => 'bar',
                'data' => ['labels' => array_column($d['pq_scores'], 'name'), 'datasets' => [
                    ['type' => 'bar', 'label' => 'Skor PQ', 'data' => array_column($d['pq_scores'], 'score'), 'backgroundColor' => array_map(fn ($r) => $resultColor[$r['result']] ?? $c['gray'], $d['pq_scores']), 'order' => 2],
                    ['type' => 'line', 'label' => 'Ambang Tinggi', 'data' => array_fill(0, count($d['pq_scores']), $d['thresholds']['tinggi']), 'borderColor' => $c['teal'], 'borderDash' => [6, 4], 'pointRadius' => 0, 'borderWidth' => 1.5, 'order' => 1],
                    ['type' => 'line', 'label' => 'Ambang Menengah', 'data' => array_fill(0, count($d['pq_scores']), $d['thresholds']['menengah']), 'borderColor' => $c['sky'], 'borderDash' => [6, 4], 'pointRadius' => 0, 'borderWidth' => 1.5, 'order' => 1],
                    ['type' => 'line', 'label' => 'Ambang Rendah', 'data' => array_fill(0, count($d['pq_scores']), $d['thresholds']['rendah']), 'borderColor' => $c['amber'], 'borderDash' => [6, 4], 'pointRadius' => 0, 'borderWidth' => 1.5, 'order' => 1],
                ]],
                'options' => ['scales' => ['y' => ['min' => 0, 'max' => 100]]],
            ]) }}"></canvas></div>
            <div class="rk-legend">
                <span><i style="background:{{ $c['teal'] }}"></i>Lulus Tinggi</span><span><i style="background:{{ $c['sky'] }}"></i>Lulus Menengah</span>
                <span><i style="background:{{ $c['amber'] }}"></i>Lulus Rendah</span><span><i style="background:{{ $c['red'] }}"></i>Gagal butir kritis</span>
            </div>
        </section>

        <section class="rk-panel">
            <h3>Hasil induksi <small>pre/post test pekerja</small></h3>
            <div class="rk-chart h-280"><canvas data-rk-chart="{{ $chart([
                'type' => 'doughnut',
                'data' => ['labels' => ['Lulus', 'Tidak lulus'], 'datasets' => [['data' => [$d['induction']['passed'], $d['induction']['failed']], 'backgroundColor' => [$c['green'], $c['red']]]]],
                'options' => ['cutout' => '62%', 'plugins' => ['legend' => ['position' => 'bottom']]],
            ]) }}"></canvas></div>
        </section>

        <section class="rk-panel span-2">
            <h3>Tren izin kerja mingguan <small>{{ $cfg['weeks'] }} minggu · garis = persentase dibatalkan</small></h3>
            <div class="rk-chart h-280"><canvas data-rk-chart="{{ $chart([
                'type' => 'bar',
                'data' => ['labels' => $d['permit_trend']['labels'], 'datasets' => array_merge(
                    array_map(fn ($s) => ['type' => 'bar', 'label' => $s['label'], 'data' => $s['data'], 'backgroundColor' => $s['color'], 'stack' => 'izin', 'yAxisID' => 'y'], $d['permit_trend']['series']),
                    [['type' => 'line', 'label' => '% dibatalkan', 'data' => $d['permit_trend']['cancel_rate'], 'borderColor' => $c['red'], 'backgroundColor' => $c['red'], 'tension' => .35, 'yAxisID' => 'y1', 'borderWidth' => 2]],
                )],
                'options' => ['scales' => [
                    'x' => ['stacked' => true], 'y' => ['stacked' => true, 'beginAtZero' => true, 'ticks' => ['precision' => 0], 'title' => ['display' => true, 'text' => 'Jumlah izin']],
                    'y1' => ['position' => 'right', 'beginAtZero' => true, 'suggestedMax' => 30, 'grid' => ['drawOnChartArea' => false], 'title' => ['display' => true, 'text' => '% batal']],
                ]],
            ]) }}"></canvas></div>
        </section>

        <section class="rk-panel">
            <h3>Kategori risiko pekerjaan <small>hasil penilaian risiko</small></h3>
            <div class="rk-chart h-280"><canvas data-rk-chart="{{ $chart([
                'type' => 'doughnut',
                'data' => ['labels' => array_column($d['risk_mix'], 'label'), 'datasets' => [['data' => array_column($d['risk_mix'], 'value'), 'backgroundColor' => array_map(fn ($r) => $tone[$r['color']], $d['risk_mix'])]]],
                'options' => ['cutout' => '62%', 'plugins' => ['legend' => ['position' => 'bottom']]],
            ]) }}"></canvas></div>
        </section>

        <section class="rk-panel span-2">
            <h3>Jatuh tempo 6 bulan ke depan <small>sertifikat, dokumen, surat sehat, kompetensi, dan kartu</small></h3>
            <div class="rk-chart h-280"><canvas data-rk-chart="{{ $chart([
                'type' => 'bar',
                'data' => ['labels' => $d['expiry']['labels'], 'datasets' => array_map(fn ($s) => ['label' => $s['label'], 'data' => $s['data'], 'backgroundColor' => $s['color'], 'stack' => 'exp'], $d['expiry']['series'])],
                'options' => ['scales' => ['x' => ['stacked' => true], 'y' => ['stacked' => true, 'beginAtZero' => true, 'ticks' => ['precision' => 0]]]],
            ]) }}"></canvas></div>
        </section>

        <section class="rk-panel">
            <h3>Bahaya dominan <small>jumlah aktivitas · skor rata-rata</small></h3>
            <div class="rk-chart h-280"><canvas data-rk-chart="{{ $chart([
                'type' => 'bar',
                'data' => ['labels' => array_map(fn ($h) => \Illuminate\Support\Str::limit($h['label'], 28), $d['hazards']), 'datasets' => [
                    ['label' => 'Aktivitas', 'data' => array_column($d['hazards'], 'n'), 'backgroundColor' => $c['amber']],
                    ['label' => 'Skor rata-rata', 'data' => array_column($d['hazards'], 'avg'), 'backgroundColor' => $c['red']],
                ]],
                'options' => ['indexAxis' => 'y', 'scales' => ['x' => ['beginAtZero' => true]]],
            ]) }}"></canvas></div>
        </section>

        <section class="rk-panel">
            <h3>Tren K3 seluruh rekanan <small>LTIFR · TRIR · jam kerja</small></h3>
            <div class="rk-chart h-280"><canvas data-rk-chart="{{ $chart([
                'type' => 'bar',
                'data' => ['labels' => $d['safety_trend']['labels'], 'datasets' => [
                    ['type' => 'line', 'label' => 'LTIFR', 'data' => $d['safety_trend']['ltifr'], 'borderColor' => $c['red'], 'backgroundColor' => $c['red'], 'yAxisID' => 'y', 'tension' => .3, 'borderWidth' => 2.5],
                    ['type' => 'line', 'label' => 'TRIR', 'data' => $d['safety_trend']['trir'], 'borderColor' => $c['violet'], 'backgroundColor' => $c['violet'], 'yAxisID' => 'y', 'tension' => .3, 'borderWidth' => 2.5],
                    ['type' => 'bar', 'label' => 'Jam kerja (ribu)', 'data' => array_map(fn ($h) => round($h / 1000), $d['safety_trend']['manhours']), 'backgroundColor' => $c['teal'].'55', 'yAxisID' => 'y1'],
                ]],
                'options' => ['scales' => ['y' => ['beginAtZero' => true, 'title' => ['display' => true, 'text' => 'Tingkat']], 'y1' => ['position' => 'right', 'beginAtZero' => true, 'grid' => ['drawOnChartArea' => false]]]],
            ]) }}"></canvas></div>
        </section>

        <section class="rk-panel">
            <h3>Izin kerja per rekanan <small>seluruh periode</small></h3>
            <div class="rk-chart h-280"><canvas data-rk-chart="{{ $chart([
                'type' => 'bar',
                'data' => ['labels' => array_column($d['permit_by_contractor'], 'name'), 'datasets' => [
                    ['label' => 'Ditutup normal', 'data' => array_column($d['permit_by_contractor'], 'closed'), 'backgroundColor' => $c['green'], 'stack' => 'p'],
                    ['label' => 'Dibatalkan', 'data' => array_column($d['permit_by_contractor'], 'cancelled'), 'backgroundColor' => $c['red'], 'stack' => 'p'],
                    ['label' => 'Berjalan / diajukan', 'data' => array_column($d['permit_by_contractor'], 'open'), 'backgroundColor' => $c['amber'], 'stack' => 'p'],
                ]],
                'options' => ['indexAxis' => 'y', 'scales' => ['x' => ['stacked' => true, 'ticks' => ['precision' => 0]], 'y' => ['stacked' => true]]],
            ]) }}"></canvas></div>
        </section>

        <section class="rk-panel">
            <h3>Beban per area <small>pekerja berkartu · izin 30 hari</small></h3>
            <div class="rk-chart h-280"><canvas data-rk-chart="{{ $chart([
                'type' => 'bar',
                'data' => ['labels' => array_map(fn ($a) => \Illuminate\Support\Str::limit($a['label'], 24), $d['area_load']), 'datasets' => [
                    ['label' => 'Pekerja berkartu', 'data' => array_column($d['area_load'], 'workers'), 'backgroundColor' => $c['teal']],
                    ['label' => 'Izin 30 hari', 'data' => array_column($d['area_load'], 'permits'), 'backgroundColor' => $c['sky']],
                    ['label' => 'Izin risiko tinggi', 'data' => array_column($d['area_load'], 'high'), 'backgroundColor' => $c['red']],
                ]],
                'options' => ['indexAxis' => 'y', 'scales' => ['x' => ['beginAtZero' => true, 'ticks' => ['precision' => 0]]]],
            ]) }}"></canvas></div>
        </section>

        <section class="rk-panel span-3">
            <h3>Rapor rekanan <small>diurutkan dari yang perlu perhatian · LTIFR/TRIR rekap 3 tahun · izin 90 hari</small></h3>
            <div class="rk-table-wrap">
                <table class="rk-table">
                    <thead><tr><th>Rekanan</th><th>Status</th><th>PQ</th><th>Sertifikat CSMS</th><th class="num">LTIFR</th><th class="num">TRIR</th><th class="num">Fatal</th><th class="num">Kontrak</th><th class="num">Pekerja</th><th class="num">Izin</th><th class="num">% batal</th><th>Rekomendasi</th></tr></thead>
                    <tbody>
                    @foreach ($d['scorecard'] as $r)
                        <tr>
                            <td><b>{{ $r['name'] }}</b><br><small class="rk-muted">{{ $r['vendor'] }} · {{ $r['field'] }}</small></td>
                            <td><span class="rk-pill {{ $r['registration']->getColor() }}">{{ $r['registration']->getLabel() }}</span></td>
                            <td>@if ($r['pq_status'])<span class="rk-pill {{ $r['pq_status']->getColor() }}">{{ $r['pq_status']->getLabel() }}</span>@if ($r['pq_score'] !== null)<br><small>{{ $num($r['pq_score'], 1) }}%</small>@endif @else - @endif</td>
                            <td>@if ($r['cert_risk']) {{ $r['cert_risk']->getLabel() }}<br><small class="rk-muted">s.d. {{ $r['cert_until']->translatedFormat('d M Y') }}</small> @else <span class="rk-muted">belum ada</span> @endif</td>
                            <td class="num {{ ($r['ltifr'] ?? 0) > $cfg['ltifr_alert'] ? 'bad' : '' }}">{{ $r['ltifr'] !== null ? $num($r['ltifr'], 2) : '-' }}</td>
                            <td class="num {{ ($r['trir'] ?? 0) > $cfg['trir_alert'] ? 'bad' : '' }}">{{ $r['trir'] !== null ? $num($r['trir'], 2) : '-' }}</td>
                            <td class="num {{ $r['fatalities'] ? 'bad' : '' }}">{{ $r['fatalities'] }}</td>
                            <td class="num">{{ $r['contracts'] }}</td>
                            <td class="num">{{ $r['workers'] }}</td>
                            <td class="num">{{ $r['permits'] }}</td>
                            <td class="num">{{ $r['cancel_pct'] !== null ? $r['cancel_pct'].'%' : '-' }}</td>
                            <td><span class="rk-pill {{ $r['tone'] }}">{{ $r['advice'] }}</span></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        @if (count($d['blocked']))
        <section class="rk-panel span-3">
            <h3>Pekerja tertahan di gerbang <small>belum boleh masuk lokasi sampai syarat dipenuhi</small></h3>
            <div class="rk-table-wrap">
                <table class="rk-table">
                    <thead><tr><th>Pekerja</th><th>Rekanan</th><th>Alasan</th></tr></thead>
                    <tbody>
                    @foreach ($d['blocked'] as $b)
                        <tr><td><b>{{ $b['worker'] }}</b><br><small class="rk-muted">{{ $b['position'] }}</small></td><td>{{ $b['contractor'] }}</td><td>{{ implode(' ', $b['reasons']) }}</td></tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </section>
        @endif
    </div>
</div>
</x-filament-panels::page>
