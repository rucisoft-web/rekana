<div class="space-y-4 text-sm text-gray-600 dark:text-gray-300">
    <p>Akun <strong>{{ auth()->user()?->email }}</strong> sudah masuk, tetapi belum terdaftar di perusahaan mana pun.</p>
    <p>Minta administrator Rekana di perusahaan Anda menambahkan akun ini melalui menu <em>Pengaturan → Pengguna &amp; Peran</em>, lalu muat ulang halaman ini. Bila Anda kontraktor, masuk lewat <a class="text-primary-600 underline" href="{{ url('/kontraktor') }}">portal kontraktor</a>.</p>
    <form method="POST" action="{{ filament()->getLogoutUrl() }}">
        @csrf
        <button type="submit" class="text-primary-600 underline">Keluar dan masuk dengan akun lain</button>
    </form>
</div>
