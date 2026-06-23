<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TrainerProfile;
use App\Services\OpenAIService;
use Illuminate\Http\Request;

class TrainerController extends Controller
{
    public function nearby(Request $request)
    {
        $request->validate([
            'lat' => ['required', 'numeric'],
            'lng' => ['required', 'numeric'],
            'radius_km' => ['nullable', 'numeric'],
            'goal' => ['nullable', 'string'],
        ]);

        $lat = (float) $request->query('lat');
        $lng = (float) $request->query('lng');
        $radius = (float) ($request->query('radius_km', 10));
        $goal = $request->query('goal');

        $haversine = "(6371 * acos(cos(radians($lat)) * cos(radians(latitude)) * cos(radians(longitude) - radians($lng)) + sin(radians($lat)) * sin(radians(latitude))))";

        $query = TrainerProfile::selectRaw("trainer_profiles.*, $haversine AS distance_km");

        if ($goal) {
            $query->where('specialty', 'like', "%{$goal}%")->orWhere('category', 'like', "%{$goal}%");
        }

        $collection = $query->whereRaw("$haversine <= ?", [$radius])
            ->with('user')
            ->get();

        // Prepare AI ranking if available
        $aiRanking = null;
        if ($goal && (config('services.openai.key') || env('OPENAI_API_KEY'))) {
            try {
                $openai = app(OpenAIService::class);
                $candidates = $collection->map(function ($tp) {
                    return [
                        'id' => $tp->user?->id,
                        'name' => $tp->user?->name,
                        'specialty' => $tp->specialty,
                        'rating' => $tp->rating,
                        'experience_years' => $tp->experience_years,
                    ];
                })->toArray();

                $ranked = $openai->rankTrainers($candidates, ['goal' => $goal]);
                if (is_array($ranked)) {
                    // ranked expected to be an ordered array of ids
                    $aiRanking = array_flip($ranked);
                }
            } catch (\Throwable $e) {
                // ignore AI errors and fallback
                $aiRanking = null;
            }
        }

        $trainers = $collection->map(function ($tp) use ($radius, $haversine, $lat, $lng, $aiRanking) {
            $distance = $tp->distance_km ?? 9999;
            $distanceScore = $radius > 0 ? max(0, ($radius - $distance) / $radius) : 0;

            // AI match score: if ranking provided, derive from position; else keyword match
            $aiScore = 0;
            if (is_array($aiRanking) && isset($aiRanking[$tp->user?->id])) {
                // lower index = better, convert to 0..1
                $pos = $aiRanking[$tp->user->id];
                $aiScore = 1 / (1 + $pos);
            } else {
                // fallback: keyword match on specialty/category
                $goal = request()->query('goal', '');
                $aiScore = 0;
                if (! empty($goal)) {
                    $goal = strtolower($goal);
                    $match = 0;
                    if (str_contains(strtolower($tp->specialty ?? ''), $goal)) $match = 1;
                    if (str_contains(strtolower($tp->category ?? ''), $goal)) $match = max($match, 1);
                    $aiScore = $match;
                }
            }

            $ratingScore = ($tp->rating ?? 0) / 5.0;

            // Combined score weights: distance 70%, AI match 20%, rating 10%
            $combined = ($distanceScore * 0.7) + ($aiScore * 0.2) + ($ratingScore * 0.1);

            return [
                'id' => $tp->user?->id ?? null,
                'name' => $tp->user?->name ?? null,
                'specialty' => $tp->specialty,
                'rating' => $tp->rating,
                'distance_km' => round($tp->distance_km, 2),
                'latitude' => $tp->latitude,
                'longitude' => $tp->longitude,
                'ai_score' => round($aiScore, 3),
                'distance_score' => round($distanceScore, 3),
                'rating_score' => round($ratingScore, 3),
                'score' => round($combined, 4),
            ];
        })->sortByDesc('score')->values();

        return response()->json(['ok' => true, 'trainers' => $trainers]);
    }
}
