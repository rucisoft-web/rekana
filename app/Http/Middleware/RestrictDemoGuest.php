<?php

namespace App\Http\Middleware;

use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** Akun tamu demo dipakai bersama: halaman profil (nama, surel, kata sandi, MFA) ditutup. */
class RestrictDemoGuest
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->isDemoGuest() && $request->routeIs('filament.*.auth.profile')) {
            return redirect()->to(Filament::getUrl());
        }

        return $next($request);
    }
}
