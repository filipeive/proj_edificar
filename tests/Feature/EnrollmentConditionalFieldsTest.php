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
        $course = Course::factory()->create(['slug' => 'pre-marital']);

        $response = $this->get('/inscricao-pre-marital');

        $response->assertStatus(200);
        $response->assertSee('x-data="{ relType:');
        $response->assertSee('x-model="relType"');
        $response->assertSee('name="wife_address"');
        $response->assertSee('x-model="isMember"');
        $response->assertSee('x-show="isMember === \'1\'"');
    }
}
