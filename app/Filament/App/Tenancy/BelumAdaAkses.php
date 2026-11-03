<?php

namespace App\Filament\App\Tenancy;

use App\Enums\Role;
use App\Models\Membership;
use App\Models\Owner;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Tenancy\RegisterTenant;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;

/**
 * Halaman bagi pengguna yang belum menjadi anggota owner mana pun. Administrator platform dapat
 * mendaftarkan owner (pelanggan SaaS) baru; pengguna lain melihat petunjuk menghubungi administrator.
 */
class BelumAdaAkses extends RegisterTenant
{
    public static function getLabel(): string
    {
        return static::isPlatformAdmin() ? 'Daftarkan Perusahaan' : 'Belum ada akses perusahaan';
    }

    public static function canView(): bool
    {
        return auth()->check() && (static::isPlatformAdmin() || ! Filament::getTenant());
    }

    protected static function isPlatformAdmin(): bool
    {
        return (bool) auth()->user()?->is_platform_admin;
    }

    public function form(Schema $schema): Schema
    {
        if (! static::isPlatformAdmin()) {
            return $schema->components([View::make('filament.app.belum-ada-akses')]);
        }

        return $schema->components([
            TextInput::make('name')->label('Nama perusahaan / lokasi')->required()->maxLength(255),
            TextInput::make('code')->label('Kode singkat')->maxLength(20),
            TextInput::make('industry')->label('Industri'),
            TextInput::make('city')->label('Kota'),
        ]);
    }

    protected function getFormActions(): array
    {
        return static::isPlatformAdmin() ? parent::getFormActions() : [];
    }

    protected function handleRegistration(array $data): Model
    {
        abort_unless(static::isPlatformAdmin(), 403);

        $owner = Owner::create($data);
        Membership::withoutGlobalScopes()->create(['owner_id' => $owner->id, 'user_id' => auth()->id(), 'role' => Role::Admin, 'is_active' => true]);

        return $owner;
    }
}
