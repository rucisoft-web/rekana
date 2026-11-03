/* Rekana: grafik dasbor (Chart.js) tanpa build step agar mudah diunggah ke cPanel. */
(function () {
    const isDark = () => document.documentElement.classList.contains('dark');
    const fmt = new Intl.NumberFormat('id-ID', { maximumFractionDigits: 2 });

    function applyDefaults() {
        const dark = isDark();
        Chart.defaults.font.family = 'Inter, ui-sans-serif, system-ui, sans-serif';
        Chart.defaults.font.size = 12;
        Chart.defaults.color = dark ? '#cbd5e1' : '#475569';
        Chart.defaults.borderColor = dark ? 'rgba(148,163,184,.18)' : 'rgba(15,23,42,.08)';
        Chart.defaults.maintainAspectRatio = false;
        Chart.defaults.responsive = true;
        Chart.defaults.plugins.legend.labels.boxWidth = 12;
        Chart.defaults.plugins.legend.labels.usePointStyle = true;
        Chart.defaults.plugins.tooltip.padding = 10;
        Chart.defaults.elements.bar.borderRadius = 4;
        Chart.defaults.elements.point.radius = 3;
    }

    // Garis ambang dan label kuadran untuk matriks keputusan (options.rkQuadrant = {x, y, labels}).
    const quadrant = {
        id: 'rkQuadrant',
        afterDraw(chart, args, opts) {
            const q = chart.config.options.rkQuadrant;
            if (!q) return;
            const { ctx, chartArea: a, scales: { x, y } } = chart;
            const px = x.getPixelForValue(q.x), py = y.getPixelForValue(q.y);
            ctx.save();
            ctx.strokeStyle = 'rgba(225,29,72,.55)';
            ctx.setLineDash([5, 4]);
            ctx.beginPath(); ctx.moveTo(px, a.top); ctx.lineTo(px, a.bottom); ctx.stroke();
            ctx.beginPath(); ctx.moveTo(a.left, py); ctx.lineTo(a.right, py); ctx.stroke();
            ctx.setLineDash([]);
            ctx.font = '600 11px Inter, sans-serif';
            ctx.fillStyle = isDark() ? 'rgba(226,232,240,.75)' : 'rgba(30,41,59,.6)';
            const l = q.labels || {};
            ctx.textAlign = 'left'; ctx.fillText(l.tl || '', a.left + 8, a.top + 14);
            ctx.textAlign = 'right'; ctx.fillText(l.tr || '', a.right - 8, a.top + 14);
            ctx.textAlign = 'left'; ctx.fillText(l.bl || '', a.left + 8, a.bottom - 8);
            ctx.textAlign = 'right'; ctx.fillText(l.br || '', a.right - 8, a.bottom - 8);
            ctx.restore();
        },
    };

    function tooltipFor(cfg) {
        const unit = cfg.options.rkUnit || '';
        if (cfg.type === 'bubble') {
            return { callbacks: { label: (c) => `${c.raw.name}: skor PQ ${fmt.format(c.raw.y)}, LTIFR ${fmt.format(c.raw.ltifr ?? c.raw.x)}, ${fmt.format(c.raw.manhours)} jam kerja` } };
        }
        if (cfg.type === 'doughnut' || cfg.type === 'pie' || cfg.type === 'polarArea') {
            return { callbacks: { label: (c) => `${c.label}: ${fmt.format(c.parsed.r ?? c.parsed)}${unit}` } };
        }
        const horizontal = cfg.options.indexAxis === 'y';
        return { callbacks: { label: (c) => `${c.dataset.label || c.label}: ${fmt.format(horizontal ? c.parsed.x : c.parsed.y)}${unit}` } };
    }

    function render(root) {
        if (!window.Chart) return;
        applyDefaults();
        (root || document).querySelectorAll('canvas[data-rk-chart]').forEach((el) => {
            if (el._rkChart) return;
            let cfg;
            try { cfg = JSON.parse(el.dataset.rkChart); } catch (e) { return; }
            cfg.options = cfg.options || {};
            cfg.options.plugins = Object.assign({ tooltip: tooltipFor(cfg) }, cfg.options.plugins || {});
            cfg.plugins = [quadrant];
            const links = el.dataset.links ? JSON.parse(el.dataset.links) : null;
            if (links) {
                cfg.options.onClick = (evt, elements) => {
                    if (elements.length && links[elements[0].index]) window.location = links[elements[0].index];
                };
                cfg.options.onHover = (evt, elements) => { evt.native.target.style.cursor = elements.length ? 'pointer' : 'default'; };
            }
            el._rkChart = new Chart(el, cfg);
        });
    }

    document.addEventListener('DOMContentLoaded', () => render());
    document.addEventListener('livewire:navigated', () => render());
    document.addEventListener('livewire:init', () => {
        Livewire.hook('morph.updated', ({ el }) => { if (el.querySelector) setTimeout(() => render(el), 0); });
    });
})();
