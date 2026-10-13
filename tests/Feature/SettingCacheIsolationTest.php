<?php

namespace Tests\Feature;

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class SettingCacheIsolationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function clearing_settings_cache_does_not_flush_the_entire_application_cache()
    {
        Cache::put('app:global', 'keep-me');

        Setting::create([
            'key' => 'theme',
            'value' => 'dark',
            'type' => 'string',
            'group' => 'general',
            'is_public' => true,
        ]);

        Setting::get('theme');

        Setting::clearCache();

        $this->assertSame('keep-me', Cache::get('app:global'));
        $this->assertNull(Cache::get('setting.theme'));
    }
}
