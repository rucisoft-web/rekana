<?php

namespace App\Providers;

use App\Enums\Role;
use App\Models\AuditLog;
use App\Models\Owner;
use App\Models\User;
use App\Support\CurrentOwner;
use App\Support\GuestPreview;
use Carbon\Carbon;
use Filament\Actions\Action;
use Illuminate\Auth\Events\Login;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->scoped(CurrentOwner::class);
    }

    /** Nama tombol yang mengubah data; disembunyikan dari Pengamat (termasuk akun tamu demo). */
    private const WRITE_ACTIONS = [
        'create', 'edit', 'delete', 'forceDelete', 'restore', 'replicate', 'attach', 'detach', 'associate', 'dissociate', 'import',
        'activate', 'approve', 'approve_start', 'assess', 'award', 'blacklist', 'block', 'cancel', 'clarify', 'close', 'copy',
        'evaluate', 'invite', 'issue_card', 'issue_cards', 'kickoff', 'open_tender', 'reactivate', 'revoke', 'score', 'start_pq', 'submit',
    ];

    public function boot(): void
    {
        Carbon::setLocale('id');

        // Pengamat hanya melihat: tombol pengubah data disembunyikan di panel owner.
        Action::configureUsing(function (Action $action) {
            if (! in_array($action->getName(), self::WRITE_ACTIONS, true)) {
                return;
            }

            $hidden = (fn () => $this->isHidden)->call($action);
            $action->hidden(function (Action $current) use ($hidden) {
                $tenant = filament()->getTenant();

                $user = auth()->user();

                return ($tenant instanceof Owner && $user?->roleIn($tenant) === Role::Pengamat && ! $user->isDemoGuest())
                    || (bool) $current->evaluate($hidden);
            });
        }, isImportant: true);

        // Mode pratinjau tamu demo: semua formulir dan tombol dapat dibuka, tetapi tidak ada data yang tersimpan.
        $guard = function (string $event, array $payload) {
            $model = $payload[0] ?? null;
            if ((app()->runningInConsole() && ! app()->runningUnitTests()) || ! $model instanceof Model || $model instanceof User || $model instanceof AuditLog
                || ! str_starts_with($model::class, 'App\\Models\\') || ! auth()->user()?->isDemoGuest()) {
                return;
            }
            GuestPreview::block();
        };
        Event::listen('eloquent.saving: *', $guard);
        Event::listen('eloquent.deleting: *', $guard);

        Event::listen(Login::class, function (Login $event) {
            $event->user->forceFill(['last_login_at' => now()])->saveQuietly();

            foreach ($event->user->memberships()->where('is_active', true)->pluck('owner_id') as $id) {
                AuditLog::record('login', null, ['email' => $event->user->email], $id);
            }
        });
    }
}
