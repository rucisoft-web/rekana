<?php

namespace App\Filament\App\Resources\Certificates\Pages;

use App\Filament\App\Resources\Certificates\CsmsCertificateResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageCsmsCertificates extends ManageRecords
{
    protected static string $resource = CsmsCertificateResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
