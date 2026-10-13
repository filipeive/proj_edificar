<?php

namespace Tests\Feature;

use App\Models\Course;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicCourseRateLimitTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function public_course_registration_is_rate_limited()
    {
        $course = Course::create([
            'name' => 'Curso de Evangelismo',
            'slug' => 'evangelismo',
            'description' => 'Curso público',
            'is_active' => true,
            'registration_open' => true,
        ]);

        for ($i = 0; $i < 10; $i++) {
            $response = $this->post(route('public.courses.store', $course), [
                'name' => 'Maria Silva ' . $i,
                'email' => 'maria' . $i . '@example.com',
                'phone' => '+258841234567',
                'observations' => 'Teste de rate limit',
            ]);

            $response->assertStatus(302);
        }

        $this->assertSame(429, $this->post(route('public.courses.store', $course), [
            'name' => 'Maria Silva 99',
            'email' => 'maria99@example.com',
            'phone' => '+258841234567',
            'observations' => 'Teste de rate limit',
        ])->getStatusCode());
    }
}
