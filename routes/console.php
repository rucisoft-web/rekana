<?php

use App\Enums\Role;
use App\Http\Controllers\DemoController;
use App\Models\Contractor;
use App\Models\ContractorDocument;
use App\Models\CsmsCertificate;
use App\Models\Membership;
use App\Models\Owner;
use App\Models\User;
use App\Services\MobilizationService;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Storage;

/*
| Perintah operasional Rekana. Cukup satu cron per menit. Di cPanel pilih "Once Per Minute"
| lalu isi kolom Command (tanpa bintang jadwal):
|   cd /home/USER/rekana && php artisan schedule:run >> /dev/null 2>&1
*/

Artisan::command('rekana:owner {slug : alamat singkat, mis. ajinomoto-mojokerto} {--name=} {--email=} {--password=} {--admin-name=}', function (string $slug) {
    // Membuat perusahaan owner (pelanggan) beserta administrator pertamanya.
    $name = $this->option('name') ?: $this->ask('Nama perusahaan owner');
    $email = $this->option('email') ?: $this->ask('Surel administrator owner');
    $password = $this->option('password') ?: $this->secret('Kata sandi administrator (minimal 10 karakter)');

    if (strlen((string) $password) < 10) {
        return $this->error('Kata sandi minimal 10 karakter.');
    }

    $owner = Owner::firstOrCreate(['slug' => $slug], ['name' => $name, 'is_active' => true]);
    $user = User::updateOrCreate(['email' => $email], ['name' => $this->option('admin-name') ?: 'Administrator '.$owner->name, 'password' => $password, 'is_active' => true]);
    Membership::withoutGlobalScopes()->updateOrCreate(['owner_id' => $owner->id, 'user_id' => $user->id], ['role' => Role::Admin->value, 'is_active' => true]);

    $this->info("Owner siap: {$owner->name}. Masuk di ".url("/app/{$owner->slug}")." dengan {$user->email}.");
})->purpose('Buat perusahaan owner baru beserta administrator pertamanya');

$removeDemo = function (Owner $owner) {
    $contractorIds = DB::table('contractor_registrations')->where('owner_id', $owner->id)->pluck('contractor_id');
    $userIds = Membership::withoutGlobalScopes()->where('owner_id', $owner->id)->pluck('user_id');
    $owner->delete();

    // Rekanan contoh yang tidak lagi terdaftar di owner mana pun ikut dihapus beserta berkasnya.
    $orphans = Contractor::whereIn('id', $contractorIds)->whereNotExists(fn ($q) => $q->from('contractor_registrations')->whereColumn('contractor_registrations.contractor_id', 'contractors.id'))->get();
    foreach ($orphans as $contractor) {
        $userIds = $userIds->merge(DB::table('contractor_users')->where('contractor_id', $contractor->id)->pluck('user_id'));
        Storage::disk('local')->delete(ContractorDocument::where('contractor_id', $contractor->id)->whereNotNull('file_path')->pluck('file_path')->all());
        $contractor->delete();
    }

    // Akun demo yang tidak lagi menjadi anggota owner atau rekanan mana pun.
    User::whereIn('id', $userIds->unique())->where('email', 'like', '%rekana.test')->where('is_platform_admin', false)
        ->whereDoesntHave('memberships')->whereNotExists(fn ($q) => $q->from('contractor_users')->whereColumn('contractor_users.user_id', 'users.id'))->delete();
};

Artisan::command('rekana:demo {perusahaan=semua : garudafood-gresik, medco-corridor, pakuwon-surabaya, atau semua} {--force : Muat ulang tanpa konfirmasi}', function (string $perusahaan = 'semua') use ($removeDemo) {
    $stories = collect(DemoController::STORIES)->map(fn ($s) => $s[3]);
    $seeders = $perusahaan === 'semua' ? $stories : $stories->only([$perusahaan]);

    if ($seeders->isEmpty()) {
        return $this->error('Pilihan: '.$stories->keys()->implode(', ').', atau semua.');
    }

    $existing = Owner::where('is_demo', true)->whereIn('slug', $seeders->keys())->get();
    if ($existing->isNotEmpty()) {
        if (! $this->option('force') && ! $this->confirm('Data contoh sudah ada dan akan dimuat ulang. Lanjutkan?')) {
            return;
        }
        $existing->each($removeDemo);
    }

    foreach ($seeders as $seeder) {
        $this->call('db:seed', ['--class' => $seeder, '--force' => true]);
    }
})->purpose('Muat data contoh simulasi: Garudafood, MedcoEnergi, Pakuwon Jati');

Artisan::command('rekana:remove-demo {perusahaan? : slug owner contoh; kosongkan untuk semua} {--force}', function (?string $perusahaan = null) use ($removeDemo) {
    $demo = Owner::where('is_demo', true)->when($perusahaan, fn ($q) => $q->where('slug', $perusahaan))->get();

    if ($demo->isEmpty()) {
        return $this->info('Tidak ada data contoh.');
    }

    if (! $this->option('force') && ! $this->confirm('Hapus '.$demo->count().' owner contoh beserta rekanan dan akun demonya?')) {
        return;
    }

    $demo->each($removeDemo);
    $this->info('Data contoh dihapus: '.$demo->pluck('name')->implode(', ').'.');
})->purpose('Hapus owner contoh, rekanan contoh, dan akun demo');

Artisan::command('rekana:expire-cards', function () {
    $this->info(app(MobilizationService::class)->expireCards().' kartu akses ditandai kedaluwarsa.');
})->purpose('Tandai kartu akses yang lewat masa berlaku');

Artisan::command('rekana:reminders', function () {
    $days = (int) config('rekana.dashboard.expiry_days', 60);
    $sent = 0;

    // Owner: sertifikat CSMS rekanan yang akan habis, dikirim ke administrator, pimpinan, dan evaluator HSE.
    foreach (Owner::where('is_active', true)->get() as $owner) {
        $certs = CsmsCertificate::withoutGlobalScopes()->where('owner_id', $owner->id)->whereNull('revoked_at')
            ->whereBetween('valid_until', [today(), today()->addDays($days)])->with('contractor')->get();
        if ($certs->isEmpty()) {
            continue;
        }
        $users = Membership::withoutGlobalScopes()->where('owner_id', $owner->id)->where('is_active', true)
            ->whereIn('role', [Role::Admin->value, Role::Pimpinan->value, Role::Evaluator->value])->with('user')->get()->pluck('user')->filter();
        foreach ($users as $user) {
            Notification::make()->title($certs->count().' sertifikat CSMS rekanan habis dalam '.$days.' hari')
                ->body($certs->take(5)->map(fn ($c) => $c->contractor->name.' ('.$c->valid_until->format('d M Y').')')->implode("\n"))
                ->warning()->sendToDatabase($user);
            $sent++;
        }
    }

    // Rekanan: dokumen perusahaan yang akan habis.
    $docs = ContractorDocument::whereBetween('expires_at', [today(), today()->addDays($days)])->get()->groupBy('contractor_id');
    foreach ($docs as $contractorId => $list) {
        $users = User::whereIn('id', DB::table('contractor_users')->where('contractor_id', $contractorId)->where('is_active', true)->where('role', '!=', 'tamu')->pluck('user_id'))->get();
        foreach ($users as $user) {
            Notification::make()->title($list->count().' dokumen perusahaan habis dalam '.$days.' hari')
                ->body($list->take(5)->map(fn ($d) => $d->title.' ('.$d->expires_at->format('d M Y').')')->implode("\n"))
                ->warning()->sendToDatabase($user);
            $sent++;
        }
    }

    $this->info("{$sent} pengingat dikirim.");
})->purpose('Ingatkan sertifikat CSMS dan dokumen rekanan yang akan kedaluwarsa');

Artisan::command('rekana:doctor', function () {
    $rows = [];
    $check = function (string $label, bool $ok, string $detail = '') use (&$rows) {
        $rows[] = [$ok ? 'OK' : 'GAGAL', $label, $detail];
    };

    $check('Versi PHP >= 8.3', version_compare(PHP_VERSION, '8.3.0', '>='), PHP_VERSION);
    foreach (['pdo_mysql', 'mbstring', 'openssl', 'zip', 'xml', 'fileinfo', 'intl', 'gd', 'curl', 'ctype', 'tokenizer'] as $ext) {
        $check("Ekstensi {$ext}", extension_loaded($ext));
    }

    $migrated = false;
    try {
        $check('Koneksi database', true, DB::connection()->getDriverName());
        $migrated = DB::getSchemaBuilder()->hasTable('owners');
        $check('Tabel sudah dimigrasi', $migrated);
    } catch (Throwable $e) {
        $check('Koneksi database', false, $e->getMessage());
    }

    $check('APP_KEY terisi', filled(config('app.key')));
    $check('APP_DEBUG mati (produksi)', ! config('app.debug') || app()->isLocal(), config('app.debug') ? 'true' : 'false');
    $check('storage/ dapat ditulis', is_writable(storage_path()));
    $check('bootstrap/cache dapat ditulis', is_writable(base_path('bootstrap/cache')));
    $check('Aset Filament terpasang', file_exists(public_path('css/filament/filament/app.css')));

    if ($migrated) {
        $check('Template PQ bawaan', DB::table('questionnaire_templates')->whereNull('owner_id')->exists(), 'jalankan php artisan db:seed bila kosong');
        $check('Minimal satu owner', Owner::exists(), 'buat dengan php artisan rekana:owner');
        $heartbeat = rescue(fn () => cache('rekana:schedule-heartbeat'), null, false);
        $check('Penjadwal (cron) berjalan', $heartbeat !== null, $heartbeat ?? 'belum ada detak; pasang cron schedule:run');
    }

    $this->table(['', 'Pemeriksaan', 'Keterangan'], $rows);
})->purpose('Periksa kesiapan server (PHP, ekstensi, database, izin folder, cron)');

Schedule::call(fn () => cache()->put('rekana:schedule-heartbeat', now()->toDateTimeString(), now()->addDay()))->everyFiveMinutes()->name('heartbeat');
Schedule::command('rekana:expire-cards')->dailyAt('00:10')->withoutOverlapping();
Schedule::command('rekana:reminders')->dailyAt('07:00')->withoutOverlapping();
Schedule::command('queue:work --stop-when-empty --max-time=50')->everyMinute()->withoutOverlapping();
