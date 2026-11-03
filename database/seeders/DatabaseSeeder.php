<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /** Template baku, administrator platform dari .env, dan data contoh bila REKANA_DEMO=true. */
    public function run(): void
    {
        $this->call(TemplateSeeder::class);

        if ($email = config('rekana.admin.email')) {
            User::query()->updateOrCreate(['email' => $email], [
                'name' => 'Administrator Rekana',
                'password' => config('rekana.admin.password') ?: str()->password(16),
                'is_platform_admin' => true,
                'is_active' => true,
            ]);
            $this->command?->info("Administrator platform: {$email}");
        }

        if (config('rekana.demo.enabled')) {
            $this->call(DemoSeeder::class);
        }
    }
}
