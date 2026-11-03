<?php

namespace App\Filament\Kontraktor\Resources\Permits\Pages;

use App\Filament\Kontraktor\Resources\Permits\WorkPermitResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageWorkPermits extends ManageRecords
{
    protected static string $resource = WorkPermitResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
