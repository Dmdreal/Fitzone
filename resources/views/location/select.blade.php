@extends('layouts.app')

@section('content')
<div class="container py-5">
    <h2>Select your location</h2>
    <p>Please choose your county or allow the browser to detect your current location.</p>

    @if($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <form id="location-form" method="POST" action="{{ route('client.location.nearby') }}">
        @csrf
        <div class="mb-3">
            <label for="county" class="form-label">County</label>
            <select name="county" id="county" class="form-select">
                <option value="">-- Select county --</option>
                @foreach($counties as $c)
                    <option value="{{ $c->name }}" @selected(old('county', $selectedCounty) === $c->name)>{{ $c->display_name }}</option>
                @endforeach
            </select>
            @if(! empty($detectedLocationText))
                <div class="form-text">Detected from registration: {{ $detectedLocationText }}</div>
            @endif
        </div>

        <input type="hidden" name="latitude" id="latitude" value="{{ $selectedLat ?? '' }}">
        <input type="hidden" name="longitude" id="longitude" value="{{ $selectedLng ?? '' }}">

        <div class="d-flex gap-2">
            <button type="button" id="use-location" class="btn btn-primary">Use my current location</button>
            <button type="submit" class="btn btn-success">Continue</button>
        </div>
    </form>

    <hr>
    <p class="text-muted small">You can change this later in your profile.</p>
</div>

@push('scripts')
<script>
document.getElementById('use-location').addEventListener('click', function () {
    if (! navigator.geolocation) {
        alert('Geolocation is not supported by your browser.');
        return;
    }

    this.disabled = true;
    this.textContent = 'Locating…';

    navigator.geolocation.getCurrentPosition(function (pos) {
        document.getElementById('latitude').value = pos.coords.latitude;
        document.getElementById('longitude').value = pos.coords.longitude;
        // submit via fetch to give fast feedback
        let token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (! token) {
            token = document.querySelector('input[name="_token"]')?.value;
        }

        if (! token) {
            alert('Missing CSRF token. Please refresh and try again.');
            document.getElementById('use-location').disabled = false;
            document.getElementById('use-location').textContent = 'Use my current location';
            return;
        }

        fetch('{{ route('client.location.nearby') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ latitude: pos.coords.latitude, longitude: pos.coords.longitude })
        }).then(r => r.json()).then(data => {
            if (data.success) {
                window.location = '{{ route('dashboard') }}';
            } else {
                alert('Could not find nearby trainers.');
                window.location.reload();
            }
        }).catch(err => {
            console.error(err);
            alert('Error finding location');
            window.location.reload();
        });
    }, function (err) {
        alert('Unable to determine location: ' + err.message);
        document.getElementById('use-location').disabled = false;
        document.getElementById('use-location').textContent = 'Use my current location';
    }, { enableHighAccuracy: true, timeout: 10000 });
});
</script>
@endpush

@endsection
