<?php

namespace App\Filament\App\Resources\WorkPermits\Pages;

use App\Filament\App\Resources\WorkPermits\WorkPermitResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageWorkPermits extends ManageRecords
{
    protected static string $resource = WorkPermitResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()->mutateDataUsing(fn (array $data) => $data + ["requested_by" => auth()->id()])];
    }
}
