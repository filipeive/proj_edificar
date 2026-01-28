<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;

class SettingController extends Controller
{
    /**
     * Display settings by group
     */
    public function index(Request $request)
    {
        $group = $request->get('group', 'general');

        $settings = Setting::all()->mapWithKeys(function ($setting) {
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
        if (empty($settings)) {
            return back()->with('error', 'Nenhuma configuração recebida. Verifique o formulário.');
        }

        $updatedCount = 0;
        foreach ($settings as $key => $value) {
            $existing = Setting::where('key', $key)->first();

            if ($existing) {
                Setting::set($key, $value, $existing->type, $existing->group);
                $updatedCount++;
            }
        }

        // Note: maintenance_mode setting is saved but does NOT run artisan down/up
        // to prevent admin lockout. Use CLI for actual maintenance mode.

        Setting::clearCache();

        return redirect()
            ->route('settings.index', ['group' => $request->input('active_tab', 'general')])
            ->with('success', 'Configurações atualizadas com sucesso!');
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

    /**
     * Export database backup
     */
    public function backup()
    {
        $driver = config('database.default');
        $timestamp = now()->format('Y_m_d_His');

        if ($driver === 'sqlite') {
            $path = config('database.connections.sqlite.database');
            if (!file_exists($path)) {
                return back()->with('error', 'Base de dados não encontrada.');
            }
            $filename = "backup_{$timestamp}.sqlite";
            return Response::download($path, $filename);
        }

        if ($driver !== 'mysql') {
            return back()->with('error', 'Backup automático disponível apenas para MySQL/SQLite.');
        }

        $host = config('database.connections.mysql.host');
        $port = config('database.connections.mysql.port');
        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');

        $backupDir = storage_path('app/backups');
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        $filename = "backup_{$database}_{$timestamp}.sql";
        $fullPath = $backupDir . DIRECTORY_SEPARATOR . $filename;

        $process = new Process([
            'mysqldump',
            '--single-transaction',
            '--quick',
            '--routines',
            '--triggers',
            '--host=' . $host,
            '--port=' . $port,
            '--user=' . $username,
            $database,
        ]);
        $process->setEnv(['MYSQL_PWD' => $password]);
        $process->run();

        if (!$process->isSuccessful()) {
            return back()->with('error', 'Não foi possível gerar o backup. Verifique se o mysqldump está instalado.');
        }

        file_put_contents($fullPath, $process->getOutput());

        return response()->download($fullPath)->deleteFileAfterSend(true);
    }
}
