<?php

namespace App\Filament\App\Resources\WorkPlans\Pages;

use App\Filament\App\Resources\WorkPlans\WorkPlanResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageWorkPlans extends ManageRecords
{
    protected static string $resource = WorkPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
