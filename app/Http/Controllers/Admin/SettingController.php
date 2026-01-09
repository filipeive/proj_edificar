<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Display settings by group
     */
    public function index(Request $request)
    {
        $group = $request->get('group', 'general');

        $settings = Setting::where('group', $group)->get()->mapWithKeys(function ($setting) {
            return [
                $setting->key => [
                    'value' => $setting->value,
                    'type' => $setting->type,
                ]
            ];
        });

        $groups = Setting::select('group')->distinct()->pluck('group');

        return view('admin.settings.index', compact('settings', 'groups', 'group'));
    }

    /**
     * Update multiple settings
     */
    public function update(Request $request)
    {
        $settings = $request->input('settings', []);

        foreach ($settings as $key => $value) {
            $existing = Setting::where('key', $key)->first();

            if ($existing) {
                Setting::set($key, $value, $existing->type, $existing->group);
            }
        }

        Setting::clearCache();

        return back()->with('success', 'Configurações atualizadas com sucesso!');
    }

    /**
     * Upload logo or branding file
     */
    public function uploadLogo(Request $request)
    {
        $request->validate([
            'logo' => 'required|image|mimes:jpeg,png,jpg,svg|max:2048',
            'type' => 'required|in:primary,secondary,favicon'
        ]);

        $file = $request->file('logo');
        $filename = 'logo_' . $request->type . '_' . time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('public/branding', $filename);

        $publicPath = '/storage/branding/' . $filename;

        $settingKey = match ($request->type) {
            'primary' => 'branding.logo_primary',
            'secondary' => 'branding.logo_secondary',
            'favicon' => 'branding.favicon',
        };

        Setting::set($settingKey, $publicPath, 'file', 'branding');
        Setting::clearCache();

        return back()->with('success', 'Logo atualizado com sucesso!');
    }

    /**
     * Reset settings to defaults
     */
    public function resetToDefaults()
    {
        \Artisan::call('db:seed', ['--class' => 'SettingSeeder']);
        Setting::clearCache();

        return back()->with('success', 'Configurações restauradas para os padrões!');
    }
}
