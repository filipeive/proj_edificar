<?php

namespace App\Console\Commands;

use App\Models\EventType;
use App\Models\QuarterlyReport;
use App\Models\QuarterlyReportEvent;
use App\Models\Supervision;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportQuarterlyReportsBulk extends Command
{
    protected $signature = 'reports:import-bulk {file} {--dry-run}';
    protected $description = 'Importa relatórios trimestrais do IV Trimestre de 2025 a partir de um Excel';

    public function handle()
    {
        $filePath = $this->argument('file');
        $dryRun = $this->option('dry-run');

        if (!file_exists($filePath)) {
            $this->error("Arquivo não encontrado: $filePath");
            return 1;
        }

        $this->info("Lendo arquivo: $filePath" . ($dryRun ? " [DRY RUN]" : ""));

        try {
            $spreadsheet = IOFactory::load($filePath);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray(null, true, true, true);
        } catch (\Exception $e) {
            $this->error("Erro ao ler Excel: " . $e->getMessage());
            return 1;
        }

        // Skip header row (index 1)
        $dataRows = array_slice($rows, 1);
        $total = count($dataRows);
        $count = 0;

        foreach ($dataRows as $index => $row) {
            $count++;
            $this->comment("Processando ($count/$total): " . ($row['B'] ?? 'Desconhecido'));

            if (empty($row['B']))
                continue;

            DB::beginTransaction();
            try {
                // 1. Zona
                $zoneName = trim($row['C']);
                $area = trim($row['D'] ?? '');
                if (stripos($area, 'MIA') !== false && stripos($zoneName, 'MIA') === false) {
                    $zoneName .= ' - MIA';
                }

                $zone = Zone::where('name', 'LIKE', $zoneName)->first();
                if (!$zone) {
                    $this->warn("  → Criando Zona: $zoneName");
                    $zone = Zone::create(['name' => $zoneName, 'is_active' => true]);
                }

                // 2. Supervisor / User
                $supervisorName = trim($row['B']);
                $supervisor = User::where('name', 'LIKE', $supervisorName)->first();
                if (!$supervisor) {
                    $this->warn("  → Criando Supervisor: $supervisorName");
                    $supervisor = User::create([
                        'name' => $supervisorName,
                        'email' => strtolower(str_replace(' ', '.', $supervisorName)) . '@placeholder.com',
                        'password' => bcrypt('password'), // Eles devem redefinir
                        'role' => 'supervisor',
                        'is_active' => true
                    ]);
                }

                // 3. Supervision (Check if exists for this zone/supervisor or create)
                $supervision = Supervision::where('zone_id', $zone->id)
                    ->where('supervisor_id', $supervisor->id)
                    ->first();
                if (!$supervision) {
                    $supervision = Supervision::create([
                        'name' => "Supervisão " . explode(' ', $supervisorName)[0],
                        'zone_id' => $zone->id,
                        'supervisor_id' => $supervisor->id,
                        'is_active' => true
                    ]);
                }

                // 4. Quarterly Report
                $reportData = [
                    'zone_id' => $zone->id,
                    'supervision_id' => $supervision->id,
                    'supervisor_id' => $supervisor->id,
                    'zone_pastor_id' => $zone->pastor_id,
                    'year' => 2025,
                    'quarter' => 4,
                    'leaders_count' => (int) ($row['E'] ?? 0),
                    'cells_count' => (int) ($row['F'] ?? 0),
                    'timoteos_count' => (int) ($row['G'] ?? 0),
                    'members_count' => (int) ($row['H'] ?? 0),
                    'participants_count' => (int) ($row['I'] ?? 0),
                    'saved_count' => (int) ($row['J'] ?? 0),
                    'planned_baptism_count' => (int) ($row['K'] ?? 0),
                    'baptized_count' => (int) ($row['L'] ?? 0),
                    'cell_multiplications_count' => (int) ($row['M'] ?? 0),
                    'disciplined_leaders_count' => (int) ($row['N'] ?? 0),
                    'closed_cells_count' => (int) ($row['O'] ?? 0),
                    'ministerial_observations' => trim(($row['P'] ?? '') . ' Area: ' . ($row['D'] ?? '') . '. ' . ($row['W'] ?? '')),
                    'evangelism_strategy' => (int) ($row['X'] ?? 0),
                    'consolidation_growth' => (int) ($row['Y'] ?? 0), // Mapping to Discipulado score
                    'pastoral_score' => (int) ($row['Z'] ?? 0),
                    'cell_participation_score' => (int) ($row['AA'] ?? 0),
                    'service_participation_score' => (int) ($row['AB'] ?? 0),
                    'communion_in_cells_score' => (int) ($row['AC'] ?? 0),
                    'relationship_building_score' => (int) ($row['AD'] ?? 0),
                    'prayer_intercession_score' => (int) ($row['AE'] ?? 0),
                    'status' => 'submitted',
                    'submitted_at' => now(),
                ];

                if (!$dryRun) {
                    $report = QuarterlyReport::updateOrCreate(
                        [
                            'zone_id' => $zone->id,
                            'supervision_id' => $supervision->id,
                            'supervisor_id' => $supervisor->id,
                            'year' => 2025,
                            'quarter' => 4,
                        ],
                        $reportData
                    );

                    // 5. Events
                    $eventMapping = [
                        'Q' => ['name' => 'Treinamento', 'code' => 'training', 'category' => 'training'],
                        'R' => ['name' => 'Confraternização', 'code' => 'fellowship', 'category' => 'fellowship'],
                        'S' => ['name' => 'Ação Comunitária', 'code' => 'community_service', 'category' => 'service'],
                        'T' => ['name' => 'Funeral', 'code' => 'funeral', 'category' => 'ceremony'],
                        'U' => ['name' => 'Casamento', 'code' => 'wedding', 'category' => 'ceremony'],
                        'V' => ['name' => 'Dedicação de Bebé', 'code' => 'baby_dedication', 'category' => 'ceremony'],
                    ];

                    foreach ($eventMapping as $col => $typeInfo) {
                        $countVal = (int) ($row[$col] ?? 0);
                        if ($countVal > 0) {
                            $eventType = EventType::firstOrCreate(
                                ['code' => $typeInfo['code']],
                                [
                                    'name' => $typeInfo['name'],
                                    'category' => $typeInfo['category'],
                                    'is_active' => true
                                ]
                            );

                            QuarterlyReportEvent::updateOrCreate(
                                [
                                    'quarterly_report_id' => $report->id,
                                    'event_type_id' => $eventType->id,
                                ],
                                ['count' => $countVal]
                            );
                        }
                    }
                }

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                $this->error("  × Erro na linha $count: " . $e->getMessage());
            }
        }

        $this->info("Importação concluída!");
        return 0;
    }
}
