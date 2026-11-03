<?php

namespace App\Http\Controllers;

use App\Models\Contractor;
use App\Models\Owner;
use App\Models\User;
use Database\Seeders\GarudafoodDemoSeeder;
use Database\Seeders\MedcoDemoSeeder;
use Database\Seeders\PakuwonDemoSeeder;
use Filament\Facades\Filament;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/** Halaman depan publik dan login tamu (hanya lihat) ke tiga perusahaan contoh simulasi. */
class DemoController extends Controller
{
    /**
     * Kartu data contoh: slug owner => [judul singkat, industri, cerita skenario, kelas seeder].
     * Urutan array = urutan tampil.
     */
    public const STORIES = [
        'garudafood-gresik' => ['Garudafood', 'Manufaktur pangan (pabrik biskuit)', '14 rekanan pabrik: overhaul oven, silo tepung, PLTS atap, sanitasi malam, forklift, kantin. Induksi GMP dan halal, uji gas ruang terbatas, rekanan gagal butir kritis BPJS.', GarudafoodDemoSeeder::class],
        'medco-corridor' => ['MedcoEnergi', 'Hulu migas', 'Workover sumur, inspeksi separator, flowline. Kontingensi H2S, SIMOPS, well control, dan rekanan yang hanya layak risiko Menengah.', MedcoDemoSeeder::class],
        'pakuwon-surabaya' => ['Pakuwon Jati', 'Properti dan konstruksi gedung', 'Proyek Pakuwon Mall Semarang, gondola fasad Tunjungan Plaza, ground tank. Tower crane, perancah, dan rekanan daftar hitam.', PakuwonDemoSeeder::class],
    ];

    public function home(): View
    {
        return view('landing', ['samples' => self::samples(), 'loginUrl' => url('/app/login'), 'contractorLoginUrl' => url('/kontraktor/login')]);
    }

    /** Masuk sebagai tamu ke owner contoh (?owner=slug), atau ke portal rekanan unggulannya (?sebagai=kontraktor). */
    public function guest(Request $request): RedirectResponse
    {
        $samples = self::samples();
        abort_if($samples->isEmpty(), 404);

        $owner = $samples->firstWhere('slug', $request->query('owner')) ?? $samples->first();
        $user = self::guestUser();

        Auth::guard(Filament::getPanel('app')->getAuthGuard())->login($user);
        $request->session()->regenerate();

        if ($request->query('sebagai') === 'kontraktor' && $contractor = self::featuredContractor($owner)) {
            return redirect()->to(url('/kontraktor/'.$contractor->slug));
        }

        return redirect()->to(url('/app/'.$owner->slug));
    }

    public static function guestUrl(?string $slug = null, bool $asContractor = false): ?string
    {
        return self::samples()->isEmpty() ? null : route('demo.tamu', array_filter(['owner' => $slug, 'sebagai' => $asContractor ? 'kontraktor' : null]));
    }

    /** @return Collection<int, Owner> owner contoh yang dapat dibuka akun tamu, sesuai urutan STORIES */
    public static function samples(): Collection
    {
        if (! config('rekana.demo.guest') || ! $user = self::guestUser()) {
            return collect();
        }

        $order = array_keys(self::STORIES);

        return $user->getTenants(Filament::getPanel('app'))->where('is_demo', true)
            ->sortBy(fn (Owner $o) => ($i = array_search($o->slug, $order, true)) === false ? 99 : $i)->values();
    }

    public static function story(Owner $owner): array
    {
        return self::STORIES[$owner->slug] ?? [$owner->name, $owner->industry, '', null];
    }

    public static function featuredContractor(Owner $owner): ?Contractor
    {
        $user = self::guestUser();
        if (! $user) {
            return null;
        }

        return $user->contractors()->whereHas('registrations', fn ($q) => $q->where('owner_id', $owner->id))->first();
    }

    protected static function guestUser(): ?User
    {
        return User::where('email', User::DEMO_GUEST_EMAIL)->where('is_active', true)->first();
    }
}
