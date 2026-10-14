<?php

namespace App\Console\Commands;

use App\Models\Congregation;
use App\Models\User;
use App\Services\CongregationContext;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreateCongregation extends Command
{
    protected $signature = 'congregation:create
        {name? : Nome oficial da congregação}
        {slug? : Identificador URL único}
        {--domain= : Domínio completo, por exemplo igreja.exemplo.org}
        {--subdomain= : Subdomínio usado para resolver a congregação}
        {--admin-email= : E-mail do novo administrador da congregação}';

    protected $description = 'Cria uma congregação isolada e o seu primeiro administrador';

    public function handle(): int
    {
        $name = $this->argument('name') ?: $this->ask('Nome da congregação');
        $slug = $this->argument('slug') ?: Str::slug($name);
        $email = $this->option('admin-email') ?: $this->ask('E-mail do administrador');

        if (Congregation::where('slug', $slug)->exists()) {
            $this->components->error("Já existe uma congregação com o identificador {$slug}.");
            return self::FAILURE;
        }

        if (User::withoutGlobalScope('congregation')->where('email', $email)->exists()) {
            $this->components->error('O e-mail já está atribuído a outra conta. Os e-mails são globalmente únicos.');
            return self::FAILURE;
        }

        $password = $this->secret('Palavra-passe inicial do administrador');
        $confirmation = $this->secret('Confirme a palavra-passe');
        $adminName = $this->ask('Nome do administrador');
        if (!$password || $password !== $confirmation) {
            $this->components->error('As palavras-passe não coincidem ou estão vazias.');
            return self::FAILURE;
        }

        $congregation = DB::transaction(function () use ($name, $slug, $email, $password, $adminName) {
            $congregation = Congregation::create([
                'name' => $name,
                'slug' => $slug,
                'domain' => $this->option('domain'),
                'subdomain' => $this->option('subdomain'),
                'is_active' => true,
                'is_primary' => false,
            ]);

            app(CongregationContext::class)->set($congregation);
            User::create([
                'name' => $adminName,
                'email' => $email,
                'password' => Hash::make($password),
                'role' => 'admin',
                'is_active' => true,
            ]);

            return $congregation;
        });

        $this->components->info("Congregação {$name} criada. Identificador: {$slug}");
        $this->line('Configure DNS/virtual host para o domínio ou subdomínio antes de permitir o acesso.');

        return self::SUCCESS;
    }
}