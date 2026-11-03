<?php

namespace Tests\Feature;

use App\Models\AccessCard;
use App\Models\Contract;
use App\Models\CsmsCertificate;
use App\Models\Owner;
use App\Models\Prequalification;
use App\Models\Tender;
use App\Models\User;
use Database\Seeders\GarudafoodDemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Semua halaman owner dan portal kontraktor terbuka tanpa galat untuk admin, tamu, dan rekanan. */
class PageSmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_pages_render(): void
    {
        $this->seed(GarudafoodDemoSeeder::class);
        $owner = Owner::where('slug', 'garudafood-gresik')->firstOrFail();
        $admin = User::where('email', 'admin@garudafood.rekana.test')->firstOrFail();
        $base = '/app/'.$owner->slug;

        $paths = ['', '/area', '/pengguna', '/template-pq', '/log-audit', '/rekanan', '/prakualifikasi',
            '/sertifikat', '/rencana-pekerjaan', '/seleksi', '/kontrak', '/induksi', '/kartu-akses', '/izin-kerja'];

        foreach ([$admin, User::where('email', User::DEMO_GUEST_EMAIL)->first()] as $user) {
            $this->flushSession();
            foreach ($paths as $path) {
                $this->actingAs($user)->get($base.$path)->assertOk();
            }
            $pq = Prequalification::withoutGlobalScopes()->where('owner_id', $owner->id)->firstOrFail();
            $this->actingAs($user)->get($base.'/prakualifikasi/'.$pq->id)->assertOk();
            $tender = Tender::withoutGlobalScopes()->where('owner_id', $owner->id)->firstOrFail();
            $this->actingAs($user)->get($base.'/seleksi/'.$tender->id)->assertOk();
        }

        $this->actingAs($admin)->get(route('cetak.sertifikat', CsmsCertificate::withoutGlobalScopes()->first()))->assertOk()->assertSee('DATA SIMULASI');
        $this->actingAs($admin)->get(route('cetak.kartu-kontrak', Contract::withoutGlobalScopes()->first()))->assertOk();
        $card = AccessCard::withoutGlobalScopes()->first();
        $this->get(route('verifikasi.kartu', $card->code))->assertOk()->assertSee($card->worker->name);

        // Portal kontraktor.
        $this->flushSession();
        $contractorAdmin = User::where('email', 'admin@mesin.garudafood.rekana.test')->firstOrFail();
        $slug = $contractorAdmin->contractors()->first()->slug;
        foreach (['', '/dokumen', '/statistik-k3', '/pekerja', '/prakualifikasi', '/undangan-seleksi', '/kontrak', '/izin-kerja'] as $path) {
            $this->actingAs($contractorAdmin)->get('/kontraktor/'.$slug.$path)->assertOk();
        }

        // Rekanan tidak dapat membuka panel owner.
        $this->assertContains($this->actingAs($contractorAdmin)->get($base)->status(), [403, 404]);
    }

    public function test_landing_and_guest_login(): void
    {
        $this->seed(GarudafoodDemoSeeder::class);
        $this->get('/')->assertOk()->assertSee('DATA SIMULASI')->assertSee('Garudafood');
        $this->get('/demo/tamu?owner=garudafood-gresik')->assertRedirect('/app/garudafood-gresik');
        $this->get('/demo/tamu?owner=garudafood-gresik&sebagai=kontraktor')->assertRedirectContains('/kontraktor/');
    }
}
