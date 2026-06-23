<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register - Fitzone</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; min-width: 320px; min-height: 100vh; display: grid; place-items: center; padding: 16px; background: #eef2f7; font-family: Inter, ui-sans-serif, system-ui, sans-serif; color: #0f172a; }
        .auth { width: min(720px, calc(100% - 28px)); background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 28px; box-shadow: 0 20px 50px rgba(15,23,42,.09); }
        h1 { margin: 0 0 6px; }
        p { color: #64748b; margin: 0 0 20px; }
        label { display: grid; gap: 7px; margin-bottom: 14px; font-weight: 800; font-size: 13px; }
        input, select, textarea { width: 100%; border: 1px solid #e2e8f0; border-radius: 7px; padding: 12px; font: inherit; }
        textarea { min-height: 92px; resize: vertical; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 240px), 1fr)); gap: 12px; }
        .panel { border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px; margin: 0 0 14px; background: #f8fafc; }
        .hidden { display: none; }
        button { border: 0; border-radius: 7px; padding: 12px 16px; background: #1263e6; color: #fff; font-weight: 900; cursor: pointer; text-align: center; }
        .row { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-top: 8px; flex-wrap: wrap; }
        .error { color: #b91c1c; background: #fee2e2; border-radius: 7px; padding: 10px 12px; margin-bottom: 14px; }
        @media (max-width: 430px) { .auth { width: 100%; padding: 20px; } h1 { font-size: 24px; } .row, button { width: 100%; } }
    </style>
</head>
<body>
    <form class="auth" method="POST" action="{{ route('register.store') }}">
        @csrf
        <h1>Create Fitzone Account</h1>
        <p>Choose client, trainer, or gym owner. Discovery details are saved during registration.</p>
        @if ($errors->any())
            <div class="error">{{ $errors->first() }}</div>
        @endif
        <label>Account Type
            <select name="role" data-role-select required>
                <option value="member" @selected(old('role', 'member') === 'member')>Client</option>
                <option value="trainer" @selected(old('role') === 'trainer')>Trainer</option>
                <option value="gym_owner" @selected(old('role') === 'gym_owner')>Gym Owner</option>
            </select>
        </label>
        <div class="grid">
            <label>Name <input name="name" value="{{ old('name') }}" required></label>
            <label>Email <input name="email" type="email" value="{{ old('email') }}" required></label>
            <label>Phone <input name="phone" value="{{ old('phone') }}" placeholder="+254..."></label>
            <label>Headline <input name="headline" value="{{ old('headline') }}" placeholder="Strength coach, gym in Westlands..."></label>
            <label>Password <input name="password" type="password" required></label>
            <label>Confirm Password <input name="password_confirmation" type="password" required></label>
        </div>
        <label>Bio <textarea name="bio" placeholder="Tell people what you offer or what you want from Fitzone.">{{ old('bio') }}</textarea></label>

        <section class="panel hidden" data-member-panel>
            <div class="grid">
                <label>Location <input id="reg-location" name="location" value="{{ old('location') }}" placeholder="Nairobi CBD, Westlands, Kilimani..."></label>
                <input type="hidden" id="reg-latitude" name="latitude" value="{{ old('latitude') }}">
                <input type="hidden" id="reg-longitude" name="longitude" value="{{ old('longitude') }}">
                <div style="display:flex;gap:8px;align-items:center;">
                    <button type="button" id="use-my-location" style="background:#10b981;padding:8px;border-radius:6px;color:#fff;border:0;">Use my current location</button>
                    <span id="reg-location-status" style="font-size:13px;color:#475569;margin-left:6px;"></span>
                </div>
                <label>County
                    <select id="reg-county" name="county_id">
                        <option value="">Select county</option>
                        @foreach(\App\Models\County::orderBy('name')->get() as $county)
                            <option value="{{ $county->id }}" @selected(old('county_id') == $county->id)>{{ $county->display_name }}</option>
                        @endforeach
                    </select>
                </label>
                <label>Nearby Locations <input name="nearby_locations" value="{{ old('nearby_locations') }}" placeholder="Parklands, Lavington, Ngara..."></label>
                <label>Age <input name="age" type="number" min="13" max="100" value="{{ old('age') }}"></label>
                <label>Gender
                    <select name="gender">
                        <option value="">Select gender</option>
                        <option value="male" @selected(old('gender') === 'male')>Male</option>
                        <option value="female" @selected(old('gender') === 'female')>Female</option>
                        <option value="other" @selected(old('gender') === 'other')>Other</option>
                    </select>
                </label>
                <label>Fitness Goal
                    <select name="fitness_goal">
                        <option value="">Choose a goal</option>
                        <option value="weight_loss" @selected(old('fitness_goal') === 'weight_loss')>Weight loss</option>
                        <option value="muscle_gain" @selected(old('fitness_goal') === 'muscle_gain')>Muscle gain</option>
                        <option value="maintenance" @selected(old('fitness_goal') === 'maintenance')>Maintenance</option>
                        <option value="healthier_lifestyle" @selected(old('fitness_goal') === 'healthier_lifestyle')>Healthier lifestyle</option>
                    </select>
                </label>
                <label>Experience Level
                    <select name="experience_level">
                        <option value="">Select level</option>
                        <option value="beginner" @selected(old('experience_level') === 'beginner')>Beginner</option>
                        <option value="intermediate" @selected(old('experience_level') === 'intermediate')>Intermediate</option>
                        <option value="advanced" @selected(old('experience_level') === 'advanced')>Advanced</option>
                    </select>
                </label>
                <label>Budget Range
                    <select name="budget_range">
                        <option value="">Select range</option>
                        <option value="< 5,000" @selected(old('budget_range') === '< 5,000')>Under KES 5,000</option>
                        <option value="5,000 - 12,000" @selected(old('budget_range') === '5,000 - 12,000')>KES 5,000 - 12,000</option>
                        <option value="12,000 - 25,000" @selected(old('budget_range') === '12,000 - 25,000')>KES 12,000 - 25,000</option>
                        <option value="> 25,000" @selected(old('budget_range') === '> 25,000')>Above KES 25,000</option>
                    </select>
                </label>
                <label>Diet Preference <input name="diet_preference" value="{{ old('diet_preference') }}" placeholder="Vegetarian, balanced, high-protein..."></label>
            </div>
        </section>

        <section class="panel hidden" data-trainer-panel>
            <div class="grid">
                <label>Location <input name="location" value="{{ old('location') }}" placeholder="Nairobi CBD, Westlands, Kilimani..."></label>
                <label>Nearby Locations <input name="nearby_locations" value="{{ old('nearby_locations') }}" placeholder="Parklands, Lavington, Ngara..."></label>
                <label>Specialty <input name="specialty" value="{{ old('specialty') }}" placeholder="Strength, yoga, weight loss..."></label>
                <label>Category <input name="category" value="{{ old('category') }}" placeholder="strength, wellness, boxing..."></label>
                <label>Experience Years <input name="experience_years" type="number" min="0" max="60" value="{{ old('experience_years') }}"></label>
            </div>
        </section>

        <section class="panel hidden" data-gym-panel>
            <div class="grid">
                <label>Location <input name="location" value="{{ old('location') }}" placeholder="Nairobi CBD, Westlands, Kilimani..."></label>
                <label>Nearby Locations <input name="nearby_locations" value="{{ old('nearby_locations') }}" placeholder="Parklands, Lavington, Ngara..."></label>
                <label>Gym Name <input name="gym_name" value="{{ old('gym_name') }}" placeholder="Fitzone Westlands"></label>
                <label>Gym Services <textarea name="gym_services" placeholder="Personal training, boxing, sauna, cafe, group classes...">{{ old('gym_services') }}</textarea></label>
            </div>
        </section>
        <div class="row">
            <button type="submit">Register</button>
            <a href="{{ route('login') }}">Back to login</a>
        </div>
    </form>
    <script>
        const roleSelect = document.querySelector('[data-role-select]');
        const memberPanel = document.querySelector('[data-member-panel]');
        const trainerPanel = document.querySelector('[data-trainer-panel]');
        const gymPanel = document.querySelector('[data-gym-panel]');

        function syncRolePanels() {
            const role = roleSelect.value;
            memberPanel.classList.toggle('hidden', role !== 'member');
            trainerPanel.classList.toggle('hidden', role !== 'trainer');
            gymPanel.classList.toggle('hidden', role !== 'gym_owner');
        }

        roleSelect.addEventListener('change', syncRolePanels);
        syncRolePanels();

        // County auto-detect: try to preselect county when user types a location
        const locationInput = document.getElementById('reg-location');
        const countySelect = document.getElementById('reg-county');
        if (locationInput && countySelect) {
            const counties = {
                @foreach(\App\Models\County::orderBy('name')->get() as $county)
                    "{{ strtolower($county->name) }}": {{ $county->id }},
                @endforeach
            };

            let detectTimeout = null;
            locationInput.addEventListener('input', () => {
                clearTimeout(detectTimeout);
                detectTimeout = setTimeout(() => {
                    const val = locationInput.value.toLowerCase();
                    for (const name in counties) {
                        if (name && val.includes(name)) {
                            countySelect.value = counties[name];
                            return;
                        }
                    }
                }, 450);
            });
        }

        // Geolocation button
        const useBtn = document.getElementById('use-my-location');
        const statusSpan = document.getElementById('reg-location-status');
        const latInput = document.getElementById('reg-latitude');
        const lngInput = document.getElementById('reg-longitude');

        if (useBtn) {
            useBtn.addEventListener('click', () => {
                if (! navigator.geolocation) {
                    statusSpan.textContent = 'Geolocation not supported';
                    return;
                }
                statusSpan.textContent = 'Locating…';
                navigator.geolocation.getCurrentPosition((pos) => {
                    const lat = pos.coords.latitude;
                    const lng = pos.coords.longitude;
                    latInput.value = lat;
                    lngInput.value = lng;
                    statusSpan.textContent = `Lat: ${lat.toFixed(4)}, Lng: ${lng.toFixed(4)}`;
                }, (err) => {
                    statusSpan.textContent = 'Unable to retrieve location';
                }, { enableHighAccuracy: true, timeout: 10000 });
            });
        }
    </script>
</body>
</html>
