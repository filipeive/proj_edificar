<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // General
            ['key' => 'church.name', 'value' => 'Life Church', 'type' => 'string', 'group' => 'general', 'is_public' => true],
            ['key' => 'church.congregation', 'value' => 'Congregação de Chimoio', 'type' => 'string', 'group' => 'general', 'is_public' => true],
            ['key' => 'church.description', 'value' => 'Uma igreja comprometida com o crescimento espiritual', 'type' => 'string', 'group' => 'general', 'is_public' => true],
            ['key' => 'church.address', 'value' => '', 'type' => 'string', 'group' => 'general', 'is_public' => true],
            ['key' => 'church.phone', 'value' => '', 'type' => 'string', 'group' => 'general', 'is_public' => true],
            ['key' => 'church.email', 'value' => 'contato@lifechurch.com', 'type' => 'string', 'group' => 'general', 'is_public' => true],
            ['key' => 'church.website', 'value' => '', 'type' => 'string', 'group' => 'general', 'is_public' => true],

            // Branding
            ['key' => 'branding.logo_primary', 'value' => '/images/logo-white-orange.png', 'type' => 'file', 'group' => 'branding', 'is_public' => true],
            ['key' => 'branding.logo_secondary', 'value' => '', 'type' => 'file', 'group' => 'branding', 'is_public' => true],
            ['key' => 'branding.favicon', 'value' => '/favicon.ico', 'type' => 'file', 'group' => 'branding', 'is_public' => true],
            ['key' => 'branding.color_primary', 'value' => '#3B82F6', 'type' => 'string', 'group' => 'branding', 'is_public' => true],
            ['key' => 'branding.color_secondary', 'value' => '#F97316', 'type' => 'string', 'group' => 'branding', 'is_public' => true],
            ['key' => 'branding.color_accent', 'value' => '#8B5CF6', 'type' => 'string', 'group' => 'branding', 'is_public' => true],

            // Regional
            ['key' => 'regional.currency', 'value' => 'MZN', 'type' => 'string', 'group' => 'regional', 'is_public' => false],
            ['key' => 'regional.currency_symbol', 'value' => 'MT', 'type' => 'string', 'group' => 'regional', 'is_public' => false],
            ['key' => 'regional.timezone', 'value' => 'Africa/Maputo', 'type' => 'string', 'group' => 'regional', 'is_public' => false],
            ['key' => 'regional.date_format', 'value' => 'd/m/Y', 'type' => 'string', 'group' => 'regional', 'is_public' => false],
            ['key' => 'regional.time_format', 'value' => 'H:i', 'type' => 'string', 'group' => 'regional', 'is_public' => false],

            // Email
            ['key' => 'email.from_name', 'value' => 'Life Church', 'type' => 'string', 'group' => 'email', 'is_public' => false],
            ['key' => 'email.from_address', 'value' => 'noreply@lifechurch.com', 'type' => 'string', 'group' => 'email', 'is_public' => false],

            // System
            ['key' => 'system.setup_completed', 'value' => 'false', 'type' => 'boolean', 'group' => 'system', 'is_public' => false],
            ['key' => 'system.maintenance_mode', 'value' => 'false', 'type' => 'boolean', 'group' => 'system', 'is_public' => false],
            ['key' => 'system.version', 'value' => '1.0.0', 'type' => 'string', 'group' => 'system', 'is_public' => true],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
