<?php

namespace App\Http\Controllers;

use App\Models\TrainerProfile;
use App\Services\LocationService;
use App\Services\OpenAIService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class RecommendationController extends Controller
{
    public function recommend(Request $request, LocationService $location, OpenAIService $openai): JsonResponse
    {
        $data = $request->validate([
            'county' => ['nullable', 'string', 'max:120'],
            'town' => ['nullable', 'string', 'max:120'],
            'goal' => ['nullable', 'string', 'max:120'],
            'lat' => ['nullable', 'numeric'],
            'lng' => ['nullable', 'numeric'],
            'radius_km' => ['nullable', 'numeric'],
        ]);

        $radius = $data['radius_km'] ?? 25;

        $query = TrainerProfile::query()->with('user');
        if (! empty($data['county'])) {
            $query->where('county', $data['county']);
        }

        $trainers = $query->get();

        $candidates = [];
        foreach ($trainers as $t) {
            $lat = $t->latitude ?? null;
            $lng = $t->longitude ?? null;
            $dist = null;
            if (! empty($data['lat']) && $lat && $lng) {
                $dist = $location->distanceBetween((float) $data['lat'], (float) $data['lng'], (float) $lat, (float) $lng);
            }

            $candidates[] = array_merge($t->toArray(), ['distance_km' => $dist]);
        }

        // Ask OpenAI to rank trainers if available
        $ranked = $openai->rankTrainers($candidates, ['goal' => $data['goal'] ?? null, 'town' => $data['town'] ?? null]);

        if ($ranked && is_array($ranked)) {
            // ranked expected to be array of objects like [{"id": 10, "reason": "..."}, ...]
            $ordered = [];
            $map = collect($candidates)->keyBy(fn($c) => $c['id']);
            foreach ($ranked as $item) {
                $id = is_array($item) ? ($item['id'] ?? null) : $item;
                if ($id && $map->has($id)) {
                    $ordered[] = array_merge($map->get($id), ['ai_reason' => $item['reason'] ?? null]);
                }
            }

            // append any remaining trainers
            foreach ($candidates as $c) {
                if (! in_array($c['id'], array_column($ordered, 'id'))) {
                    $ordered[] = $c;
                }
            }

            return response()->json(['ok' => true, 'trainers' => $ordered]);
        }

        // fallback: sort by distance then specialization match
        usort($candidates, function ($a, $b) {
            $ad = $a['distance_km'] ?? PHP_FLOAT_MAX;
            $bd = $b['distance_km'] ?? PHP_FLOAT_MAX;
            return $ad <=> $bd;
        });

        return response()->json(['ok' => true, 'trainers' => $candidates]);
    }
}
