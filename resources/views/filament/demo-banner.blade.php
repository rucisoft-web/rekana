@php($tenant = filament()->getTenant())
@if ($tenant?->is_demo)
<div class="rk-demo-banner" role="note">
    <span class="rk-demo-tag">DATA SIMULASI</span>
    <span><b>Seluruh data di layar ini adalah data contoh simulasi untuk keperluan demo software Rekana.</b>
        @if ($tenant instanceof \App\Models\Owner)
            {{ $tenant->settings['demo_note'] ?? 'Nama rekanan, staf, pekerja, skor, dan aktivitas adalah ilustrasi.' }}
        @else
            {{ $tenant->name }} adalah perusahaan rekaan; pekerja, dokumen, statistik K3, dan izin kerjanya disusun untuk menjelaskan fitur portal kontraktor.
        @endif
        Tidak ada data yang berasal dari sistem atau dokumen internal perusahaan mana pun.
        @if (auth()->user()?->isDemoGuest()) Anda masuk sebagai <b>tamu</b>: semua formulir dan tombol dapat dicoba, tetapi perubahan tidak disimpan. @endif
    </span>
</div>
@endif
