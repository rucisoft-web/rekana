<?php

namespace App\Filament\App\Resources\Templates\Pages;

use App\Filament\App\Resources\Templates\QuestionnaireTemplateResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageQuestionnaireTemplates extends ManageRecords
{
    protected static string $resource = QuestionnaireTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
