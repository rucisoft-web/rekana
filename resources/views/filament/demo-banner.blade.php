@php($tenant = filament()->getTenant())
@if ($tenant?->is_demo)
<div class="ak-demo-banner" role="note">
    <span class="ak-demo-tag">DATA DEMO</span>
    <span><b>{{ $tenant->name }}</b> di aplikasi ini disusun untuk menjelaskan fitur-fitur Akredia. Profil institusi diambil dari laman resminya; angka kinerja, nama staf, bukti, dan aktivitas adalah ilustrasi, bukan data resmi institusi.
    @if (auth()->user()?->isDemoGuest())
        Anda masuk sebagai <b>tamu (hanya lihat)</b>; ganti perguruan tinggi contoh dari pita di atas layar.
    @endif
    </span>
</div>
@endif
