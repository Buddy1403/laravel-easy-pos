<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            ['key' => 'currency_symbol', 'value' => '₱'],
            ['key' => 'site_description', 'value' => 'Name of canteen or business'],
            ['key' => 'site_email', 'value' => 'admin@example.com'],
            ['key' => 'site_name', 'value' => 'SMX - Philippine Breeders Festival'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(['key' => $setting['key']], ['value' => $setting['value']]);
        }
    }
}
