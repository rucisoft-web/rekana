/* Akredia: grafik dasbor (Chart.js) dan editor LED. Tanpa build step agar mudah diunggah ke cPanel. */
(function () {
    const isDark = () => document.documentElement.classList.contains('dark');

    function applyDefaults() {
        if (!window.Chart) return;
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

    function render(root) {
        if (!window.Chart) return;
        applyDefaults();
        (root || document).querySelectorAll('canvas[data-ak-chart]').forEach((el) => {
            if (el._akChart) return;
            let cfg;
            try { cfg = JSON.parse(el.dataset.akChart); } catch (e) { return; }
            const links = el.dataset.links ? JSON.parse(el.dataset.links) : null;
            cfg.options = cfg.options || {};
            const fmt = new Intl.NumberFormat('id-ID', { maximumFractionDigits: 2 });
            cfg.options.plugins = Object.assign({ tooltip: { callbacks: { label: (c) => `${c.dataset.label || c.label}: ${fmt.format(c.parsed.y ?? c.parsed.r ?? c.parsed)}` } } }, cfg.options.plugins || {});
            if (cfg.type === 'bar' && cfg.options.indexAxis === 'y') {
                cfg.options.plugins.tooltip = { callbacks: { label: (c) => `${c.dataset.label}: ${fmt.format(c.parsed.x)}` } };
            }
            if (cfg.type === 'doughnut') {
                cfg.options.plugins.tooltip = { callbacks: { label: (c) => `${c.label}: ${fmt.format(c.parsed)}` } };
            }
            if (links) {
                cfg.options.onClick = (evt, elements) => {
                    if (elements.length && links[elements[0].index]) window.location = links[elements[0].index];
                };
                cfg.options.onHover = (evt, elements) => { evt.native.target.style.cursor = elements.length ? 'pointer' : 'default'; };
            }
            el._akChart = new Chart(el, cfg);
        });
    }

    document.addEventListener('DOMContentLoaded', () => render());
    document.addEventListener('livewire:navigated', () => render());
    document.addEventListener('livewire:init', () => {
        Livewire.hook('morph.updated', ({ el }) => { if (el.querySelector) setTimeout(() => render(el), 0); });
    });

    // Editor LED: penghitung kata langsung dan sisip penanda pada posisi kursor.
    window.akLedEditor = function (limit) {
        return {
            limit,
            words: 0,
            init() { this.$nextTick(() => this.count()); },
            plain() {
                const ta = this.$refs.body;
                return ta ? ta.value.replace(/\[B:[A-Z0-9\/\-]+\]/g, ' ') : '';
            },
            count() {
                const t = this.plain().replace(/<[^>]*>/g, ' ').trim();
                this.words = t === '' ? 0 : t.split(/\s+/u).length;
            },
            insert(token) {
                if (!token) return;
                const ta = this.$refs.body;
                if (!ta) return;
                const start = ta.selectionStart ?? ta.value.length;
                const end = ta.selectionEnd ?? start;
                const before = ta.value.slice(0, start);
                const spacer = before && !/\s$/.test(before) ? ' ' : '';
                ta.value = before + spacer + token + ta.value.slice(end);
                const pos = start + spacer.length + token.length;
                ta.focus();
                ta.setSelectionRange(pos, pos);
                ta.dispatchEvent(new Event('input', { bubbles: true }));
                this.count();
            },
        };
    };
})();
