<?php

namespace App\Filament\App\Tenancy;

use App\Enums\InstitutionType;
use App\Enums\Role;
use App\Models\Institution;
use App\Models\Membership;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Tenancy\RegisterTenant;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;

/**
 * Halaman untuk pengguna yang belum menjadi anggota institusi mana pun.
 * Administrator platform dapat langsung mendaftarkan institusi pertamanya;
 * pengguna lain melihat petunjuk untuk menghubungi administrator.
 */
class DaftarInstitusi extends RegisterTenant
{
    public static function getLabel(): string
    {
        return static::isPlatformAdmin() ? 'Daftarkan Institusi' : 'Belum ada akses institusi';
    }

    public static function canView(): bool
    {
        // Tanpa ini Filament menampilkan 404 bagi pengguna yang belum punya institusi.
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
            TextInput::make('name')->label('Nama perguruan tinggi')->required()->maxLength(255),
            TextInput::make('code')->label('Kode PT (PDDikti)')->maxLength(20),
            Select::make('type')->label('Jenis PT (varian Buku 3)')->options(InstitutionType::class)->required()->default('pts_akademik'),
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

        $institution = Institution::create($data);
        Membership::withoutGlobalScopes()->create([
            'institution_id' => $institution->id, 'user_id' => auth()->id(), 'role' => Role::Admin, 'is_active' => true,
        ]);

        return $institution;
    }
}
