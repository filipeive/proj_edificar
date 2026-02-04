<?php

namespace App\Http\Controllers;

use App\Models\Concerns\NormalizesMozPhone;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Artisan;

class SetupController extends Controller
{
    use NormalizesMozPhone;
    /**
     * Check if setup is already completed
     */
    public function index()
    {
        if (Setting::get('system.setup_completed', false)) {
            return redirect()->route('login')->with('info', 'O sistema já foi configurado.');
        }

        return view('setup.welcome');
    }

    /**
     * Process Step 1: Church Information
     */
    public function step1(Request $request)
    {
        $validated = $request->validate([
            'church_name' => 'required|string|max:255',
            'church_description' => 'nullable|string',
            'church_email' => 'required|email',
            'church_phone' => 'nullable|moz_phone',
            'church_address' => 'nullable|string',
        ]);

        Setting::set('church.name', $validated['church_name'], 'string', 'general');
        Setting::set('church.description', $validated['church_description'] ?? '', 'string', 'general');
        Setting::set('church.email', $validated['church_email'], 'string', 'general');
        Setting::set('church.phone', $this->normalizeMozPhone($validated['church_phone'] ?? '') ?? '', 'string', 'general');
        Setting::set('church.address', $validated['church_address'] ?? '', 'string', 'general');

        return response()->json(['success' => true, 'next_step' => 2]);
    }

    /**
     * Process Step 2: Create First Admin User
     */
    public function step2(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'admin',
            'is_active' => true,
        ]);

        return response()->json(['success' => true, 'next_step' => 3]);
    }

    /**
     * Process Step 3: Branding & Theme
     */
    public function step3(Request $request)
    {
        $validated = $request->validate([
            'color_primary' => 'nullable|string|max:7',
            'color_secondary' => 'nullable|string|max:7',
            'color_accent' => 'nullable|string|max:7',
        ]);

        if (!empty($validated['color_primary'])) {
            Setting::set('branding.color_primary', $validated['color_primary'], 'string', 'branding');
        }
        if (!empty($validated['color_secondary'])) {
            Setting::set('branding.color_secondary', $validated['color_secondary'], 'string', 'branding');
        }
        if (!empty($validated['color_accent'])) {
            Setting::set('branding.color_accent', $validated['color_accent'], 'string', 'branding');
        }

        return response()->json(['success' => true, 'next_step' => 4]);
    }

    /**
     * Complete Setup
     */
    public function complete()
    {
        Setting::set('system.setup_completed', 'true', 'boolean', 'system');
        Setting::clearCache();

        // Run migrations to ensure database is up to date
        Artisan::call('migrate', ['--force' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Configuração concluída com sucesso!',
            'redirect' => route('login')
        ]);
    }

    /**
     * Upload logo
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

        return response()->json([
            'success' => true,
            'path' => $publicPath
        ]);
    }
}
