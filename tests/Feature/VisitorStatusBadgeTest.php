<?php

namespace Tests\Feature;

use App\Models\Visitor;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

class VisitorStatusBadgeTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function visitor_status_badge_component_renders_expected_variants()
    {
        $statuses = [
            'pendente' => 'Pendente',
            'contatado' => 'Contatado',
            'integrado' => 'Integrado',
            'sem_interesse' => 'Sem Interesse',
        ];

        foreach ($statuses as $status => $label) {
            $rendered = Blade::render('<x-visitor-status-badge :status="$status" />', ['status' => $status]);
            $this->assertStringContainsString($label, $rendered);
        }
    }

    /** @test */
    public function visitor_model_accessor_delegates_to_badge_component()
    {
        $visitor = new Visitor(['contact_status' => 'contatado']);
        $badge = $visitor->status_badge;

        $this->assertStringContainsString('Contatado', $badge);
    }
}
