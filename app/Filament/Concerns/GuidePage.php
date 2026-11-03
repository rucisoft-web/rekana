<?php

namespace App\Filament\Concerns;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

/** Halaman Panduan Pimpinan: penjelasan bagaimana Akredia membantu pimpinan menyiapkan akreditasi institusi. */
abstract class GuidePage extends Page
{
    protected static string|UnitEnum|null $navigationGroup = 'Panduan Pimpinan';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookOpen;

    /** Tautan ke modul untuk dipakai di dalam teks panduan. */
    protected function moduleLinks(): array
    {
        $r = fn (string $class, string $page = 'index') => class_exists($class) ? $class::getUrl($page) : '#';

        return [
            'dashboard' => \App\Filament\App\Pages\DasborEksekutif::getUrl(),
            'project' => $r(\App\Filament\App\Resources\AccreditationProjects\AccreditationProjectResource::class),
            'items' => $r(\App\Filament\App\Resources\WorkItems\WorkItemResource::class),
            'gaps' => $r(\App\Filament\App\Resources\GapItems\GapItemResource::class),
            'documents' => $r(\App\Filament\App\Resources\Documents\DocumentResource::class),
            'led' => $r(\App\Filament\App\Resources\LedSections\LedSectionResource::class),
            'metrics' => $r(\App\Filament\App\Resources\MetricValues\MetricValueResource::class),
            'recon' => $r(\App\Filament\App\Resources\Reconciliations\ReconciliationResource::class),
            'tasks' => $r(\App\Filament\App\Resources\Tasks\TaskResource::class),
            'risks' => $r(\App\Filament\App\Resources\Risks\RiskResource::class),
            'decisions' => $r(\App\Filament\App\Resources\Decisions\DecisionResource::class),
            'findings' => $r(\App\Filament\App\Resources\Findings\FindingResource::class),
            'visit' => $r(\App\Filament\App\Resources\SiteVisits\SiteVisitResource::class),
            'surveys' => $r(\App\Filament\App\Resources\Surveys\SurveyResource::class),
            'programs' => $r(\App\Filament\App\Resources\StudyPrograms\StudyProgramResource::class),
            'lecturers' => $r(\App\Filament\App\Resources\Lecturers\LecturerResource::class),
            'members' => $r(\App\Filament\App\Resources\Memberships\MembershipResource::class),
            'simulation' => \App\Filament\App\Pages\SimulasiPenilaian::getUrl(),
            'map' => \App\Filament\App\Pages\PetaInstrumen::getUrl(),
            'reports' => \App\Filament\App\Pages\Laporan::getUrl(),
            'lkpt' => \App\Filament\App\Pages\TabelLkpt::getUrl(),
            'guide_intro' => \App\Filament\App\Pages\PanduanMengenalAkredia::getUrl(),
            'guide_roadmap' => \App\Filament\App\Pages\PanduanPetaJalan::getUrl(),
            'guide_iapt' => \App\Filament\App\Pages\PanduanIapt::getUrl(),
            'guide_role' => \App\Filament\App\Pages\PanduanPeranPimpinan::getUrl(),
            'guide_dashboard' => \App\Filament\App\Pages\PanduanMembacaDasbor::getUrl(),
            'guide_faq' => \App\Filament\App\Pages\PanduanTanyaJawab::getUrl(),
        ];
    }

    protected function getViewData(): array
    {
        return ['l' => $this->moduleLinks(), 'institution' => filament()->getTenant(), 'project' => filament()->getTenant()?->activeProject()];
    }
}
