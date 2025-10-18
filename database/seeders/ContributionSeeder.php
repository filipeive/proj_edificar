<?php
namespace Database\Seeders;

use App\Models\Cell;
use App\Models\Contribution;
use App\Models\User;
use Illuminate\Database\Seeder;

class ContributionSeeder extends Seeder {
    public function run(): void {
        $members = User::where('role', '!=', 'admin')->get();
        $now = now();

        // Período: 20 do mês anterior até 5 do mês atual
        $periodStart = $now->copy()->startOfMonth()->addDays(19);
        $periodEnd = $now->copy()->addDays(4);

        foreach ($members as $user) {
            // 80% de chance de contribuir
            if (rand(1, 100) > 80) continue;

            $cell = $user->cell;
            if (!$cell) continue;

            // Cada membro faz 1-2 contribuições no período
            $numContributions = rand(1, 2);

            for ($i = 0; $i < $numContributions; $i++) {
                // Gerar data aleatória no período
                $days = $periodEnd->diffInDays($periodStart);
                $randomDay = rand(0, $days);
                $contributionDate = $periodStart->copy()->addDays($randomDay);

                // Gerar valor aleatório dentro do pacote do usuário
                $commitment = $user->getActiveCommitment();
                if (!$commitment) continue;

                $package = $commitment->package;
                $minAmount = $package->min_amount;
                $maxAmount = $package->max_amount ?? $package->min_amount * 10;

                $amount = rand($minAmount * 100, $maxAmount * 100) / 100;

                // 70% de chance de já estar verificada
                $status = rand(1, 100) <= 70 ? 'verificada' : 'pendente';

                Contribution::create([
                    'user_id' => $user->id,
                    'cell_id' => $cell->id,
                    'supervision_id' => $cell->supervision_id,
                    'zone_id' => $cell->supervision->zone_id,
                    'amount' => $amount,
                    'contribution_date' => $contributionDate,
                    'status' => $status,
                    'registered_by_id' => $cell->leader_id,
                    'verified_by_id' => $status === 'verificada' ? 1 : null,
                    'notes' => $status === 'verificada' ? 'Verificado' : null,
                ]);
            }
        }
    }
}
