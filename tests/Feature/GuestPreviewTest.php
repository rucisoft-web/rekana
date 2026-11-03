<?php

namespace Tests\Feature;

use App\Filament\App\Resources\Areas\Pages\ManageAreas;
use App\Models\Area;
use App\Models\Owner;
use App\Models\User;
use App\Models\WorkPermit;
use App\Support\CurrentOwner;
use Database\Seeders\GarudafoodDemoSeeder;
use Filament\Facades\Filament;
use Filament\Support\Exceptions\Halt;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/** Tamu demo dapat membuka semua formulir dan tombol owner maupun kontraktor, tetapi tidak ada data yang tersimpan. */
class GuestPreviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_sees_every_form_but_cannot_save(): void
    {
        $this->seed(GarudafoodDemoSeeder::class);
        $owner = Owner::where('slug', 'garudafood-gresik')->firstOrFail();
        $guest = User::where('email', User::DEMO_GUEST_EMAIL)->firstOrFail();

        // Tombol tamu owner dan kontraktor di halaman login dan beranda.
        $this->get('/app/login')->assertOk()->assertSee('Tamu sebagai Owner')->assertSee('Tamu sebagai Kontraktor');
        $this->get('/kontraktor/login')->assertOk()->assertSee('Tamu sebagai Kontraktor');
        $this->get('/')->assertOk()->assertSee('Tamu sebagai Owner')->assertSee('Tamu sebagai Kontraktor');

        // Tombol pembuat data tampil untuk tamu di kedua panel.
        $this->get('/demo/tamu?owner=garudafood-gresik&sebagai=owner')->assertRedirect('/app/garudafood-gresik');
        $this->actingAs($guest)->get('/app/garudafood-gresik/izin-kerja')->assertOk()->assertSee('Buat Izin kerja');
        $contractor = $guest->contractors()->firstOrFail();
        $this->actingAs($guest)->get('/kontraktor/'.$contractor->slug.'/pekerja')->assertOk()->assertSee('Buat Pekerja');

        // Penyimpanan lewat formulir dihentikan tanpa galat.
        Filament::setCurrentPanel(Filament::getPanel('app'));
        Filament::setTenant($owner);
        app(CurrentOwner::class)->set($owner);
        $before = Area::count();
        Livewire::actingAs($guest)->test(ManageAreas::class)
            ->callAction('create', ['code' => 'TMU', 'name' => 'Area uji tamu', 'hazard_class' => 'umum'])
            ->assertNotified('Mode tamu: perubahan tidak disimpan');
        $this->assertSame($before, Area::count());

        // Penyimpanan langsung lewat model juga dihentikan.
        $permit = WorkPermit::firstOrFail();
        $this->expectException(Halt::class);
        $permit->delete();
    }
}
