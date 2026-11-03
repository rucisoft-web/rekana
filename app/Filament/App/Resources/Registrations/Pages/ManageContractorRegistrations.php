<?php

namespace App\Filament\App\Resources\Registrations\Pages;

use App\Filament\App\Resources\Registrations\ContractorRegistrationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageContractorRegistrations extends ManageRecords
{
    protected static string $resource = ContractorRegistrationResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
