<?php

namespace Tests\Feature;

use App\Services\OpenAIService;
use App\Services\LocationService;
use App\Models\TrainerProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class TrainerRecommendationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Mock OpenAI responses
        Http::fake([
            'api.openai.com/*' => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'content' => json_encode([
                                ['id' => 2, 'reason' => 'Best match for your goals'],
                                ['id' => 1, 'reason' => 'Good alternative'],
                            ]),
                        ],
                    ],
                ],
            ]),
        ]);
    }

    public function test_openai_service_is_available()
    {
        $service = app(OpenAIService::class);
        $this->assertNotNull($service);
    }

    public function test_location_service_is_available()
    {
        $service = app(LocationService::class);
        $this->assertNotNull($service);
    }

    public function test_recommendation_endpoint_accepts_request()
    {
        $trainer1 = User::factory()->create(['role' => 'trainer']);
        $trainer2 = User::factory()->create(['role' => 'trainer']);

        TrainerProfile::create([
            'user_id' => $trainer1->id,
            'specialty' => 'Strength Training',
            'category' => 'Personal Training',
            'experience_years' => 5,
            'latitude' => -1.2921,
            'longitude' => 36.8219,
        ]);

        TrainerProfile::create([
            'user_id' => $trainer2->id,
            'specialty' => 'Cardio',
            'category' => 'Group Classes',
            'experience_years' => 3,
            'latitude' => -1.2845,
            'longitude' => 36.8314,
        ]);

        $member = User::factory()->create(['role' => 'member']);

        $response = $this->actingAs($member)->postJson(route('client.recommend.trainers'), [
            'goal' => 'Lose weight',
            'town' => 'Nairobi',
            'lat' => -1.2921,
            'lng' => 36.8219,
            'radius_km' => 10,
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['ok', 'trainers']);
    }

    public function test_openai_config_is_registered()
    {
        $this->assertNotNull(config('services.openai'));
        $this->assertTrue(isset(config('services.openai')['key']));
        $this->assertTrue(isset(config('services.openai')['model']));
    }

    public function test_google_maps_config_is_registered()
    {
        $this->assertNotNull(config('services.google'));
        $this->assertTrue(isset(config('services.google')['maps_api_key']));
    }
}
