<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Course;

class EnrollmentConditionalFieldsTest extends TestCase
{
    /** @test */
    public function the_pre_marital_form_renders_with_alpine_directives()
    {
        $course = Course::firstOrCreate(['slug' => 'casais'], [
            'name' => 'Curso de Casais',
            'description' => 'Curso Pré-Marital',
            'is_active' => true,
            'registration_open' => true,
        ]);

        $response = $this->get('/inscricao-pre-marital');

        $response->assertStatus(200);
        $response->assertSee('name="couple_name"', false);
        $response->assertSee('id="relationship_type"', false);
        $response->assertSee('name="is_church_member"', false);
        $response->assertSee('id="zone_id"', false);
    }
}
