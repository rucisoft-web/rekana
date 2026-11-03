<?php

namespace App\Filament\Kontraktor\Resources\Workers\Pages;

use App\Filament\Kontraktor\Resources\Workers\WorkerResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageWorkers extends ManageRecords
{
    protected static string $resource = WorkerResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
