@extends('layouts.app')

@section('title', 'Profile - Fitzone')

@section('content')
<h1>Profile</h1>

@if (session('status'))
    <section class="card" style="border-color:#86efac;margin-bottom:16px">{{ session('status') }}</section>
@endif

<div class="grid two">
    <section class="card">
        <h2>Your Profile</h2>
        <div style="display:flex;gap:16px;align-items:center;flex-wrap:wrap;margin-bottom:16px">
            @if ($user->profile_photo_url)
                <img class="profile-photo-xl" src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}">
            @else
                <div class="profile-photo-xl fallback">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
            @endif
            <div>
                <h2 style="margin-bottom:4px">{{ $user->name }}</h2>
                <p class="muted">{{ ucfirst($user->role) }} - {{ $user->headline ?? 'No headline yet' }}</p>
                @if ($user->profile_photo_path)
                    <form method="POST" action="{{ route('profile.photo.destroy') }}">
                        @csrf
                        @method('DELETE')
                        <button class="btn ghost" type="submit">Remove Photo</button>
                    </form>
                @endif
            </div>
        </div>

        @if ($errors->any())
            <p class="muted" style="color:var(--red)">{{ $errors->first() }}</p>
        @endif

        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="form-grid">
                <label>Name <input name="name" value="{{ old('name', $user->name) }}" required></label>
                <label>Email <input type="email" name="email" value="{{ old('email', $user->email) }}" required></label>
                <label>Phone <input name="phone" value="{{ old('phone', $user->phone) }}" placeholder="+254..."></label>
                <label>Headline <input name="headline" value="{{ old('headline', $user->headline) }}" placeholder="Strength trainer, premium member..."></label>
            </div>
            @if ($user->role === 'member')
                <div class="form-grid" style="margin-top:12px">
                    <label>Location <input name="location" value="{{ old('location', $user->location) }}" placeholder="Nairobi CBD, Westlands, Kilimani..."></label>
                    <label>Nearby Locations <input name="nearby_locations" value="{{ old('nearby_locations', $user->nearby_locations) }}" placeholder="Parklands, Lavington, Ngara..."></label>
                    <label>Age <input type="number" min="13" max="100" name="age" value="{{ old('age', $user->age) }}"></label>
                    <label>Gender
                        <select name="gender">
                            <option value="">Select gender</option>
                            <option value="male" @selected(old('gender', $user->gender) === 'male')>Male</option>
                            <option value="female" @selected(old('gender', $user->gender) === 'female')>Female</option>
                            <option value="other" @selected(old('gender', $user->gender) === 'other')>Other</option>
                        </select>
                    </label>
                    <label>Fitness Goal
                        <select name="fitness_goal">
                            <option value="">Choose a goal</option>
                            <option value="weight_loss" @selected(old('fitness_goal', $user->fitness_goal) === 'weight_loss')>Weight loss</option>
                            <option value="muscle_gain" @selected(old('fitness_goal', $user->fitness_goal) === 'muscle_gain')>Muscle gain</option>
                            <option value="maintenance" @selected(old('fitness_goal', $user->fitness_goal) === 'maintenance')>Maintenance</option>
                            <option value="healthier_lifestyle" @selected(old('fitness_goal', $user->fitness_goal) === 'healthier_lifestyle')>Healthier lifestyle</option>
                        </select>
                    </label>
                    <label>Experience Level
                        <select name="experience_level">
                            <option value="">Select level</option>
                            <option value="beginner" @selected(old('experience_level', $user->experience_level) === 'beginner')>Beginner</option>
                            <option value="intermediate" @selected(old('experience_level', $user->experience_level) === 'intermediate')>Intermediate</option>
                            <option value="advanced" @selected(old('experience_level', $user->experience_level) === 'advanced')>Advanced</option>
                        </select>
                    </label>
                    <label>Budget Range
                        <select name="budget_range">
                            <option value="">Select range</option>
                            <option value="< 5,000" @selected(old('budget_range', $user->budget_range) === '< 5,000')>Under KES 5,000</option>
                            <option value="5,000 - 12,000" @selected(old('budget_range', $user->budget_range) === '5,000 - 12,000')>KES 5,000 - 12,000</option>
                            <option value="12,000 - 25,000" @selected(old('budget_range', $user->budget_range) === '12,000 - 25,000')>KES 12,000 - 25,000</option>
                            <option value="> 25,000" @selected(old('budget_range', $user->budget_range) === '> 25,000')>Above KES 25,000</option>
                        </select>
                    </label>
                    <label>Diet Preference <input name="diet_preference" value="{{ old('diet_preference', $user->diet_preference) }}" placeholder="Vegetarian, balanced, high-protein..."></label>
                </div>
            @endif
            @if (in_array($user->role, ['trainer', 'gym_owner'], true))
                <div class="form-grid" style="margin-top:12px">
                    <label>Location <input name="location" value="{{ old('location', $user->location) }}" placeholder="Nairobi CBD, Westlands, Kilimani..."></label>
                    <label>Nearby Locations <input name="nearby_locations" value="{{ old('nearby_locations', $user->nearby_locations) }}" placeholder="Parklands, Lavington, Ngara..."></label>
                </div>
            @endif
            @if ($user->role === 'trainer')
                <div class="form-grid" style="margin-top:12px">
                    <label>Specialty <input name="specialty" value="{{ old('specialty', $user->trainerProfile?->specialty) }}" placeholder="Strength, yoga, weight loss..."></label>
                    <label>Category <input name="category" value="{{ old('category', $user->trainerProfile?->category) }}" placeholder="strength, wellness, boxing..."></label>
                    <label>Experience Years <input type="number" min="0" max="60" name="experience_years" value="{{ old('experience_years', $user->trainerProfile?->experience_years) }}"></label>
                </div>
                <div class="form-grid" style="margin-top:12px">
                    <label>County
                        <select name="county_id">
                            <option value="">Select county</option>
                            @foreach(\App\Models\County::orderBy('name')->get() as $county)
                                <option value="{{ $county->id }}" @selected(old('county_id', $user->trainerProfile?->county_id) == $county->id)>{{ $county->display_name }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label>Town <input name="town" value="{{ old('town', $user->trainerProfile?->town) }}"></label>
                    <label>Latitude <input id="latitude" name="latitude" value="{{ old('latitude', $user->trainerProfile?->latitude) }}"></label>
                    <label>Longitude <input id="longitude" name="longitude" value="{{ old('longitude', $user->trainerProfile?->longitude) }}"></label>
                </div>
                <div style="margin-top:12px">
                    <label>Map</label>
                    <div id="trainer-map" style="width:100%;height:300px;border:1px solid #e5e7eb;border-radius:6px"></div>
                    <div style="margin-top:8px;display:flex;gap:8px">
                        <input id="location-input" name="location_input" placeholder="Search for a place (Kilimani, Westlands...)" style="flex:1;padding:8px;border:1px solid #d1d5db;border-radius:4px" />
                        <button type="button" id="use-my-location" class="btn ghost">Use my GPS</button>
                    </div>
                </div>
            @endif
            @if ($user->role === 'gym_owner')
                <div class="form-grid" style="margin-top:12px">
                    <label>Gym Name <input name="gym_name" value="{{ old('gym_name', $user->gym_name) }}" placeholder="Fitzone Westlands"></label>
                    <label>Gym Services <textarea name="gym_services" placeholder="Personal training, boxing, sauna, cafe, group classes...">{{ old('gym_services', $user->gym_services) }}</textarea></label>
                </div>
            @endif
            <label style="margin-top:12px">Profile Photo <input type="file" name="profile_photo" accept="image/png,image/jpeg,image/webp"></label>
            <label style="margin-top:12px">Bio <textarea name="bio" placeholder="Write something about yourself...">{{ old('bio', $user->bio) }}</textarea></label>
            <div class="form-grid" style="margin-top:12px">
                <label>New Password <input type="password" name="password" autocomplete="new-password"></label>
                <label>Confirm Password <input type="password" name="password_confirmation" autocomplete="new-password"></label>
            </div>
            <div class="actions">
                <button class="btn" type="submit">Save Profile</button>
            </div>
        </form>
    </section>

    <section class="card">
        <h2>How It Appears</h2>
        <article class="message" style="width:100%">
            <div class="message-head">
                @if ($user->profile_photo_url)
                    <img class="message-avatar image" src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}">
                @else
                    <span class="message-avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                @endif
                <span class="message-meta">
                    <strong>{{ $user->name }}</strong>
                    <small>{{ ucfirst($user->role) }} - shown in chats</small>
                </span>
            </div>
            <div class="message-body">{{ $user->bio ?: 'Your bio and profile image help people recognize you, like on Telegram or LinkedIn.' }}</div>
        </article>

        @if ($user->role === 'member')
            <div style="height:16px"></div>
            <h2>Your Member QR</h2>
            <div style="display:grid;gap:14px">
                <img src="{{ route('members.qr.svg', $user) }}" alt="QR code for {{ $user->name }}" style="width:min(260px,100%);aspect-ratio:1;background:#fff;border:12px solid #fff;border-radius:8px;box-shadow:0 0 0 1px #e2e8f0">
                <div>
                    <p class="muted" style="overflow-wrap:anywhere;margin-bottom:10px">{{ route('members.qr.show', $user->qr_token) }}</p>
                    <p class="muted" style="margin-bottom:12px">Scan this with any phone camera to open your member details. Trainers can also use it for attendance.</p>
                    <div class="actions" style="justify-content:flex-start">
                        <a class="btn" href="{{ route('members.qr.svg', ['member' => $user, 'download' => 1]) }}">Download QR</a>
                        <a class="btn ghost" href="{{ route('members.qr.show', $user->qr_token) }}" target="_blank">Preview Card</a>
                    </div>
                </div>
            </div>
        @endif
    </section>
</div>
@endsection

@push('scripts')
<script>
    function initMap() {
        const mapEl = document.getElementById('trainer-map');
        if (!mapEl) return;

        const defaultLat = parseFloat(document.getElementById('latitude')?.value) || -1.2921;
        const defaultLng = parseFloat(document.getElementById('longitude')?.value) || 36.8219;

        const center = { lat: defaultLat, lng: defaultLng };
        const map = new google.maps.Map(mapEl, { center, zoom: 13 });
        const marker = new google.maps.Marker({ position: center, map, draggable: true });

        marker.addListener('dragend', () => {
            const pos = marker.getPosition();
            document.getElementById('latitude').value = pos.lat();
            document.getElementById('longitude').value = pos.lng();
        });

        // Autocomplete
        const input = document.getElementById('location-input');
        const ac = new google.maps.places.Autocomplete(input, { types: ['(regions)'] });
        ac.addListener('place_changed', () => {
            const place = ac.getPlace();
            if (!place.geometry) return;
            const lat = place.geometry.location.lat();
            const lng = place.geometry.location.lng();
            document.querySelector('input[name="town"]').value = place.address_components?.[0]?.long_name ?? document.querySelector('input[name="town"]').value;
            document.getElementById('latitude').value = lat;
            document.getElementById('longitude').value = lng;
            const newPos = { lat, lng };
            marker.setPosition(newPos);
            map.panTo(newPos);
        });

        document.getElementById('use-my-location')?.addEventListener('click', () => {
            if (!navigator.geolocation) return alert('Geolocation not supported');
            navigator.geolocation.getCurrentPosition(({ coords }) => {
                const lat = coords.latitude;
                const lng = coords.longitude;
                document.getElementById('latitude').value = lat;
                document.getElementById('longitude').value = lng;
                const pos = { lat, lng };
                marker.setPosition(pos);
                map.panTo(pos);
            }, (err) => alert('Unable to get location: ' + err.message));
        });
    }
</script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') }}&libraries=places&callback=initMap"></script>
@endpush
