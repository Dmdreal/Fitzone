<?php

namespace App\Http\Controllers;

use App\Models\MembershipPackage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Closure;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        $request->user()->ensureMemberIdentity();

        return view('profile.edit', [
            'user' => $request->user(),
            'packages' => MembershipPackage::visible()->get(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $locationService = app(\App\Services\LocationService::class);
        $user = $request->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:40'],
            'headline' => ['nullable', 'string', 'max:120'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'location' => [
                Rule::requiredIf(fn () => in_array($user->role, ['trainer', 'gym_owner'])),
                'nullable',
                'string',
                'max:120',
            ],
            'nearby_locations' => ['nullable', 'string', 'max:1000'],
            'age' => ['nullable', 'integer', 'min:13', 'max:100'],
            'gender' => ['nullable', 'string', 'max:20'],
            'fitness_goal' => ['nullable', 'string', 'max:120'],
            'experience_level' => ['nullable', 'string', 'max:80'],
            'budget_range' => ['nullable', 'string', 'max:80'],
            'diet_preference' => ['nullable', 'string', 'max:120'],
            'gym_name' => ['nullable', 'string', 'max:255'],
            'gym_services' => ['nullable', 'string', 'max:1000'],
            'specialty' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:80'],
            'experience_years' => ['nullable', 'integer', 'min:0', 'max:60'],
            'county_id' => ['nullable', 'exists:counties,id'],
            'town' => ['nullable', 'string', 'max:120'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'preferred_package_id' => ['nullable', 'exists:membership_packages,id'],
            'preferred_rate' => ['nullable', 'numeric', 'min:0'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        if ($request->hasFile('profile_photo')) {
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }

            $data['profile_photo_path'] = $request->file('profile_photo')->store('profile-photos', 'public');
        }

        if (empty($data['password'])) {
            unset($data['password']);
        }

        unset($data['profile_photo']);

        $trainerData = [
            'specialty' => $data['specialty'] ?? null,
            'category' => $data['category'] ?? null,
            'experience_years' => $data['experience_years'] ?? null,
            'preferred_package_id' => $data['preferred_package_id'] ?? null,
            'preferred_rate' => $data['preferred_rate'] ?? null,
            'county_id' => $data['county_id'] ?? null,
            'town' => $data['town'] ?? null,
            'latitude' => $data['latitude'] ?? null,
            'longitude' => $data['longitude'] ?? null,
        ];

        unset($data['specialty'], $data['category'], $data['experience_years'], $data['preferred_package_id'], $data['preferred_rate']);

        $user->update($data);

        if ($user->role === 'trainer') {
            $profile = $user->trainerProfile()->firstOrNew(['user_id' => $user->id]);
            $profile->fill(array_filter([
                'specialty' => $trainerData['specialty'] ?: $profile->specialty ?: 'General fitness',
                'category' => $trainerData['category'] ?: $profile->category ?: 'fitness',
                'experience_years' => $trainerData['experience_years'] ?? $profile->experience_years ?? 0,
                'preferred_package_id' => $trainerData['preferred_package_id'] ?? $profile->preferred_package_id,
                'preferred_rate' => $trainerData['preferred_rate'] ?? $profile->preferred_rate,
                'bio' => $user->bio,
            ], fn ($value) => $value !== null));
            $profile->save();

            // Auto-geocode if coordinates missing but we have a town/county or user location
            $shouldGeocode = empty($profile->latitude) || empty($profile->longitude);
            $addressParts = [];
            if ($profile->town) {
                $addressParts[] = $profile->town;
            }
            if ($profile->county_id) {
                $county = \App\Models\County::find($profile->county_id);
                if ($county) {
                    $addressParts[] = $county->name;
                }
            }
            if (empty($addressParts) && $user->location) {
                $addressParts[] = $user->location;
            }

            if ($shouldGeocode && ! empty($addressParts)) {
                $coords = $locationService->geocode(implode(', ', $addressParts));
                if ($coords) {
                    $profile->latitude = $coords['lat'];
                    $profile->longitude = $coords['lng'];
                    $profile->save();
                }
            }
        }

        return back()->with('status', 'Profile updated successfully.');
    }

    public function destroyPhoto(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
            $user->update(['profile_photo_path' => null]);
        }

        return back()->with('status', 'Profile photo removed.');
    }
}
