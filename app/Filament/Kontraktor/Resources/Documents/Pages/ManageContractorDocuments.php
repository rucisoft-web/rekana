<?php

namespace App\Filament\Kontraktor\Resources\Documents\Pages;

use App\Filament\Kontraktor\Resources\Documents\ContractorDocumentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageContractorDocuments extends ManageRecords
{
    protected static string $resource = ContractorDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
