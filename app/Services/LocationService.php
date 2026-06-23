<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class LocationService
{
    /**
     * Geocode a county name to coordinates using Google Maps API
     * Falls back to hard-coded Kenya county centers if API fails or key missing
     */
    public function geocodeCounty(string $countyName): ?array
    {
        $key = config('services.google.maps_api_key') ?? env('GOOGLE_MAPS_API_KEY');

        if ($key) {
            try {
                $resp = Http::timeout(5)->get('https://maps.googleapis.com/maps/api/geocode/json', [
                    'address' => $countyName . ', Kenya',
                    'key' => $key,
                ]);

                if ($resp->ok()) {
                    $body = $resp->json();
                    if (! empty($body['results'][0]['geometry']['location'])) {
                        return [
                            'lat' => $body['results'][0]['geometry']['location']['lat'],
                            'lng' => $body['results'][0]['geometry']['location']['lng'],
                        ];
                    }
                }
            } catch (\Exception $e) {
                \Log::warning('Google Maps geocoding failed: ' . $e->getMessage());
            }
        }

        // Fallback: hard-coded Kenya county centers
        $centers = $this->getCountyCenters();

        $searchKey = strtolower(trim($countyName));
        foreach ($centers as $k => $v) {
            if (strpos($searchKey, $k) !== false || strpos($k, $searchKey) !== false) {
                return $v;
            }
        }

        return null;
    }

    private function getCountyCenters(): array
    {
        return [
            'mombasa' => ['lat' => -4.05466, 'lng' => 39.66347],
            'kwale' => ['lat' => -4.1697, 'lng' => 39.4790],
            'kilifi' => ['lat' => -3.6333, 'lng' => 39.8500],
            'tana river' => ['lat' => -2.0333, 'lng' => 40.1000],
            'lamu' => ['lat' => -2.2717, 'lng' => 40.9097],
            'taita taveta' => ['lat' => -3.3500, 'lng' => 38.3333],
            'taita-taveta' => ['lat' => -3.3500, 'lng' => 38.3333],
            'garissa' => ['lat' => -0.4530, 'lng' => 39.6460],
            'wajir' => ['lat' => 1.7500, 'lng' => 40.0667],
            'mandera' => ['lat' => 3.9378, 'lng' => 41.8560],
            'marsabit' => ['lat' => 2.3333, 'lng' => 37.9833],
            'isiolo' => ['lat' => 0.3515, 'lng' => 37.5868],
            'meru' => ['lat' => 0.0500, 'lng' => 37.6500],
            'tharaka nithi' => ['lat' => 0.1833, 'lng' => 37.6500],
            'tharaka-nithi' => ['lat' => 0.1833, 'lng' => 37.6500],
            'embu' => ['lat' => -0.5333, 'lng' => 37.4500],
            'kitui' => ['lat' => -1.3667, 'lng' => 38.0167],
            'machakos' => ['lat' => -1.5167, 'lng' => 37.2667],
            'makueni' => ['lat' => -1.8000, 'lng' => 37.7000],
            'nyandarua' => ['lat' => -0.4167, 'lng' => 36.0667],
            'nyeri' => ['lat' => -0.4167, 'lng' => 36.9667],
            'kirinyaga' => ['lat' => -0.6833, 'lng' => 37.2833],
            'murang\'a' => ['lat' => -0.6833, 'lng' => 37.1000],
            'muranga' => ['lat' => -0.6833, 'lng' => 37.1000],
            'kiambu' => ['lat' => -1.15000, 'lng' => 36.83333],
            'turkana' => ['lat' => 3.3720, 'lng' => 35.5568],
            'west pokot' => ['lat' => 1.1053, 'lng' => 35.0156],
            'samburu' => ['lat' => 0.5345, 'lng' => 37.5286],
            'trans nzoia' => ['lat' => 1.0333, 'lng' => 35.0100],
            'trans-nzoia' => ['lat' => 1.0333, 'lng' => 35.0100],
            'uasin gishu' => ['lat' => 0.5000, 'lng' => 35.2500],
            'elgeyo-marakwet' => ['lat' => 0.5000, 'lng' => 35.5000],
            'elgeyo marakwet' => ['lat' => 0.5000, 'lng' => 35.5000],
            'nandi' => ['lat' => 0.1000, 'lng' => 35.0000],
            'baringo' => ['lat' => 0.0000, 'lng' => 36.0000],
            'laikipia' => ['lat' => 0.4167, 'lng' => 36.3667],
            'nakuru' => ['lat' => -0.3031, 'lng' => 36.0800],
            'narok' => ['lat' => -1.0833, 'lng' => 35.2833],
            'kajiado' => ['lat' => -1.8500, 'lng' => 36.7833],
            'kericho' => ['lat' => -0.3612, 'lng' => 35.2803],
            'bomet' => ['lat' => -0.7833, 'lng' => 35.3667],
            'kakamega' => ['lat' => 0.2825, 'lng' => 34.7519],
            'vihiga' => ['lat' => 0.0667, 'lng' => 34.7333],
            'bungoma' => ['lat' => 0.5667, 'lng' => 34.5667],
            'busia' => ['lat' => 0.4574, 'lng' => 34.1040],
            'siaya' => ['lat' => 0.0583, 'lng' => 34.2856],
            'kisumu' => ['lat' => -0.0917, 'lng' => 34.7680],
            'homa bay' => ['lat' => -0.5344, 'lng' => 34.4587],
            'migori' => ['lat' => -1.0653, 'lng' => 34.4736],
            'kisii' => ['lat' => -0.6833, 'lng' => 34.7667],
            'nyamira' => ['lat' => -0.5333, 'lng' => 35.0833],
            'nairobi' => ['lat' => -1.28333, 'lng' => 36.81667],
        ];
    }

    /**
     * Generic address geocoding (used by trainer profile creation)
     */
    public function geocode(string $address): ?array
    {
        $key = config('services.google.maps_api_key') ?? env('GOOGLE_MAPS_API_KEY');
        
        if (! $key) {
            return null;
        }

        try {
            $response = Http::timeout(5)->get('https://maps.googleapis.com/maps/api/geocode/json', [
                'address' => $address,
                'key' => $key,
            ]);

            if (! $response->successful()) {
                return null;
            }

            $data = $response->json();
            $loc = $data['results'][0]['geometry']['location'] ?? null;
            if (! $loc) {
                return null;
            }

            return ['lat' => $loc['lat'], 'lng' => $loc['lng']];
        } catch (\Exception $e) {
            \Log::warning('Google Maps geocoding failed: ' . $e->getMessage());
            return null;
        }
    }

    public function reverseGeocode(float $lat, float $lng): ?array
    {
        $key = config('services.google.maps_api_key') ?? env('GOOGLE_MAPS_API_KEY');
        if (! $key) {
            return null;
        }

        try {
            $response = Http::timeout(5)->get('https://maps.googleapis.com/maps/api/geocode/json', [
                'latlng' => "$lat,$lng",
                'key' => $key,
            ]);

            if (! $response->successful()) {
                return null;
            }

            $data = $response->json();
            if (empty($data['results'])) {
                return null;
            }

            $address = $data['results'][0]['formatted_address'] ?? null;
            $county = null;
            $components = $data['results'][0]['address_components'] ?? [];
            foreach ($components as $component) {
                if (in_array('administrative_area_level_1', $component['types'] ?? [], true)) {
                    $county = $component['long_name'];
                    break;
                }
            }

            return ['address' => $address, 'county' => $county];
        } catch (\Exception $e) {
            \Log::warning('Google Maps reverse geocoding failed: ' . $e->getMessage());
            return null;
        }
    }

    public function detectCountyFromLocation(string $location): ?string
    {
        $searchKey = strtolower($location);
        $counties = array_keys($this->getCountyCenters());

        foreach ($counties as $county) {
            if (strpos($searchKey, $county) !== false) {
                return ucwords($county);
            }
        }

        return null;
    }

    /**
     * Calculate distance between two coordinates using Haversine formula
     */
    public function distanceBetween(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371; // km

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) * sin($dLat / 2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Find nearby trainers using Haversine formula
     */
    public function findNearbyTrainers(float $lat, float $lng, float $radiusKm = 10)
    {
        // Haversine formula: distance = 6371 * acos(cos(lat1) * cos(lat2) * cos(lng2 - lng1) + sin(lat1) * sin(lat2))
        $haversine = '(6371 * acos( cos( radians(?) ) * cos( radians(latitude) ) * cos( radians(longitude) - radians(?) ) + sin( radians(?) ) * sin( radians(latitude) ) ) )';

        $results = DB::table('trainer_profiles')
            ->leftJoin('users', 'users.id', '=', 'trainer_profiles.user_id')
            ->selectRaw('trainer_profiles.*, users.name as trainer_name, ' . $haversine . ' AS distance', [$lat, $lng, $lat])
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->having('distance', '<=', $radiusKm)
            ->orderBy('distance')
            ->limit(20)
            ->get();

        return $results;
    }
}
