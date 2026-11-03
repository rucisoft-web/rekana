<!doctype html>
<html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>@yield('title') · Rekana</title>
<style>
    body { font-family: Inter, Arial, sans-serif; color: #1c1917; margin: 0; background: #e7e5e4; }
    .bar { background: #1c1917; color: #fafaf9; padding: .6rem 1rem; display: flex; justify-content: space-between; align-items: center; font-size: .85rem; }
    .bar button { background: #fbbf24; border: 0; padding: .45rem .9rem; border-radius: 6px; font-weight: 700; cursor: pointer; }
    .page { max-width: 900px; margin: 1.5rem auto; }
    .demo { background: #ea580c; color: #fff; text-align: center; font-weight: 800; font-size: .75rem; letter-spacing: .06em; padding: .3rem; }
    .stripe { height: 10px; background: repeating-linear-gradient(-45deg, #fbbf24 0 12px, #1c1917 12px 24px); }
    @media print { body { background: #fff; } .bar { display: none; } .page { margin: 0 auto; } }
    @yield('css')
</style></head>
<body>
<div class="bar"><span>Rekana · pratinjau cetak</span><button onclick="window.print()">Cetak</button></div>
<div class="page">@yield('body')</div>
</body></html>
