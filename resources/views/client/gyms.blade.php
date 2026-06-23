@extends('layouts.app')

@section('title', 'Find Gyms - Fitzone')

@section('content')
<h1>Find Gyms</h1>

<section class="card">
    <h2>Gym Discovery</h2>
    <p class="muted">Search gyms by gym name, services, location, or nearby areas.</p>
    <form method="GET" action="{{ route('client.gyms') }}" class="form-grid" style="margin-bottom:16px">
        <label>Search <input name="q" value="{{ $search }}" placeholder="CBD, Kilimani, boxing, sauna..."></label>
        <label style="align-self:end"><button class="btn" type="submit">Search Gyms</button></label>
    </form>

    <div class="grid three">
        @forelse ($gyms as $gym)
            <article class="card" style="box-shadow:none">
                <div class="avatar" style="width:54px;height:54px;margin-bottom:12px">{{ strtoupper(substr($gym->gym_name ?: $gym->name, 0, 1)) }}</div>
                <h2>{{ $gym->gym_name ?: $gym->name }}</h2>
                <p><span class="badge amber">{{ ucfirst($gym->verification_status ?? 'pending') }}</span></p>
                <p><strong>Owner:</strong> {{ $gym->name }}</p>
                <p><strong>Location:</strong> {{ $gym->location ?: 'Not listed' }}</p>
                @if ($gym->nearby_locations)
                    <p class="muted">Nearby: {{ $gym->nearby_locations }}</p>
                @endif
                @if ($gym->gym_services)
                    <p>{{ $gym->gym_services }}</p>
                @else
                    <p class="muted">Services are not listed yet.</p>
                @endif
                @if ($gym->phone)
                    <p><strong>Phone:</strong> {{ $gym->phone }}</p>
                @endif
                <div style="margin:14px 0 0">
                    @if(isset($package))
                        <a class="btn" href="{{ route('client.checkout', ['package' => $package->id, 'gym' => $gym->id]) }}">Choose this gym with selected plan</a>
                    @elseif($gym->preferredPackage)
                        <a class="btn" href="{{ route('client.checkout', ['package' => $gym->preferredPackage->id, 'gym' => $gym->id]) }}">Select gym's preferred package</a>
                    @else
                        <a class="btn" href="{{ route('client.packages', ['gym' => $gym->id]) }}">Choose package for this gym</a>
                    @endif
                </div>
                <details style="margin-top:12px">
                    <summary class="btn ghost" style="cursor:pointer">Book / Review / Report</summary>
                    <div class="grid" style="margin-top:12px">
                        <form method="POST" action="{{ route('client.bookings.store') }}" class="form-grid">
                            @csrf
                            <input type="hidden" name="target_user_id" value="{{ $gym->id }}">
                            <input type="hidden" name="target_type" value="gym">
                            <label>Visit Time <input type="datetime-local" name="scheduled_at"></label>
                            <label>Notes <input name="notes" placeholder="Tour, class, or membership inquiry"></label>
                            <label style="align-self:end"><button class="btn" type="submit">Request Booking</button></label>
                        </form>
                        <form method="POST" action="{{ route('client.reviews.store') }}" class="form-grid">
                            @csrf
                            <input type="hidden" name="target_user_id" value="{{ $gym->id }}">
                            <input type="hidden" name="target_type" value="gym">
                            <label>Rating
                                <select name="rating" required>
                                    <option value="5">5 - Excellent</option>
                                    <option value="4">4 - Good</option>
                                    <option value="3">3 - Okay</option>
                                    <option value="2">2 - Poor</option>
                                    <option value="1">1 - Bad</option>
                                </select>
                            </label>
                            <label>Review <input name="body" placeholder="Share your gym experience"></label>
                            <label style="align-self:end"><button class="btn ghost" type="submit">Submit Review</button></label>
                        </form>
                        <form method="POST" action="{{ route('client.complaints.store') }}" class="form-grid">
                            @csrf
                            <input type="hidden" name="target_user_id" value="{{ $gym->id }}">
                            <input type="hidden" name="target_type" value="gym">
                            <label>Subject <input name="subject" placeholder="What happened?" required></label>
                            <label>Complaint <input name="body" placeholder="Details for admin review" required></label>
                            <label style="align-self:end"><button class="btn ghost" type="submit">Send Complaint</button></label>
                        </form>
                    </div>
                </details>
            </article>
        @empty
            <article class="card" style="box-shadow:none">
                <h2>No gyms found</h2>
                <p class="muted">Try a gym name, service, location, or nearby area.</p>
            </article>
        @endforelse
    </div>
</section>
@endsection
