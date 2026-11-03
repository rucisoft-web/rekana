<?php

namespace App\Filament\App\Resources\Tenders\Pages;

use App\Enums\TenderStatus;
use App\Filament\App\Resources\Tenders\TenderResource;
use App\Models\Contractor;
use App\Services\TenderService;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;

class ViewTender extends ViewRecord
{
    protected static string $resource = TenderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('invite')->label('Undang rekanan')->icon(Heroicon::OutlinedEnvelope)
                ->visible(fn () => TenderResource::role()?->canProcure() && in_array($this->record->status, [TenderStatus::Draf, TenderStatus::Dibuka], true))
                ->schema([
                    Select::make('contractors')->label('Rekanan yang memenuhi syarat')->multiple()->required()
                        ->options(fn () => app(TenderService::class)->eligibleContractors($this->record)->pluck('name', 'id'))
                        ->helperText('Hanya rekanan aktif dengan sertifikat CSMS yang mencakup kategori risiko '.$this->record->workPlan->risk_category?->getLabel().' yang muncul di daftar ini.'),
                ])
                ->action(function (array $data) {
                    foreach ($data['contractors'] as $id) {
                        TenderResource::rule(fn () => app(TenderService::class)->invite($this->record, Contractor::findOrFail($id)), 'Undangan dikirim ke portal rekanan');
                    }
                }),
        ];
    }
}
