<?php

namespace App\Filament\App\Resources\Memberships\Pages;

use App\Filament\App\Resources\Memberships\MembershipResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageMemberships extends ManageRecords
{
    protected static string $resource = MembershipResource::class;

    protected function getHeaderActions(): array
    {
        return [MembershipResource::createAction(CreateAction::make())];
    }
}
