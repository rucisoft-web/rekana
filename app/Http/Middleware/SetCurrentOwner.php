<?php

namespace App\Http\Middleware;

use App\Models\Owner;
use App\Support\CurrentOwner;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** Menetapkan owner aktif dari tenant Filament agar global scope BelongsToOwner bekerja. */
class SetCurrentOwner
{
    public function handle(Request $request, Closure $next): Response
    {
        $tenant = filament()->getTenant();
        if ($tenant instanceof Owner) {
            app(CurrentOwner::class)->set($tenant);
        }

        return $next($request);
    }
}
