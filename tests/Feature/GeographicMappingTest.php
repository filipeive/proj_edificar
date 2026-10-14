<?php

namespace Tests\Feature;

use App\Models\Cell;
use App\Models\Supervision;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GeographicMappingTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function zones_store_boundaries_and_cells_store_map_coordinates()
    {
        $boundary = [
            'type' => 'Polygon',
            'coordinates' => [[
                [36.88, -17.87],
                [36.90, -17.87],
                [36.90, -17.89],
                [36.88, -17.87],
            ]],
        ];

        $zone = Zone::create([
            'name' => 'Zona geográfica de teste',
            'latitude' => -17.88,
            'longitude' => 36.89,
            'boundary' => $boundary,
        ]);

        $supervision = Supervision::create([
            'name' => 'Supervisão de teste',
            'zone_id' => $zone->id,
        ]);

        $cell = Cell::create([
            'name' => 'Célula geográfica de teste',
            'type' => 'membros',
            'supervision_id' => $supervision->id,
            'latitude' => -17.885,
            'longitude' => 36.895,
        ]);

        $this->assertSame($boundary, $zone->fresh()->boundary);
        $this->assertSame('-17.8800000', $zone->fresh()->latitude);
        $this->assertSame('36.8900000', $zone->fresh()->longitude);
        $this->assertSame('-17.8850000', $cell->fresh()->latitude);
        $this->assertSame('36.8950000', $cell->fresh()->longitude);
    }

    /** @test */
    public function admin_dashboard_renders_configured_zone_and_cell_map_data()
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));

        $zone = Zone::create([
            'name' => 'Zona Centro do Mapa',
            'latitude' => -17.88,
            'longitude' => 36.89,
            'boundary' => [
                'type' => 'Polygon',
                'coordinates' => [[[36.88, -17.87], [36.90, -17.87], [36.90, -17.89], [36.88, -17.87]]],
            ],
        ]);
        $supervision = Supervision::create(['name' => 'Supervisão Central', 'zone_id' => $zone->id]);
        Cell::create([
            'name' => 'Célula da Praça',
            'type' => 'membros',
            'supervision_id' => $supervision->id,
            'latitude' => -17.885,
            'longitude' => 36.895,
        ]);

        $response = $this->get(route('dashboard.admin'));

        $response->assertOk()
            ->assertSee('church-map')
            ->assertSee('Zona Centro do Mapa')
            ->assertSee('C\\u00e9lula da Pra\\u00e7a', false);
    }

    /** @test */
    public function zone_edit_form_renders_polygon_editor_with_saved_boundary()
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        $boundary = [
            'type' => 'Polygon',
            'coordinates' => [[[36.88, -17.87], [36.90, -17.87], [36.90, -17.89], [36.88, -17.87]]],
        ];
        $zone = Zone::create([
            'name' => 'Zona Editável',
            'latitude' => -17.88,
            'longitude' => 36.89,
            'boundary' => $boundary,
        ]);

        $this->get(route('zones.edit', $zone))
            ->assertOk()
            ->assertSee('data-location-editor', false)
            ->assertSee('data-mode="area"', false)
            ->assertSee('várias células')
            ->assertSee('Polygon', false);
    }
}
