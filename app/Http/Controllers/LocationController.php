<?php

namespace App\Http\Controllers;

use App\Models\County;
use App\Models\TrainerProfile;
use App\Services\LocationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LocationController extends Controller
{
    protected LocationService $locationService;

    public function __construct(LocationService $locationService)
    {
        $this->middleware('auth');
        $this->locationService = $locationService;
    }

    public function showSelectionForm()
    {
        $counties = County::orderBy('name')->get();
        $user = Auth::user();
        $selectedCounty = null;
        $selectedLat = null;
        $selectedLng = null;
        $detectedLocationText = null;

        if ($user && $user->location) {
            $locationData = json_decode($user->location, true);
            if (is_array($locationData)) {
                $selectedCounty = $locationData['county'] ?? null;
                $selectedLat = $locationData['latitude'] ?? null;
                $selectedLng = $locationData['longitude'] ?? null;
                $detectedLocationText = $locationData['county'] ?? null;
            } else {
                $detectedLocationText = $user->location;
                $selectedCounty = $this->locationService->detectCountyFromLocation($user->location);
            }
        }

        return view('location.select', compact('counties', 'selectedCounty', 'selectedLat', 'selectedLng', 'detectedLocationText'));
    }

    public function findNearbyTrainers(Request $request)
    {
        $request->validate([
            'county' => ['nullable', 'string', 'max:120'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
        ]);

        $lat = $request->input('latitude');
        $lng = $request->input('longitude');
        $county = $request->input('county');

        $user = Auth::user();
        $currentLocationText = $user?->location;

        if ((! $lat || ! $lng) && ! $county && $currentLocationText) {
            $jsonLocation = json_decode($currentLocationText, true);
            if (is_array($jsonLocation) && ! empty($jsonLocation['latitude']) && ! empty($jsonLocation['longitude'])) {
                $lat = $jsonLocation['latitude'];
                $lng = $jsonLocation['longitude'];
                $county = $jsonLocation['county'] ?? null;
            } else {
                $county = $this->locationService->detectCountyFromLocation($currentLocationText);
                if (! $county) {
                    $coords = $this->locationService->geocode($currentLocationText);
                    if ($coords) {
                        $lat = $coords['lat'];
                        $lng = $coords['lng'];
                    }
                }
            }
        }

        if (! $lat || ! $lng) {
            if (! $county) {
                return back()->withErrors(['county' => 'Please choose a county or allow location access.']);
            }

            $coords = $this->locationService->geocodeCounty($county);
            if (! $coords) {
                return back()->withErrors(['county' => 'Could not determine coordinates for that county.']);
            }

            $lat = $coords['lat'];
            $lng = $coords['lng'];
        }

        if (! $county && $lat && $lng) {
            $reverse = $this->locationService->reverseGeocode((float) $lat, (float) $lng);
            $county = $reverse['county'] ?? $reverse['address'] ?? null;
        }

        $radiusKm = (float) ($request->input('radius_km', 10));

        $trainers = $this->locationService->findNearbyTrainers($lat, $lng, $radiusKm);

        $locationValue = $county ?: $currentLocationText ?: sprintf('Lat %.6f, Lng %.6f', $lat, $lng);
        if ($user) {
            $user->forceFill(['location' => $locationValue])->save();
        }

        $suggestions = $trainers->map(function ($t) {
            return [
                'id' => $t->id,
                'name' => $t->trainer_name ?? null,
                'specialty' => $t->specialty ?? null,
                'distance_km' => round($t->distance, 2),
                'town' => $t->town ?? null,
            ];
        })->toArray();

        session(['nearby_trainers' => $suggestions, 'nearby_location_label' => $locationValue]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'trainers' => $suggestions]);
        }

        return redirect()->route('dashboard');
    }
}
