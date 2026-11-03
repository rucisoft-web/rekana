<?php

namespace App\Filament\App\Resources\Inductions\Pages;

use App\Filament\App\Resources\Inductions\InductionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageInductions extends ManageRecords
{
    protected static string $resource = InductionResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
