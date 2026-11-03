<div class="space-y-4 text-sm text-gray-600 dark:text-gray-300">
    <p>Akun <strong>{{ auth()->user()?->email }}</strong> sudah masuk, tetapi belum terdaftar sebagai anggota institusi mana pun.</p>
    <p>Minta administrator Akredia di perguruan tinggi Anda menambahkan akun ini melalui menu <em>Administrasi → Pengguna &amp; Peran</em>, lalu muat ulang halaman ini.</p>
    @if (auth()->user()?->is_platform_admin)
        <p><a class="text-primary-600 underline" href="{{ url('/platform') }}">Buka Admin Platform</a></p>
    @endif
    <form method="POST" action="{{ filament()->getLogoutUrl() }}">
        @csrf
        <button type="submit" class="text-primary-600 underline">Keluar dan masuk dengan akun lain</button>
    </form>
</div>
