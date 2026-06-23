<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\County;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_registration_persists_county_and_coordinates()
    {
        $this->withoutExceptionHandling();

        // Create a county
        $county = County::create(['name' => 'TestCounty']);

        $payload = [
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'member',
            'county_id' => $county->id,
            'location' => 'TestTown, TestCounty',
            'latitude' => -1.2345,
            'longitude' => 36.7890,
            'age' => 30,
            'gender' => 'male',
            'fitness_goal' => 'maintenance',
            'experience_level' => 'beginner',
            'budget_range' => '5,000 - 12,000',
            'diet_preference' => 'balanced',
        ];

        $response = $this->post(route('register.store'), $payload);

        $response->assertRedirect();

        $this->assertDatabaseHas('users', [
            'email' => 'testuser@example.com',
            'county_id' => $county->id,
        ]);

        $user = User::where('email', 'testuser@example.com')->first();
        $this->assertNotNull($user);

        // location should be stored as JSON when lat/lng provided
        $this->assertNotNull($user->location);
        $loc = json_decode($user->location, true);
        $this->assertEqualsWithDelta(-1.2345, $loc['latitude'], 0.0001);
        $this->assertEqualsWithDelta(36.7890, $loc['longitude'], 0.0001);
    }
}
