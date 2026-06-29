<?php

namespace App\Http\Controllers;

use App\Models\TrainerProfile;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Closure;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors([
                'email' => 'the details provided are already  being used by another user.',
            ])->onlyInput('email');
        }

        if (Auth::user()->status !== 'active') {
            Auth::logout();

            return back()->withErrors([
                'email' => 'This account is not active.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->intended(route($this->homeRoute()));
    }

    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', Rule::in(['member', 'trainer', 'gym_owner'])],
            'phone' => ['nullable', 'string', 'max:40'],
            'headline' => ['nullable', 'string', 'max:120'],
            'bio' => ['nullable', 'string', 'max:1000'],
            // allow location to be optional at signup; trainers can add finer details later
            'location' => ['nullable', 'string', 'max:120'],
            'nearby_locations' => ['nullable', 'string', 'max:1000'],
            'age' => ['required_if:role,member', 'nullable', 'integer', 'min:13', 'max:100'],
            'gender' => ['required_if:role,member', 'nullable', 'string', 'max:20'],
            'fitness_goal' => ['required_if:role,member', 'nullable', 'string', 'max:120'],
            'experience_level' => ['required_if:role,member', 'nullable', 'string', 'max:80'],
            'budget_range' => ['required_if:role,member', 'nullable', 'string', 'max:80'],
            'diet_preference' => ['required_if:role,member', 'nullable', 'string', 'max:120'],
            // make trainer profile fields optional during signup to avoid blocking registration
            'specialty' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:80'],
            'experience_years' => ['nullable', 'integer', 'min:0', 'max:60'],
            'county_id' => ['nullable', 'exists:counties,id'],
            'town' => ['nullable', 'string', 'max:120'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'gym_name' => ['required_if:role,gym_owner', 'nullable', 'string', 'max:255'],
            'gym_services' => ['required_if:role,gym_owner', 'nullable', 'string', 'max:1000'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => $data['role'],
            'status' => 'active',
            'phone' => $data['phone'] ?? null,
            'headline' => $data['headline'] ?? null,
            'bio' => $data['bio'] ?? null,
            'location' => (isset($data['latitude']) && isset($data['longitude'])) ? json_encode(['label' => $data['location'] ?? null, 'latitude' => $data['latitude'], 'longitude' => $data['longitude']]) : ($data['location'] ?? null),
            'county_id' => $data['county_id'] ?? null,
            'nearby_locations' => $data['nearby_locations'] ?? null,
            'age' => $data['age'] ?? null,
            'gender' => $data['gender'] ?? null,
            'fitness_goal' => $data['fitness_goal'] ?? null,
            'experience_level' => $data['experience_level'] ?? null,
            'budget_range' => $data['budget_range'] ?? null,
            'diet_preference' => $data['diet_preference'] ?? null,
            'gym_name' => $data['gym_name'] ?? null,
            'gym_services' => $data['gym_services'] ?? null,
            'verification_status' => 'pending',
        ]);

        if ($user->role === 'member') {
            $user->ensureMemberIdentity();
        }

        // If client provided coords but not county, attempt reverse geocode to set county
        if ($user->role === 'member' && empty($user->county_id) && isset($data['latitude']) && isset($data['longitude'])) {
            $locationService = app(\App\Services\LocationService::class);
            $res = $locationService->reverseGeocode((float) $data['latitude'], (float) $data['longitude']);
            if (! empty($res['county'])) {
                $county = \App\Models\County::searchByName($res['county'])->first();
                if ($county) {
                    $user->county_id = $county->id;
                    $user->save();
                }
            }
        }

        if ($user->role === 'trainer') {
            TrainerProfile::create([
                'user_id' => $user->id,
                    'specialty' => $data['specialty'] ?? null,
                    'category' => $data['category'] ?? null,
                    'experience_years' => $data['experience_years'] ?? null,
                'rating' => 5,
                'bio' => $data['bio'] ?? null,
                'county_id' => $data['county_id'] ?? null,
                'town' => $data['town'] ?? null,
                'latitude' => $data['latitude'] ?? null,
                'longitude' => $data['longitude'] ?? null,
            ]);

            $profile = $user->trainerProfile()->first();

            if ($profile && empty($profile->county_id) && isset($data['latitude']) && isset($data['longitude'])) {
                $locationService = app(\App\Services\LocationService::class);
                $res = $locationService->reverseGeocode((float) $data['latitude'], (float) $data['longitude']);
                if (! empty($res['county'])) {
                    $county = \App\Models\County::searchByName($res['county'])->first();
                    if ($county) {
                        $profile->county_id = $county->id;
                        $user->county_id = $county->id;
                    }
                }
            }

            // attempt geocoding if lat/lng missing
            if ($profile && (empty($profile->latitude) || empty($profile->longitude))) {
                $locationService = app(\App\Services\LocationService::class);
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
                if (! empty($addressParts)) {
                    $coords = $locationService->geocode(implode(', ', $addressParts));
                    if ($coords) {
                        $profile->latitude = $coords['lat'];
                        $profile->longitude = $coords['lng'];
                    }
                }
            }

            if ($profile) {
                $profile->save();
                $user->save();
            }
        }

        Auth::login($user);

        if ($user->role === 'member') {
            $request->session()->put('needs_location_setup', true);
        }

        return redirect()->route($this->homeRoute($user));
    }

    public function loginWithGoogle(Request $request): JsonResponse
    {
        $data = $request->validate([
            'id_token' => ['required', 'string'],
        ]);

        $response = Http::asForm()->post('https://oauth2.googleapis.com/tokeninfo', [
            'id_token' => $data['id_token'],
        ]);

        if (! $response->successful()) {
            return response()->json(['message' => 'Unable to verify Google login token.'], 422);
        }

        $payload = $response->json();

        if (! in_array($payload['iss'] ?? '', ['https://accounts.google.com', 'accounts.google.com'], true)) {
            return response()->json(['message' => 'Invalid Google issuer.'], 422);
        }

        if (! empty($clientId = config('services.firebase.client_id')) && ($payload['aud'] ?? '') !== $clientId) {
            return response()->json(['message' => 'Google token audience mismatch.'], 422);
        }

        if (empty($payload['email']) || empty($payload['email_verified']) || $payload['email_verified'] !== 'true') {
            return response()->json(['message' => 'Google account must have a verified email.'], 422);
        }

        $email = $payload['email'];
        $user = User::where('email', $email)->first();

        if ($user && $user->status !== 'active') {
            return response()->json(['message' => 'This account is not active.'], 403);
        }

        if (! $user) {
            $user = User::create([
                'name' => $payload['name'] ?? explode('@', $email)[0],
                'email' => $email,
                'email_verified_at' => now(),
                'password' => bcrypt(Str::random(32)),
                'role' => 'member',
                'status' => 'active',
                'phone' => null,
                'headline' => null,
                'bio' => null,
                'location' => null,
                'county_id' => null,
                'nearby_locations' => null,
                'age' => null,
                'gender' => null,
                'fitness_goal' => null,
                'experience_level' => null,
                'budget_range' => null,
                'diet_preference' => null,
                'gym_name' => null,
                'gym_services' => null,
                'verification_status' => 'pending',
            ]);
        }

        Auth::login($user);

        return response()->json(['ok' => true, 'redirect' => route($this->homeRoute($user))]);
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    private function homeRoute(?User $user = null): string
    {
        $currentUser = $user ?? Auth::user();
        $role = $currentUser?->role;

        // Ensure members set location before proceeding to onboarding/dashboard
        if ($role === 'member') {
            if (session()->pull('needs_location_setup', false)) {
                return 'client.location.select';
            }

            if (! session()->has('completed_onboarding_' . $currentUser->id)) {
                return 'client.onboarding';
            }
        }

        return match ($role) {
            'admin' => 'admin.dashboard',
            'trainer' => 'trainer.dashboard',
            'gym_owner' => 'gym-owner.dashboard',
            'cafe' => 'cafe.dashboard',
            default => 'client.dashboard',
        };
    }
}
