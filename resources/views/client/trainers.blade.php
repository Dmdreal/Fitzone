@extends('layouts.app')

@section('title', 'Select Trainer - Fitzone')

@section('content')
<h1>Select a Trainer</h1>
<section class="card">
    <h2>Trainer Preview</h2>
    <p class="muted">Search trainers by name, specialty, location, or nearby areas. Use your goal and experience to find a better match.</p>
    <form method="GET" action="{{ route('client.trainers') }}" class="form-grid" style="margin-bottom:16px">
        @if ($package)
            <input type="hidden" name="package" value="{{ $package->id }}">
        @endif
        <label>Search <input name="q" value="{{ $search }}" placeholder="weight loss, strength, lifestyle, nutrition..."></label>
        <label style="align-self:end"><button class="btn" type="submit">Search Trainers</button></label>
    </form>
    <div class="grid three">
        @forelse ($trainers as $trainer)
            <article class="card" style="box-shadow:none">
                <div class="avatar" style="width:54px;height:54px;margin-bottom:12px">{{ strtoupper(substr($trainer->user->name, 0, 1)) }}</div>
                <h2>{{ $trainer->user->name }}</h2>
                <p><span class="badge green">{{ ucfirst($trainer->category) }}</span></p>
                <p class="muted">{{ $trainer->specialty }}</p>
                <p><strong>Location:</strong> {{ $trainer->town ?? $trainer->user->location ?? 'Not listed' }}</p>
                @if (! empty($trainer->distance_km))
                    <p><strong>Distance:</strong> {{ $trainer->distance_km }} km</p>
                @endif
                @if (! empty($trainer->latitude) && ! empty($trainer->longitude))
                    <p><a class="muted" href="https://www.google.com/maps/search/?api=1&query={{ $trainer->latitude }},{{ $trainer->longitude }}" target="_blank">View on map</a></p>
                @endif
                @if ($trainer->user->nearby_locations)
                    <p class="muted">Nearby: {{ $trainer->user->nearby_locations }}</p>
                @endif
                <p>Rating {{ $trainer->rating }} - {{ $trainer->experience_years }} years experience</p>
                @if ($package)
                    <a class="btn" href="{{ route('client.checkout', ['package' => $package->id, 'trainer' => $trainer->id]) }}"><span>+</span> Select Trainer</a>
                @elseif ($trainer->preferredPackage)
                    <a class="btn" href="{{ route('client.checkout', ['package' => $trainer->preferredPackage->id, 'trainer' => $trainer->id]) }}"><span>+</span> Select Trainer and package</a>
                @else
                    <a class="btn" href="{{ route('client.packages', ['trainer' => $trainer->id]) }}"><span>+</span> Choose package for this trainer</a>
                @endif
                <details style="margin-top:12px">
                    <summary class="btn ghost" style="cursor:pointer">Book / Review / Report</summary>
                    <div class="grid" style="margin-top:12px">
                        <form method="POST" action="{{ route('client.bookings.store') }}" class="form-grid">
                            @csrf
                            <input type="hidden" name="target_user_id" value="{{ $trainer->user_id }}">
                            <input type="hidden" name="target_type" value="trainer">
                            <label>Booking Time <input type="datetime-local" name="scheduled_at"></label>
                            <label>Notes <input name="notes" placeholder="Session goal or preferred time"></label>
                            <label style="align-self:end"><button class="btn" type="submit">Request Booking</button></label>
                        </form>
                        <form method="POST" action="{{ route('client.reviews.store') }}" class="form-grid">
                            @csrf
                            <input type="hidden" name="target_user_id" value="{{ $trainer->user_id }}">
                            <input type="hidden" name="target_type" value="trainer">
                            <label>Rating
                                <select name="rating" required>
                                    <option value="5">5 - Excellent</option>
                                    <option value="4">4 - Good</option>
                                    <option value="3">3 - Okay</option>
                                    <option value="2">2 - Poor</option>
                                    <option value="1">1 - Bad</option>
                                </select>
                            </label>
                            <label>Review <input name="body" placeholder="Share your experience"></label>
                            <label style="align-self:end"><button class="btn ghost" type="submit">Submit Review</button></label>
                        </form>
                        <form method="POST" action="{{ route('client.complaints.store') }}" class="form-grid">
                            @csrf
                            <input type="hidden" name="target_user_id" value="{{ $trainer->user_id }}">
                            <input type="hidden" name="target_type" value="trainer">
                            <label>Subject <input name="subject" placeholder="What happened?" required></label>
                            <label>Complaint <input name="body" placeholder="Details for admin review" required></label>
                            <label style="align-self:end"><button class="btn ghost" type="submit">Send Complaint</button></label>
                        </form>
                    </div>
                </details>
            </article>
        @empty
            <article class="card" style="box-shadow:none">
                <h2>No trainers found</h2>
                <p class="muted">Try a trainer name, location, nearby area, or training specialty.</p>
            </article>
        @endforelse
    </div>
    <div style="margin-top:18px">
        <h3>Map</h3>
        <div id="nearby-map" style="width:100%;height:380px;border:1px solid #e5e7eb;border-radius:6px"></div>
        <div id="nearby-list" style="margin-top:12px"></div>
    </div>
    <div class="actions" style="justify-content:flex-start">
        @if ($package)
            <a class="btn ghost" href="{{ route('client.checkout', ['package' => $package->id]) }}">Continue Without Trainer</a>
        @else
            <a class="btn" href="{{ route('client.packages') }}">Choose Package First</a>
        @endif
    </div>
</section>
@endsection

@push('scripts')
<script>
    let map, markers = [];
    function initNearbyMap() {
        const mapEl = document.getElementById('nearby-map');
        if (!mapEl) return;
        const lat = parseFloat('{{ request()->get('lat', '-1.2921') }}');
        const lng = parseFloat('{{ request()->get('lng', '36.8219') }}');
        map = new google.maps.Map(mapEl, { center: { lat, lng }, zoom: 13 });
    }

    function clearMarkers() {
        markers.forEach(m => m.setMap(null));
        markers = [];
    }

    function fetchNearby() {
        const params = new URLSearchParams({
            lat: document.querySelector('input[name="lat"]')?.value || -1.2921,
            lng: document.querySelector('input[name="lng"]')?.value || 36.8219,
            radius_km: 10,
            goal: document.querySelector('input[name="q"]')?.value || ''
        });

        fetch(`/api/trainers/nearby?${params.toString()}`)
            .then(r => r.json())
            .then(data => {
                if (!data.ok) return;
                clearMarkers();
                const list = document.getElementById('nearby-list');
                list.innerHTML = '';
                data.trainers.forEach(t => {
                    const marker = new google.maps.Marker({ position: { lat: parseFloat(t.latitude), lng: parseFloat(t.longitude) }, map, title: t.name });
                    markers.push(marker);
                    const item = document.createElement('div');
                    item.className = 'card';
                    item.style.marginBottom = '8px';
                    item.innerHTML = `<strong>${t.name}</strong> <div class="muted">${t.specialty} — ${t.distance_km} km — score ${t.score}</div>`;
                    item.addEventListener('click', () => { map.panTo({ lat: parseFloat(t.latitude), lng: parseFloat(t.longitude) }); map.setZoom(15); });
                    list.appendChild(item);
                });
            }).catch(console.error);
    }

    document.addEventListener('DOMContentLoaded', () => {
        // load maps script if not loaded
        initNearbyMap();
        fetchNearby();
    });
</script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') }}&libraries=places&callback=initNearbyMap"></script>
@endpush
