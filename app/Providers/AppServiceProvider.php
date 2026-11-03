<?php

namespace App\Providers;

use App\Enums\Role;
use App\Models\AuditLog;
use App\Models\Owner;
use App\Support\CurrentOwner;
use Carbon\Carbon;
use Filament\Actions\Action;
use Illuminate\Auth\Events\Login;
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

                return ($tenant instanceof Owner && auth()->user()?->roleIn($tenant) === Role::Pengamat)
                    || (bool) $current->evaluate($hidden);
            });
        }, isImportant: true);

        Event::listen(Login::class, function (Login $event) {
            $event->user->forceFill(['last_login_at' => now()])->saveQuietly();

            foreach ($event->user->memberships()->where('is_active', true)->pluck('owner_id') as $id) {
                AuditLog::record('login', null, ['email' => $event->user->email], $id);
            }
        });
    }
}
