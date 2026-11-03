<?php

namespace App\Filament\App\Resources\AccessCards\Pages;

use App\Filament\App\Resources\AccessCards\AccessCardResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageAccessCards extends ManageRecords
{
    protected static string $resource = AccessCardResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
