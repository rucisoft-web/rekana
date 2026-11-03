<?php

namespace App\Filament\Kontraktor\Resources\Bids\Pages;

use App\Filament\Kontraktor\Resources\Bids\TenderBidResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageTenderBids extends ManageRecords
{
    protected static string $resource = TenderBidResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
