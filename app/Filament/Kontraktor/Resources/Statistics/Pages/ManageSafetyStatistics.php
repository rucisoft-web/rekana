<?php

namespace App\Filament\Kontraktor\Resources\Statistics\Pages;

use App\Filament\Kontraktor\Resources\Statistics\SafetyStatisticResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageSafetyStatistics extends ManageRecords
{
    protected static string $resource = SafetyStatisticResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
