<?php

use App\Enums\Role;
use App\Enums\TaskStatus;
use App\Models\Institution;
use App\Models\Membership;
use App\Models\Task;
use App\Models\User;
use App\Services\EvidenceService;
use App\Services\ExecutiveDashboard;
use App\Services\InstrumentImporter;
use App\Services\ReadinessCalculator;
use App\Support\CurrentInstitution;
use Database\Seeders\DemoSeeder;
use Database\Seeders\UinSukaDemoSeeder;
use Database\Seeders\UnjDemoSeeder;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Storage;

/*
| Perintah operasional Akredia. Cukup satu cron per menit. Di cPanel pilih "Once Per Minute"
| lalu isi kolom Command (tanpa bintang jadwal):
|   cd /home/USER/akredia && php artisan schedule:run >> /dev/null 2>&1
*/

$institutions = fn (?string $slug) => Institution::query()->where('is_active', true)->when($slug, fn ($q) => $q->where('slug', $slug))->get();

Artisan::command('akredia:create-admin {email?} {--name=} {--password=}', function (?string $email = null) {
    $email ??= $this->ask('Email administrator platform');
    $password = $this->option('password') ?: $this->secret('Kata sandi (minimal 10 karakter)');

    if (strlen((string) $password) < 10) {
        return $this->error('Kata sandi minimal 10 karakter.');
    }

    $user = User::updateOrCreate(['email' => $email], [
        'name' => $this->option('name') ?: 'Administrator Akredia', 'password' => $password,
        'is_platform_admin' => true, 'is_active' => true,
    ]);
    $this->info("Administrator platform siap: {$user->email} (masuk di /platform)");
})->purpose('Buat atau perbarui akun administrator platform');

Artisan::command('akredia:instrument {--force : Jalankan tanpa konfirmasi}', function () {
    // Menyelaraskan Pustaka Instrumen dengan database/data/iapt41.php (ambang metrik dan penanda syarat perlu).
    if (! $this->option('force') && ! $this->confirm('Deskriptor, syarat perlu, dan ambang metrik yang diubah lewat Pustaka Instrumen akan ditimpa data bawaan. Lanjutkan?')) {
        return;
    }

    app(InstrumentImporter::class)->seed();
    $this->info('Pustaka Instrumen IAPT 4.1 diperbarui. Jalankan akredia:snapshot agar proyeksi dihitung ulang.');
})->purpose('Perbarui Pustaka Instrumen IAPT 4.1 dari data bawaan aplikasi');

Artisan::command('akredia:demo {institusi=semua : unj, uin-suka, unm, atau semua} {--force : Muat ulang tanpa konfirmasi}', function (string $institusi = 'semua') {
    // "semua" memuat perguruan tinggi contoh yang dapat dibuka lewat "Masuk sebagai Tamu"; UNM (fiktif) dimuat terpisah.
    $all = [UnjDemoSeeder::SLUG => UnjDemoSeeder::class, UinSukaDemoSeeder::SLUG => UinSukaDemoSeeder::class];
    $seeders = collect($all + [DemoSeeder::SLUG => DemoSeeder::class])->only($institusi === 'semua' ? array_keys($all) : [$institusi]);

    if ($seeders->isEmpty()) {
        return $this->error('Pilihan institusi: unj, uin-suka, unm, atau semua.');
    }

    if (Institution::whereIn('slug', $seeders->keys())->exists() && ! $this->option('force') && ! $this->confirm('Data contoh sudah ada dan akan dimuat ulang. Lanjutkan?')) {
        return;
    }

    foreach ($seeders as $seeder) {
        $this->call('db:seed', ['--class' => $seeder, '--force' => true]);
    }

    // Akun tamu lama (satu institusi) digantikan akun tamu bersama.
    User::where('email', 'tamu@unj.akredia.test')->delete();
})->purpose('Muat data contoh: UNJ dan UIN Sunan Kalijaga (profil dari laman resmi, angka ilustrasi) atau UNM (fiktif)');

Artisan::command('akredia:remove-demo {institusi? : slug institusi contoh; kosongkan untuk semua} {--force}', function (?string $institusi = null) {
    $demo = Institution::where('is_demo', true)->when($institusi, fn ($query) => $query->where('slug', $institusi))->get();

    if ($demo->isEmpty()) {
        return $this->info('Tidak ada data contoh.');
    }

    if (! $this->option('force') && ! $this->confirm('Hapus '.$demo->count().' institusi contoh beserta seluruh datanya?')) {
        return;
    }

    foreach ($demo as $institution) {
        Storage::disk(EvidenceService::DISK)->deleteDirectory('bukti/'.$institution->id);
        $userIds = Membership::withoutGlobalScopes()->where('institution_id', $institution->id)->pluck('user_id');
        $institution->delete();
        // Akun demo yang tidak lagi menjadi anggota institusi mana pun ikut dihapus.
        User::whereIn('id', $userIds)->where('email', 'like', '%akredia.test')->where('is_platform_admin', false)
            ->whereDoesntHave('memberships')->delete();
    }

    $this->info('Data contoh dihapus: '.$demo->pluck('name')->implode(', ').'.');
})->purpose('Hapus institusi contoh, akun demo, dan berkas buktinya');

Artisan::command('akredia:doctor', function () {
    $rows = [];
    $check = function (string $label, bool $ok, string $detail = '') use (&$rows) {
        $rows[] = [$ok ? 'OK' : 'GAGAL', $label, $detail];
    };

    $check('Versi PHP >= 8.3', version_compare(PHP_VERSION, '8.3.0', '>='), PHP_VERSION);

    foreach (['pdo_mysql', 'mbstring', 'openssl', 'zip', 'xml', 'fileinfo', 'intl', 'gd', 'curl', 'ctype', 'tokenizer'] as $ext) {
        $check("Ekstensi {$ext}", extension_loaded($ext));
    }

    $schema = null;

    try {
        $version = DB::selectOne('select version() as v')->v;
        $check('Koneksi database', true, $version);
        $schema = DB::getSchemaBuilder();
        $check('Tabel sudah dimigrasi', $schema->hasTable('institutions'));
    } catch (Throwable $e) {
        $check('Koneksi database', false, $e->getMessage());
    }

    $check('APP_KEY terisi', filled(config('app.key')));
    $check('APP_DEBUG mati (produksi)', ! config('app.debug') || app()->isLocal(), config('app.debug') ? 'true' : 'false');
    $check('storage/ dapat ditulis', is_writable(storage_path()));
    $check('bootstrap/cache dapat ditulis', is_writable(base_path('bootstrap/cache')));
    $check('Aset Filament terpasang', file_exists(public_path('css/filament/filament/app.css')));

    if ($schema?->hasTable('institutions')) {
        $check('Pustaka Instrumen IAPT 4.1', DB::table('instrument_versions')->where('code', 'IAPT-4.1')->exists());
        $check('Administrator platform', User::where('is_platform_admin', true)->exists());
        $heartbeat = rescue(fn () => cache('akredia:schedule-heartbeat'), null, false);
        $check('Penjadwal (cron) berjalan', $heartbeat !== null, $heartbeat ?? 'belum ada detak; pasang cron schedule:run');
    }

    $check('Asisten AI', true, config('akredia.ai.key') ? 'aktif' : 'nonaktif (opsional)');

    $this->table(['', 'Pemeriksaan', 'Keterangan'], $rows);
})->purpose('Periksa kesiapan server (PHP, ekstensi, database, izin folder, cron)');

Artisan::command('akredia:check-links {--institution=}', function () use ($institutions) {
    foreach ($institutions($this->option('institution')) as $institution) {
        $r = app(EvidenceService::class)->checkAll($institution->id);
        $this->line("{$institution->name}: {$r['checked']} tautan diperiksa, {$r['broken']} rusak.");
    }
})->purpose('Periksa tautan bukti dan hyperlink LED');

Artisan::command('akredia:snapshot {--institution=}', function () use ($institutions) {
    foreach ($institutions($this->option('institution')) as $institution) {
        app(CurrentInstitution::class)->runAs($institution, function () use ($institution) {
            app(EvidenceService::class)->expireDue($institution->id);

            if ($project = $institution->activeProject()) {
                $result = app(ReadinessCalculator::class)->refresh($project);
                $this->line("{$institution->name}: AKI {$result['aki']}");
            }
        });
    }
})->purpose('Hitung ulang kesiapan dan rekam snapshot harian');

Artisan::command('akredia:reminders {--institution=}', function () use ($institutions) {
    $escalate = (int) config('akredia.task_escalation_days', 7);
    $sent = 0;

    foreach ($institutions($this->option('institution')) as $institution) {
        app(CurrentInstitution::class)->runAs($institution, function () use ($escalate, &$sent) {
            $open = Task::query()->whereNotIn('status', [TaskStatus::Done->value, TaskStatus::Cancelled->value])->whereNotNull('due_date')->with('assignee')->get();

            foreach ($open->groupBy('assignee_id') as $tasks) {
                $user = $tasks->first()->assignee;
                $soon = $tasks->filter(fn ($t) => $t->due_date->between(today(), today()->addDays(3)));
                $late = $tasks->filter(fn ($t) => $t->due_date->lt(today()));

                if (! $user || ($soon->isEmpty() && $late->isEmpty())) {
                    continue;
                }

                Notification::make()->title('Pengingat tugas akreditasi')
                    ->body(trim(($late->count() ? "{$late->count()} tugas lewat tenggat. " : '').($soon->count() ? "{$soon->count()} tugas jatuh tempo dalam 3 hari." : '')))
                    ->warning()->sendToDatabase($user);
                $sent++;
            }

            // Eskalasi ke manajer untuk tugas yang terlambat lebih dari N hari.
            $overdue = $open->filter(fn ($t) => $t->due_date->lt(today()->subDays($escalate)) && ! $t->escalated_at);

            if ($overdue->isNotEmpty()) {
                $managers = Membership::query()->whereIn('role', [Role::Manajer->value, Role::Admin->value])->where('is_active', true)->with('user')->get()->pluck('user')->filter();

                foreach ($managers as $manager) {
                    Notification::make()->title('Eskalasi: '.$overdue->count().' tugas terlambat lebih dari '.$escalate.' hari')
                        ->body($overdue->take(5)->map(fn ($t) => $t->number.' '.$t->title)->implode("\n"))->danger()->sendToDatabase($manager);
                }

                Task::query()->whereKey($overdue->pluck('id'))->update(['escalated_at' => now()]);
            }
        });
    }

    $this->info("{$sent} pengingat dikirim.");
})->purpose('Kirim pengingat tenggat dan eskalasi tugas terlambat');

Artisan::command('akredia:weekly-report {--institution=}', function () use ($institutions) {
    foreach ($institutions($this->option('institution')) as $institution) {
        app(CurrentInstitution::class)->runAs($institution, function () use ($institution) {
            $project = $institution->activeProject();
            $html = view('reports.weekly', [
                'institution' => $institution,
                'd' => app(ExecutiveDashboard::class)->data($project),
                'gate' => $project ? app(ReadinessCalculator::class)->gate($project) : [],
            ])->render();
            $leaders = Membership::query()->whereIn('role', [Role::Pimpinan->value, Role::Manajer->value])->where('is_active', true)->with('user')->get()->pluck('user')->filter();

            foreach ($leaders as $user) {
                Mail::html($html, fn ($m) => $m->to($user->email)->subject('Ringkasan mingguan kesiapan akreditasi · '.$institution->name));
            }

            $this->line("{$institution->name}: dikirim ke {$leaders->count()} pimpinan/manajer.");
        });
    }
})->purpose('Kirim ringkasan mingguan kesiapan ke pimpinan');

Schedule::call(fn () => cache()->put('akredia:schedule-heartbeat', now()->toDateTimeString(), now()->addDay()))->everyFiveMinutes()->name('heartbeat');
Schedule::command('akredia:snapshot')->dailyAt('01:00')->withoutOverlapping();
Schedule::command('akredia:reminders')->dailyAt('07:00')->withoutOverlapping();
Schedule::command('akredia:check-links')->weeklyOn(0, '02:00')->withoutOverlapping();
Schedule::command('akredia:weekly-report')->weeklyOn(match (config('akredia.weekly_report_day')) {
    'sunday' => 0, 'tuesday' => 2, 'wednesday' => 3, 'thursday' => 4, 'friday' => 5, 'saturday' => 6, default => 1,
}, '06:30');
Schedule::command('queue:work --stop-when-empty --max-time=50')->everyMinute()->withoutOverlapping();
