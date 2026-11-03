<?php

namespace Tests\Feature;

use App\Models\Contractor;
use App\Models\Owner;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Perintah operasional: muat dan hapus data contoh, buat owner baru, pengingat. */
class CommandsTest extends TestCase
{
    use RefreshDatabase;

    public function test_demo_lifecycle_and_new_owner(): void
    {
        $this->artisan('rekana:demo', ['perusahaan' => 'garudafood-gresik', '--force' => true])->assertSuccessful();
        $this->assertTrue(Owner::where('slug', 'garudafood-gresik')->exists());
        $this->artisan('rekana:reminders')->assertSuccessful();

        $this->artisan('rekana:remove-demo', ['--force' => true])->assertSuccessful();
        $this->assertFalse(Owner::exists());
        $this->assertSame(0, Contractor::count());
        $this->assertFalse(User::where('email', 'like', '%garudafood.rekana.test')->exists());

        $this->artisan('rekana:owner', ['slug' => 'ajinomoto-mojokerto', '--name' => 'Ajinomoto Mojokerto', '--email' => 'hse@example.com', '--password' => 'rahasia-panjang-1'])->assertSuccessful();
        $user = User::where('email', 'hse@example.com')->firstOrFail();
        $this->actingAs($user)->get('/app/ajinomoto-mojokerto')->assertOk();
    }
}
