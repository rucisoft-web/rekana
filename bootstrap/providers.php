<?php

use App\Providers\AppServiceProvider;
use App\Providers\Filament\AppPanelProvider;
use App\Providers\Filament\KontraktorPanelProvider;

return [
    AppServiceProvider::class,
    AppPanelProvider::class,
    KontraktorPanelProvider::class,
];
