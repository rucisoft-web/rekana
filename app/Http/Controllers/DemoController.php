<?php

namespace App\Http\Controllers;

use App\Models\Institution;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Halaman publik (halaman depan dan "Akredia untuk Pimpinan Perguruan Tinggi") serta login tamu ke perguruan tinggi contoh.
 */
class DemoController extends Controller
{
    /** Urutan kartu di halaman login. */
    public const ORDER = ['unj', 'uin-suka'];

    /** Kartu perguruan tinggi contoh di halaman publik: nama, jenis dan jalur, skenario, akun pimpinan. */
    public const STORIES = [
        'unj' => ['Universitas Negeri Jakarta', 'PTN Badan Hukum · jalur Unggul', 'AKI sekitar 75. Satu syarat perlu Unggul (proporsi lektor kepala dan guru besar) belum terpenuhi; SK berakhir Februari 2027.', 'rektor@unj.akredia.test'],
        'uin-suka' => ['UIN Sunan Kalijaga Yogyakarta', 'PTN Akademik (PTKIN) · jalur Unggul', 'Persiapan dini reakreditasi dengan IAPT 4.1. Syarat perlu terpenuhi; tim memperkuat analisis capaian kelulusan dan bukti dampak riset sebelum unggah September 2027.', 'rektor@uin-suka.akredia.test'],
    ];

    public function home(): View
    {
        return view('landing', [
            'guestUrl' => self::guestUrl(),
            'loginUrl' => self::loginUrl(),
            'samples' => self::samples(),
        ]);
    }

    public function pimpinan(): View
    {
        return view('pimpinan.page', [
            'guestUrl' => self::guestUrl(),
            'loginUrl' => self::loginUrl(),
            'samples' => self::samples(),
        ]);
    }

    /** Penawaran workshop dua hari beserta materi lengkapnya. */
    public function workshop(): View
    {
        return view('workshop', ['loginUrl' => self::loginUrl()]);
    }

    public function guest(Request $request): RedirectResponse
    {
        $samples = self::samples();
        abort_if($samples->isEmpty(), 404);

        $institution = $samples->firstWhere('slug', $request->query('pt')) ?? $samples->first();

        Auth::guard(Filament::getPanel('app')->getAuthGuard())->login(self::guestUser());
        $request->session()->regenerate();

        return redirect()->to(url('/app/'.$institution->slug));
    }

    public static function loginUrl(): string
    {
        return url('/app/login');
    }

    /** Tautan "Masuk sebagai Tamu", atau null bila belum ada perguruan tinggi contoh untuk tamu. */
    public static function guestUrl(?string $slug = null): ?string
    {
        return self::samples()->isEmpty() ? null : route('demo.tamu', array_filter(['pt' => $slug]));
    }

    /** @return Collection<int, Institution> perguruan tinggi contoh yang dapat dibuka akun tamu */
    public static function samples(): Collection
    {
        if (! config('akredia.demo.guest') || ! $user = self::guestUser()) {
            return collect();
        }

        return $user->getTenants(Filament::getPanel('app'))->where('is_demo', true)
            ->sortBy(fn (Institution $i) => array_search($i->slug, self::ORDER, true) === false ? 99 : array_search($i->slug, self::ORDER, true))
            ->values();
    }

    /** Singkatan untuk pita tamu, misalnya "UNJ" atau "UIN SUKA". */
    public static function shortName(Institution $institution): string
    {
        return mb_strtoupper(str_replace('-', ' ', $institution->slug));
    }

    protected static function guestUser(): ?User
    {
        return User::where('email', User::DEMO_GUEST_EMAIL)->where('is_active', true)->first();
    }
}
