<?php

namespace App\Filament\App\Resources\Areas\Pages;

use App\Filament\App\Resources\Areas\AreaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageAreas extends ManageRecords
{
    protected static string $resource = AreaResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
