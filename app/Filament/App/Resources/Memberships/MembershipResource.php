<?php

namespace App\Filament\App\Resources\Memberships;

use App\Enums\Role;
use App\Filament\App\Resources\Memberships\Pages\ManageMemberships;
use App\Filament\Concerns\OwnerAccess;
use App\Models\Membership;
use App\Models\User;
use BackedEnum;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use UnitEnum;

/** Pengguna perusahaan dan perannya. Peran menentukan siapa menilai, menyetujui, dan mengawasi. */
class MembershipResource extends Resource
{
    use OwnerAccess;

    protected static ?string $slug = 'pengguna';

    protected static ?string $model = Membership::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;
    protected static string|UnitEnum|null $navigationGroup = 'Pengaturan';
    protected static ?int $navigationSort = 20;
    protected static ?string $modelLabel = 'Pengguna';
    protected static ?string $pluralModelLabel = 'Pengguna & Peran';

    public static function canCreate(): bool { return static::canManage(); }
    public static function canEdit(Model $record): bool { return static::canManage(); }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('user_name')->label('Nama')->required()->visibleOn('create'),
            TextInput::make('user_email')->label('Surel')->email()->required()->visibleOn('create')
                ->helperText('Bila surel belum terdaftar, akun baru dibuat dan pengguna mengatur sandi lewat "Lupa sandi".'),
            Select::make('role')->label('Peran')->options(Role::class)->required()->default('pengamat')
                ->helperText(fn ($state) => $state ? (Role::tryFrom($state instanceof Role ? $state->value : $state)?->description()) : null)->live(),
            TextInput::make('position_title')->label('Jabatan'),
            Select::make('area_id')->label('Area tanggung jawab (opsional)')->relationship('area', 'name'),
            Toggle::make('is_active')->label('Aktif')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('user.name')->label('Nama')->searchable()->weight('bold')->description(fn (Membership $m) => $m->user?->email),
            TextColumn::make('position_title')->label('Jabatan')->wrap(),
            TextColumn::make('role')->label('Peran')->badge(),
            TextColumn::make('area.name')->label('Area')->placeholder('Semua area')->toggleable(),
            IconColumn::make('is_active')->label('Aktif')->boolean(),
            TextColumn::make('user.last_login_at')->label('Terakhir masuk')->since()->placeholder('-'),
        ])->recordActions([EditAction::make(), DeleteAction::make()]);
    }

    public static function createAction(CreateAction $action): CreateAction
    {
        return $action->using(function (array $data) {
            $user = User::firstOrCreate(['email' => strtolower($data['user_email'])], ['name' => $data['user_name'], 'password' => Str::password(24)]);

            return Membership::create(['user_id' => $user->id, 'role' => $data['role'], 'position_title' => $data['position_title'] ?? null, 'area_id' => $data['area_id'] ?? null, 'is_active' => $data['is_active'] ?? true]);
        });
    }

    public static function getPages(): array
    {
        return ['index' => ManageMemberships::route('/')];
    }
}
